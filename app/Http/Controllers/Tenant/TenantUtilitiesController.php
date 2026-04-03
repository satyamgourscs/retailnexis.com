<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class TenantUtilitiesController extends Controller
{
    public function migrate(): never
    {
        Artisan::call('migrate');

        dd('migrated');
    }

    public function clearCaches(): never
    {
        Artisan::call('optimize:clear');
        cache()->forget('biller_list');
        cache()->forget('brand_list');
        cache()->forget('category_list');
        cache()->forget('coupon_list');
        cache()->forget('customer_list');
        cache()->forget('customer_group_list');
        cache()->forget('product_list');
        cache()->forget('product_list_with_variant');
        cache()->forget('warehouse_list');
        cache()->forget('table_list');
        cache()->forget('tax_list');
        cache()->forget('currency');
        cache()->forget('general_setting');
        cache()->forget('pos_setting');
        cache()->forget('user_role');
        cache()->forget('permissions');
        cache()->forget('role_has_permissions');
        cache()->forget('role_has_permissions_list');
        dd('cleared');
    }

    public function phpFileInfo(): void
    {
        if (app()->environment('production')) {
            abort(404);
        }

        phpinfo();
    }
}
