<?php



namespace App\Http\Controllers;



use App\Models\InvoiceSetting;

use App\Support\UpiUri;

use Auth;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Role;

use Intervention\Image\ImageManager;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;



class InvoiceSettingController extends Controller

{

    /**

     * Tenant id for filesystem scoping (SaaS). Safe when tenant() is null (single-DB installs).

     */

    private function tenantScopedId(): string

    {

        if (function_exists('tenant') && tenant()) {

            return (string) tenant('id');

        }



        return 'app';

    }



    /**

     * Validation rules for UPI ID on invoice settings forms.

     */

    private function upiIdValidationRules(): array

    {

        return [

            'upi_id' => ['nullable', 'string', 'max:50'],

        ];

    }



    private function upiIdValidationMessages(): array

    {

        return [

            'upi_id.max' => 'UPI ID may not be greater than 50 characters.',

            'upi_id.string' => 'UPI ID must be text.',

        ];

    }



    /**

     * Optional / nullable fields shared by store & update (avoids null surprises downstream).

     */

    private function invoiceSettingBaseValidationRules(): array

    {

        return array_merge([

            'prefix' => 'nullable|string|max:11',

            'numbering_type' => 'nullable|string|max:50',

            'start_number' => 'nullable|numeric',

            'footer_text' => 'nullable|string',

            'header_text' => 'nullable|string',

            'invoice_date_format' => 'nullable|string|max:100',

            'logo_height' => 'nullable|string|max:20',

            'logo_width' => 'nullable|string|max:20',

            'primary_color' => 'nullable|string|max:32',

            'number_of_digit' => 'nullable|string|max:10',

            'size' => 'nullable|string|max:20',

            'show_column' => 'nullable|array',

            'show_column.*' => 'nullable',

        ], $this->upiIdValidationRules());

    }



    /**

     * Persist UPI QR PNG under public/invoices, scoped by tenant + invoice_settings.id.

     * Removes previous file for this row when UPI changes or is cleared.

     */

    private function regenerateInvoiceUpiQrImage(InvoiceSetting $invoice): void

    {

        $schema = $invoice->getConnection()->getSchemaBuilder();

        $table = $invoice->getTable();

        if (! $schema->hasColumn($table, 'upi_id')) {

            return;

        }

        $upiId = $invoice->upi_id;

        $directory = public_path('invoices');

        if (! is_dir($directory)) {

            mkdir($directory, 0755, true);

        }

        $oldFile = $invoice->upi_qr_image ? basename((string) $invoice->upi_qr_image) : '';

        if ($upiId === null || $upiId === '') {

            if ($oldFile !== '' && preg_match('/^[A-Za-z0-9._-]+$/', $oldFile)) {

                $prevPath = $directory.DIRECTORY_SEPARATOR.$oldFile;

                if (is_file($prevPath)) {

                    @unlink($prevPath);

                }

            }

            if ($invoice->upi_qr_image !== null && $schema->hasColumn($table, 'upi_qr_image')) {

                $invoice->upi_qr_image = null;

                $invoice->save();

            }

            return;

        }

        $tenantKey = preg_replace('/[^A-Za-z0-9._-]/', '_', $this->tenantScopedId());

        $filename = $tenantKey.'_inv'.$invoice->id.'_upi.png';

        if ($oldFile !== '' && $oldFile !== $filename && preg_match('/^[A-Za-z0-9._-]+$/', $oldFile)) {

            $prevPath = $directory.DIRECTORY_SEPARATOR.$oldFile;

            if (is_file($prevPath)) {

                @unlink($prevPath);

            }

        }

        try {

            $payload = UpiUri::build((string) $upiId);

            if ($payload === '' || trim($payload) === '') {

                return;

            }

            $dns2d = app()->bound('DNS2D') ? app('DNS2D') : null;

            if ($dns2d === null) {

                Log::warning('InvoiceSetting UPI QR: DNS2D binding missing', ['invoice_setting_id' => $invoice->id]);

                return;

            }

            $pngB64 = $dns2d->getBarcodePNG((string) $payload, 'QRCODE,M', 3, 3);

            if ($pngB64 === false || $pngB64 === '') {

                Log::warning('InvoiceSetting UPI QR: barcode generation returned empty', ['invoice_setting_id' => $invoice->id]);

                return;

            }

            $binary = base64_decode((string) $pngB64, true);

            if ($binary === false || $binary === '') {

                Log::warning('InvoiceSetting UPI QR: invalid base64', ['invoice_setting_id' => $invoice->id]);

                return;

            }

            file_put_contents($directory.DIRECTORY_SEPARATOR.$filename, $binary);

            if (! $schema->hasColumn($table, 'upi_qr_image')) {

                return;

            }

            $invoice->upi_qr_image = $filename;

            $invoice->save();

        } catch (\Throwable $e) {

            Log::error('InvoiceSetting UPI QR generation failed', [

                'invoice_setting_id' => $invoice->id,

                'message' => $e->getMessage(),

            ]);

        }

    }



    /**

     * Display a listing of the resource.

     */

    public function index()

