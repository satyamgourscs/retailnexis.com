<?php



namespace App\Http\Requests\Sale;



use Illuminate\Foundation\Http\FormRequest;



class StoreSaleRequest extends FormRequest

{

    /**

     * Determine if the user is authorized to make this request.

     */

    public function authorize(): bool

    {

        return true;

    }



    public function rules(): array

    {

        $paymentStatusRule = $this->input('pos') ? 'nullable' : 'required';



        return [

            'reference_no'   => 'nullable|string|max:191|unique:sales,reference_no',

            'customer_id'    => 'required|exists:customers,id',

            'warehouse_id'   => 'required|exists:warehouses,id',

            'item'           => 'required|min:1',

            'sale_status'    => 'required',

            'payment_status' => $paymentStatusRule,

            'document'       => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',

        ];

    }

    

    public function messages(): array

    {

        return [

            'reference_no.unique'     => 'The reference number must be unique.',

            'customer_id.required'    => 'Please select a customer.',

            'warehouse_id.required'   => 'Please select a warehouse.',

            'item.required'           => 'Please add at least one item.',

            'sale_status.required'    => 'Sale status is required.',

            'payment_status.required' => 'Payment status is required.',

            'document.mimes'          => 'The document must be a file of type: jpg, jpeg, png, gif, pdf, csv, docx, xlsx, txt.',

        ];

    }



    /**

     * Multi-line payments: totals, cheque numbers per Cheque row, numeric paying amounts vs remaining due.

     */

    public function withValidator($validator): void

    {

        $validator->after(function (\Illuminate\Validation\Validator $v): void {

            $paid = $this->input('paid_amount');

            if (! is_array($paid)) {

                return;

            }



            $grand = (float) $this->input('grand_total', 0);

            $sum = 0.0;

            foreach ($paid as $amount) {

                $sum += (float) $amount;

            }

            if ($sum > $grand + 0.000001) {

                $v->errors()->add(

                    'paid_amount',

                    'Total amount applied to the sale cannot exceed the grand total.'

                );

            }



            $paidBy = $this->input('paid_by_id', []);

            if (! is_array($paidBy)) {

                $paidBy = [];

            }



            $chequeRaw = $this->input('cheque_no');

            $paying = $this->input('paying_amount');

            if (! is_array($paying)) {

                $paying = [];

            }



            $running = $grand;

            foreach ($paid as $i => $amount) {

                $p = (float) $amount;

                if ($p > 0 && $p > $running + 1e-5) {

                    $v->errors()->add(

                        'paid_amount',

                        'A payment row exceeds the remaining balance for this sale.'

                    );

                }

                if ($p > 0) {

                    $running -= $p;

                }



                $method = $paidBy[$i] ?? null;

                $methodInt = is_numeric($method) ? (int) $method : null;



                if ($p > 0 && $methodInt === 4) {

                    $cn = '';

                    if (is_array($chequeRaw)) {

                        $cn = isset($chequeRaw[$i]) ? trim((string) $chequeRaw[$i]) : '';

                    } elseif ($chequeRaw !== null && $chequeRaw !== '') {

                        $cn = trim((string) $chequeRaw);

                    }

                    if ($cn === '') {

                        $v->errors()->add(

                            'cheque_no.' . $i,

                            'Cheque number is required when payment method is Cheque.'

                        );

                    }

                }



                if ($p > 0) {

                    $payRow = $paying[$i] ?? null;

                    if ($payRow === null || $payRow === '') {

                        $v->errors()->add(

                            'paying_amount.' . $i,

                            'Received amount is required for each payment row with an amount.'

                        );

                    } elseif (! is_numeric($payRow)) {

                        $v->errors()->add(

                            'paying_amount.' . $i,

                            'Received amount must be numeric.'

                        );

                    } else {

                        $pr = (float) $payRow;

                        if ($pr + 1e-5 < $p) {

                            $v->errors()->add(

                                'paying_amount',

                                'Each payment row must have received amount greater than or equal to amount applied.'

                            );

                        }

                        $dueBefore = $grand;

                        for ($j = 0; $j < $i; $j++) {

                            $dueBefore -= (float) ($paid[$j] ?? 0);

                        }

                        if ($dueBefore < 0) {

                            $dueBefore = 0;

                        }

                        if ($pr > $dueBefore + 1e-5) {

                            $v->errors()->add(

                                'paying_amount.' . $i,

                                'Received amount cannot exceed the remaining amount due for this sale at this payment row.'

                            );

                        }

                    }

                }

            }

        });

    }

}