    {

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if (! $role->hasPermissionTo('invoice_setting')) {

            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));

        }



        $data['invoiceSettings'] = InvoiceSetting::all();



        return view('backend.setting.invoice_setting.index')->with($data);

    }



    /**

     * Show the form for creating a new resource.

     */

    public function create()

    {

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if (! $role->hasPermissionTo('invoice_create_edit_delete')) {

            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));

        }



        return view('backend.setting.invoice_setting.create');

    }



    /**

     * Store a newly created resource in storage.

     */

    public function store(Request $request)

    {

        if (! env('USER_VERIFIED')) {

            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));

        }



        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if (! $role->hasPermissionTo('invoice_create_edit_delete')) {

            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));

        }



        $request->validate(array_merge($this->invoiceSettingBaseValidationRules(), [

            'template_name' => 'required|string|max:255',

        ]), $this->upiIdValidationMessages());



        try {

            DB::beginTransaction();

            $data = $this->getRequestData($request);



            $data['status'] = $request->boolean('status') ? 1 : 0;

            $data['is_default'] = $request->boolean('is_default') ? 1 : 0;



            /** @var InvoiceSetting $invoice */

            $invoice = InvoiceSetting::query()->create($data);

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Invoice setting store failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);



            return redirect()->back()

                ->withErrors(['save' => 'Could not save invoice settings. Please try again.'])

                ->withInput();

        }



        try {

            $this->regenerateInvoiceUpiQrImage($invoice->fresh());

        } catch (\Throwable $e) {

            Log::warning('Invoice setting UPI QR regeneration failed after store', [

                'invoice_setting_id' => $invoice->id ?? null,

                'message' => $e->getMessage(),

            ]);

        }



        return redirect()->route('settings.invoice.index')->with('customMessage', 'Invoice setting stored successfully.');

    }



    /**

     * Show the form for editing the specified resource.

     */

    public function edit($id)

    {

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if (! $role->hasPermissionTo('invoice_create_edit_delete')) {

            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));

        }



        $invoice = InvoiceSetting::findOrFail($id);



        return view('backend.setting.invoice_setting.edit', compact('invoice'));

    }



    /**

     * Update the specified resource in storage.

     */

    public function update(Request $request, $id)

    {

        // List index uses AJAX (Set Default) — only that flow should short-circuit here.
        if ($request->ajax() && $request->filled('column')) {

            $this->changeStatus($request, $id);

            return response()->json(['success' => true]);

        }



        if (! env('USER_VERIFIED')) {

            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));

        }



        $request->validate(array_merge($this->invoiceSettingBaseValidationRules(), [

            'template_name' => 'required|string|max:255',

            'prefix' => 'required|string|max:11',

        ]), $this->upiIdValidationMessages());



        try {

            DB::beginTransaction();



            $invoice = InvoiceSetting::query()->findOrFail($id);

            $data = $this->getRequestData($request);



            $invoice->update($data);

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Invoice setting update failed', [

                'id' => $id,

                'message' => $e->getMessage(),

                'exception' => $e::class,

                'trace' => $e->getTraceAsString(),

                'request' => $request->except(['company_logo', 'preview_invoice', '_token', 'password']),

            ]);



            return redirect()->back()

                ->withErrors(['save' => 'Could not save invoice settings. Please try again.'])

                ->withInput();

        }



        try {

            $this->regenerateInvoiceUpiQrImage($invoice->fresh());

        } catch (\Throwable $e) {

            Log::warning('Invoice setting UPI QR regeneration failed after update', [

                'id' => $id,

                'message' => $e->getMessage(),

            ]);

        }



        return redirect()->back()->with('customMessage', 'Invoice setting saved successfully.');

    }



    /**

     * Remove the specified resource from storage.

     */

    public function destroy($id)

    {

        if (! env('USER_VERIFIED')) {

            return response()->json(['not_permitted' => __('db.This feature is disable for demo!')]);

        }



        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if (! $role->hasPermissionTo('invoice_create_edit_delete')) {

            return response()->json(['not_permitted' => __('db.Sorry! You are not allowed to access this module')]);



        }



        $invoice = InvoiceSetting::findOrFail($id);



        if ($invoice->is_default != 1) {

            $qr = $invoice->upi_qr_image ? basename((string) $invoice->upi_qr_image) : '';

            if ($qr !== '' && preg_match('/^[A-Za-z0-9._-]+$/', $qr)) {

                $qp = public_path('invoices'.DIRECTORY_SEPARATOR.$qr);

                if (is_file($qp)) {

                    @unlink($qp);

                }

            }

            $invoice->delete();



            return response()->json(['message' => 'Invoice deleted successfully', 'success' => true]);

        }



        return response()->json(['message' => 'Default invoice cannot be deleted', 'success' => false]);

    }



    public function getRequestData(Request $request): array

    {

        $checkboxFields = [

            'show_barcode',

            'show_qr_code',

            'show_description',

            'show_in_words',

            'active_primary_color',

            'show_warehouse_info',

            'show_bill_to_info',

            'show_footer_text',

            'show_biller_info',

            'show_paid_info',

            'show_payment_note',

            'show_ref_number',

            'active_date_format',

            'active_generat_settings',

            'active_logo_height_width',

            'hide_total_due',

        ];



        $showColumn = [];

        $columnInput = $request->input('show_column', []);

        if (! is_array($columnInput)) {

            $columnInput = [];

        }

        foreach ($checkboxFields as $field) {

            $showColumn[$field] = ! empty($columnInput[$field]) ? 1 : 0;

        }



        if ((int) $request->input('status', 0) === 1) {

            InvoiceSetting::query()->where('status', 1)->update(['status' => 0]);

        }



        if ((int) $request->input('is_default', 0) === 1) {

            InvoiceSetting::query()->where('is_default', 1)->update(['is_default' => 0]);

        }



        $scalarKeys = [

            'template_name',

            'prefix',

            'numbering_type',

            'start_number',

            'footer_text',

            'header_text',

            'invoice_date_format',

            'logo_height',

            'logo_width',

            'primary_color',

            'number_of_digit',

            'size',

        ];



        $data = [

            'show_column' => $showColumn,

        ];



        foreach ($scalarKeys as $key) {

            if (! $request->exists($key)) {

                continue;

            }

            $value = $request->input($key);

            if ($value === null) {

                $data[$key] = null;

                continue;

            }

            if (is_string($value)) {

                $trimmed = trim($value);

                $data[$key] = $trimmed === '' ? null : $trimmed;

                continue;

            }

            $data[$key] = $value;

        }



        if ($request->has('upi_id')) {

            $rawUpi = $request->input('upi_id');

            if ($rawUpi === null || $rawUpi === '') {

                $data['upi_id'] = null;

            } elseif (is_string($rawUpi)) {

                $trimmed = trim($rawUpi);

                $data['upi_id'] = $trimmed === '' ? null : mb_substr($trimmed, 0, 50);

            }

        }



        if ($request->hasFile('company_logo')) {

            try {

                $uploaded = $this->uploadInvoiceTemplate($request->company_logo);

                if ($uploaded) {

                    $data['company_logo'] = $uploaded;

                }

            } catch (\Throwable $e) {

                Log::warning('Invoice company_logo upload failed', [

                    'message' => $e->getMessage(),

                    'exception' => $e::class,

                ]);

            }

        }



        if ($request->hasFile('preview_invoice')) {

            try {

                $uploaded = $this->uploadInvoiceTemplate($request->preview_invoice);

                if ($uploaded) {

                    $data['preview_invoice'] = $uploaded;

                }

            } catch (\Throwable $e) {

                Log::warning('Invoice preview_invoice upload failed', [

                    'message' => $e->getMessage(),

                    'exception' => $e::class,

                ]);

            }

        }



        return $this->onlyFillableInvoiceData($data);

    }



    /**

     * Only pass attributes that exist on invoice_settings to avoid SQL / mass-assignment issues.

     */

    private function onlyFillableInvoiceData(array $data): array

    {

        $model = new InvoiceSetting;

        $fillable = $model->getFillable();

        $filtered = array_intersect_key($data, array_flip($fillable));

        $schema = $model->getConnection()->getSchemaBuilder();

        $table = $model->getTable();

        foreach (array_keys($filtered) as $key) {

            if (! $schema->hasColumn($table, $key)) {

                unset($filtered[$key]);

            }

        }



        return $filtered;

    }



    private function uploadInvoiceTemplate($request_image)

    {

        if (isset($request_image)) {

            $logo = $request_image;

            if ($logo) {

                $originalName = $logo->getClientOriginalName();

                $originalName = $originalName === null ? '' : (string) $originalName;

                $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));

                if ($ext === '' || ! preg_match('/^[a-z0-9]{1,8}$/', $ext)) {

                    $ext = 'png';

                }

                $imageName = date('Ymdhis').'.'.$ext;



                // Save original file first

                $logo->move(public_path('invoices/'), $imageName);



                $manager = new ImageManager(new GdDriver());

                $image = $manager->read(public_path('invoices/').$imageName);

                // Get original dimensions

                $originalWidth = $image->width();

                $originalHeight = $image->height();



                // Only resize if wider than 300px

                if ($originalWidth > 300) {

                    $newWidth = 300;

                    $newHeight = intval(($originalHeight / $originalWidth) * $newWidth);



                    // Resize explicitly

                    $image->resize($newWidth, $newHeight);

                }



                // Save resized image

                if (! file_exists(public_path('invoices/small')) && ! is_dir(public_path('invoices/small'))) {

                    mkdir(public_path('invoices/small'), 0755, true);

                }

                $image->save(public_path('invoices/small/'.$imageName), quality: 100);



                return $imageName;

            }

        }



        return null;

    }



    public function changeStatus($request, $id)

    {

        if ($request->column == 'status') {

            InvoiceSetting::query()->where('status', 1)->update(['status' => 0]);

            InvoiceSetting::query()->findOrFail($id)->update(['status' => 1]);



            return true;

        }



        if ($request->column == 'is_default') {

            InvoiceSetting::query()->where('is_default', 1)->update(['is_default' => 0]);

            InvoiceSetting::query()->findOrFail($id)->update(['is_default' => 1]);



            return true;

        }

    }

}


