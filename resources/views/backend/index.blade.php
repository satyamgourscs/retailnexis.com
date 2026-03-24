@extends('backend.layout.main')
@section('content')

@push('css')
<style>
.bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {width: auto;}
/* Dashboard welcome row (scoped) */
.dash-welcome-row { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem 1.5rem; padding: 1.25rem 0 1.5rem; margin-bottom: 0.25rem; border-bottom: 1px solid rgba(0,0,0,.06); }
@media (min-width: 768px) {
  .dash-welcome-row { align-items: center; }
}
.dash-welcome-heading { font-size: 1.5rem; font-weight: 600; color: #1e293b; letter-spacing: -0.02em; margin: 0; line-height: 1.3; text-transform: none; }
.dash-welcome-sub { font-size: 0.8125rem; color: #64748b; margin: 0.35rem 0 0; font-weight: 400; }
.dash-welcome-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1rem; }
.dash-welcome-filters .btn-defualt, .dash-welcome-filters .btn-default { border-radius: 10px; }
body.dark-mode .dash-welcome-row { border-bottom-color: rgba(255,255,255,.08); }
body.dark-mode .dash-welcome-heading { color: #e8eaef; }
body.dark-mode .dash-welcome-sub { color: #94a3b8; }
</style>
@endpush

    <x-success-message key="message" />
    <x-error-message key="not_permitted" />

    @php
        if ($general_setting->theme == 'default.css') {
            $color = '#733686';
            $color_rgba = 'rgba(115, 54, 134, 0.8)';
        } elseif ($general_setting->theme == 'green.css') {
            $color = '#2ecc71';
            $color_rgba = 'rgba(46, 204, 113, 0.8)';
        } elseif ($general_setting->theme == 'blue.css') {
            $color = '#3498db';
            $color_rgba = 'rgba(52, 152, 219, 0.8)';
        } elseif ($general_setting->theme == 'dark.css') {
            $color = '#34495e';
            $color_rgba = 'rgba(52, 73, 94, 0.8)';
        }
    @endphp
    <div class="row">

        <div class="container-fluid">
            @php
                $lims_warehouse_list = App\Models\Warehouse::where('is_active', true)->get();
            @endphp

            @if (!config('database.connections.saleprosaas_landlord') && \Auth::user()->role_id <= 2)
                @if (isset($versionUpgradeData['alert_version_upgrade_enable']) &&
                        $versionUpgradeData['alert_version_upgrade_enable'] == true)
                    <div id="alertSection" class="alert not-slide alert-primary alert-dismissible fade show" role="alert">
                        <p id="announce"><strong>Announce !!!</strong> A new version
                            {{ $versionUpgradeData['demo_version'] }} has been released. Please <i><b><a
                                        href="{{ route('new-release') }}">Click here</a></b></i> to check upgrade details.
                        </p>
                        <button type="button" id="closeButtonUpgrade" class="close" data-dismiss="alert"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            @endif
                <div class="col-md-12 mt-2 px-0">
                    @php
                        $user = Auth::user();
                        $displayName = null;
                        if (function_exists('tenancy') && tenancy()->initialized) {
                            $tenantCompanyName = tenant()?->company_name ?? '';
                            $displayName = is_string($tenantCompanyName) ? trim($tenantCompanyName) : '';
                            if ($displayName === '') {
                                $displayName = null;
                            }
                        }
                        if ($displayName === null) {
                            $displayName = $user?->company_name ?: $user?->name;
                        }
                        $displayName = is_string($displayName) ? trim($displayName) : '';
                        $lowerDisplayName = strtolower($displayName);
                        if (in_array($lowerDisplayName, ['superadmin', 'lioncoders'], true)) {
                            $displayName = tenant()?->id ?: 'Tenant';
                        }
                    @endphp
                @if (in_array('restaurant', explode(',', (string) ($general_setting->modules ?? ''))))
                    @if (Auth::user()->role_id > 2 && isset(Auth::user()->service_staff))
                        @php
                            $cooked = DB::table('sales')
                                ->where('waiter_id', Auth::user()->id)
                                ->where('sale_status', 5)
                                ->orWhere('sale_status', 6)
                                ->where('sales.created_at', '>=', now()->subDay())
                                ->count();
                        @endphp
                    @elseif(Auth::user()->role_id <= 2)
                        @php
                            $cooked = DB::table('sales')
                                ->where('sale_status', 6)
                                ->where('sales.created_at', '>=', now()->subDay())
                                ->count();
                        @endphp
                    @endif
                @endif
                @if (in_array('restaurant', explode(',', (string) ($general_setting->modules ?? ''))))
                    <a href="{{ route('kitchen.dashboard') }}">
                        <div class="alert alert-warning alert-dismissible text-center mb-2">
                            <strong>{{ $cooked }} {{ __('db.Orders to serve') }}</strong>
                        </div>
                    </a>
                @endif

                @php
                    $revenue_profit_summary = $role_has_permissions_list
                        ->where('name', 'revenue_profit_summary')
                        ->first();
                @endphp
                    <div class="dash-welcome-row">
                        <div class="dash-welcome-intro flex-grow-1 min-w-0">
                            <h1 class="dash-welcome-heading">{{ __('Welcome back') }}, {{ $displayName }}</h1>
                            <p class="dash-welcome-sub">{{ __('Dashboard overview') }}</p>
                        </div>
                        @if ($revenue_profit_summary)
                            <div class="dash-welcome-filters flex-shrink-0">
                                @if (\Auth::user()->role_id <= 2)
                                <div class="d-flex align-items-center">
                                    <i class="dripicons-location mr-1" style="color:#64748b;font-size:1.1rem;"></i>
                                    <select name="warehouse_id" class="selectpicker" id="warehouse_btn" data-live-search="true"
                                        data-live-search-style="begins">
                                        <option value="0">{{ __('db.All Warehouse') }}</option>
                                        @foreach ($lims_warehouse_list as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="dropdown">
                                    <button type="button" class="btn btn-light border dropdown-toggle px-3 py-2" data-toggle="dropdown" style="border-radius:10px;">
                                        <i class="dripicons-calendar"></i> {{ __('db.Date Range') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                        <button type="button" class="btn btn-default date-btn w-100 text-left border-0" style="border-radius:0;"
                                            data-start_date="{{ date('Y-m-d') }}"
                                            data-end_date="{{ date('Y-m-d') }}">{{ __('db.Today') }}</button>
                                        <button type="button" class="btn btn-default date-btn w-100 text-left border-0" style="border-radius:0;"
                                            data-start_date="{{ date('Y-m-d', strtotime(' -7 day')) }}"
                                            data-end_date="{{ date('Y-m-d') }}">{{ __('db.Last 7 Days') }}</button>
                                        <button type="button" class="btn btn-default date-btn w-100 active text-left border-0" style="border-radius:0;"
                                            data-start_date="{{ date('Y') . '-' . date('m') . '-' . '01' }}"
                                            data-end_date="{{ date('Y-m-d') }}">{{ __('db.This Month') }}</button>
                                        <button type="button" class="btn btn-default date-btn w-100 text-left border-0" style="border-radius:0;"
                                            data-start_date="{{ date('Y') . '-01' . '-01' }}"
                                            data-end_date="{{ date('Y') . '-12' . '-31' }}">{{ __('db.This Year') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
        </div>
    </div>
    <!-- Counts Section -->
    <section class="dashboard-counts pt-2">
        <div class="container-fluid">
            <div class="row">
                @if ($revenue_profit_summary)
                    <div class="col-md-12 form-group mb-0">
                        <div class="row dash-metric-row">
                            <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
                                <div class="dash-metric-card dash-metric-card--revenue" role="group"
                                    aria-label="{{ __('db.revenue') }}">
                                    <div class="dash-metric-card__icon" aria-hidden="true">
                                        <span class="dash-metric-card__emoji">💰</span>
                                        <i class="dripicons-graph-bar"></i>
                                    </div>
                                    <div class="dash-metric-card__body">
                                        <div class="dash-metric-card__value revenue-data">
                                            {{ number_format((float) $revenue, $general_setting->decimal, '.', '') }}
                                        </div>
                                        <div class="dash-metric-card__label">
                                            <strong>{{ __('db.revenue') }}</strong>
                                            <x-info title="(grand_total - shipping_cost) - Return +income  =  Revenue" type="info" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
                                <div class="dash-metric-card dash-metric-card--sale-return" role="group"
                                    aria-label="{{ __('db.Sale Return') }}">
                                    <div class="dash-metric-card__icon" aria-hidden="true">
                                        <span class="dash-metric-card__emoji">↩️</span>
                                        <i class="dripicons-return"></i>
                                    </div>
                                    <div class="dash-metric-card__body">
                                        <div class="dash-metric-card__value return-data">
                                            {{ number_format((float) $return, $general_setting->decimal, '.', '') }}
                                        </div>
                                        <div class="dash-metric-card__label">
                                            <strong>{{ __('db.Sale Return') }}</strong>
                                            <x-info title="Total Sale Return Amount" type="info" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
                                <div class="dash-metric-card dash-metric-card--purchase-return" role="group"
                                    aria-label="{{ __('db.Purchase Return') }}">
                                    <div class="dash-metric-card__icon" aria-hidden="true">
                                        <span class="dash-metric-card__emoji">📦</span>
                                        <i class="dripicons-box"></i>
                                    </div>
                                    <div class="dash-metric-card__body">
                                        <div class="dash-metric-card__value purchase_return-data">
                                            {{ number_format((float) $purchase_return, $general_setting->decimal, '.', '') }}
                                        </div>
                                        <div class="dash-metric-card__label">
                                            <strong>{{ __('db.Purchase Return') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-xl-3 mb-0">
                                <div class="dash-metric-card dash-metric-card--profit" role="group"
                                    aria-label="{{ __('db.profit') }}">
                                    <div class="dash-metric-card__icon" aria-hidden="true">
                                        <span class="dash-metric-card__emoji">📈</span>
                                        <i class="dripicons-trophy"></i>
                                    </div>
                                    <div class="dash-metric-card__body">
                                        <div class="dash-metric-card__value profit-data">
                                            {{ number_format((float) $profit, $general_setting->decimal, '.', '') }}
                                        </div>
                                        <div class="dash-metric-card__label">
                                            <strong>{{ __('db.profit') }}</strong>
                                            <x-info title="Revenue + Purchase Return - Product Cost - Expense" type="info" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @php
                    $cash_flow = $role_has_permissions_list->where('name', 'cash_flow')->first();
                @endphp
                @if ($cash_flow)
                    <div class="col-md-7 mt-4">
                        <div class="card line-chart-example dash-panel-card border">
                            <div class="card-header d-flex align-items-center border-bottom bg-white">
                                <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.Cash Flow') }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="cashFlow" data-color = "{{ $color }}"
                                    data-color_rgba = "{{ $color_rgba }}"
                                    data-recieved = "{{ json_encode($payment_recieved) }}"
                                    data-sent = "{{ json_encode($payment_sent) }}"
                                    data-month = "{{ json_encode($month) }}"
                                    data-label1="{{ __('db.Payment Recieved') }}"
                                    data-label2="{{ __('db.Payment Sent') }}"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
                @php
                    $monthly_summary = $role_has_permissions_list->where('name', 'monthly_summary')->first();
                @endphp
                @if ($monthly_summary)
                    <div class="col-md-5 mt-4">
                        <div class="card dash-panel-card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white">
                                <h4 class="mb-0 text-dark font-weight-bold">{{ date('F') }} {{ date('Y') }}</h4>
                            </div>
                            <div class="pie-chart mb-2">
                                <canvas id="transactionChart" data-color = "{{ $color }}"
                                    data-color_rgba = "{{ $color_rgba }}" data-revenue={{ $revenue }}
                                    data-purchase={{ $purchase }} data-expense={{ $expense }}
                                    data-label1="{{ __('db.Purchase') }}" data-label2="{{ __('db.revenue') }}"
                                    data-label3="{{ __('db.Expense') }}" width="100" height="95"> </canvas>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                @php
                    $yearly_report = $role_has_permissions_list->where('name', 'yearly_report')->first();
                @endphp
                @if ($yearly_report)
                    <div class="col-md-12">
                        <div class="card dash-panel-card border">
                            <div class="card-header d-flex align-items-center border-bottom bg-white">
                                <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.yearly report') }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="saleChart" data-sale_chart_value = "{{ json_encode($yearly_sale_amount) }}"
                                    data-purchase_chart_value = "{{ json_encode($yearly_purchase_amount) }}"
                                    data-label1="{{ __('db.Purchased Amount') }}"
                                    data-label2="{{ __('db.Sold Amount') }}"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-7">
                    <div class="card dash-panel-card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white">
                            <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.Recent Transaction') }}</h4>
                            <div class="right-column">
                                <div class="badge badge-primary">{{ __('db.latest') }} 5</div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#sale-latest" role="tab"
                                    data-toggle="tab">{{ __('db.Sale') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#purchase-latest" role="tab"
                                    data-toggle="tab">{{ __('db.Purchase') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#quotation-latest" role="tab"
                                    data-toggle="tab">{{ __('db.Quotation') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#payment-latest" role="tab"
                                    data-toggle="tab">{{ __('db.Payment') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">
                                    <table id="recent-sale" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('db.date') }}</th>
                                                <th>{{ __('db.reference') }}</th>
                                                <th>{{ __('db.customer') }}</th>
                                                <th>{{ __('db.status') }}</th>
                                                <th>{{ __('db.grand total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="purchase-latest">
                                <div class="table-responsive">
                                    <table id="recent-purchase" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('db.date') }}</th>
                                                <th>{{ __('db.reference') }}</th>
                                                <th>{{ __('db.Supplier') }}</th>
                                                <th>{{ __('db.status') }}</th>
                                                <th>{{ __('db.grand total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="quotation-latest">
                                <div class="table-responsive">
                                    <table id="recent-quotation" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('db.date') }}</th>
                                                <th>{{ __('db.reference') }}</th>
                                                <th>{{ __('db.customer') }}</th>
                                                <th>{{ __('db.status') }}</th>
                                                <th>{{ __('db.grand total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="payment-latest">
                                <div class="table-responsive">
                                    <table id="recent-payment" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('db.date') }}</th>
                                                <th>{{ __('db.reference') }}</th>
                                                <th>{{ __('db.Amount') }}</th>
                                                <th>{{ __('db.Paid By') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card dash-panel-card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white">
                            <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.Best Seller') . ' ' . date('F') }}</h4>
                            <div class="right-column">
                                <div class="badge badge-primary">{{ __('db.top') }} 5</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="monthly-best-selling-qty" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('db.Product Details') }}</th>
                                        <th>{{ __('db.qty') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card dash-panel-card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white">
                            <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.Best Seller') . ' ' . date('Y') . '(' . __('db.qty') . ')' }}</h4>
                            <div class="right-column">
                                <div class="badge badge-primary">{{ __('db.top') }} 5</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="yearly-best-selling-qty" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('db.Product Details') }}</th>
                                        <th>{{ __('db.qty') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card dash-panel-card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white">
                            <h4 class="mb-0 text-dark font-weight-bold">{{ __('db.Best Seller') . ' ' . date('Y') . '(' . __('db.Price') . ')' }}</h4>
                            <div class="right-column">
                                <div class="badge badge-primary">{{ __('db.top') }} 5</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="yearly-best-selling-price" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('db.Product Details') }}</th>
                                        <th>{{ __('db.grand total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/yearly-best-selling-price') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = '{{ url('/images/product') }}';
                    data.forEach(function(item) {
                        if (item.product_images)
                            var images = item.product_images.split(',');
                        else
                            var images = ['zummXD2dvAtI.png'];
                        $('#yearly-best-selling-price').find('tbody').append(
                            '<tr><td><div class="d-flex align-items-center"><img src="' +
                            url + '/' + images[0] +
                            '" width="30" height="25" class="ml-3 mr-3"> ' + item
                            .product_name + ' [' + item.product_code + ']</div></td><td>' +
                            (item.total_price / item.exchange_rate).toFixed({{ $general_setting->decimal }}) + '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/yearly-best-selling-qty') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = '{{ url('/images/product') }}';
                    data.forEach(function(item) {
                        if (item.product_images)
                            var images = item.product_images.split(',');
                        else
                            var images = ['zummXD2dvAtI.png'];
                        $('#yearly-best-selling-qty').find('tbody').append(
                            '<tr><td><div class="d-flex align-items-center"><img src="' +
                            url + '/' + images[0] +
                            '" width="30" height="25" class="ml-3 mr-3"> ' + item
                            .product_name + ' [' + item.product_code + ']</div></td><td>' +
                            item.sold_qty + '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/monthly-best-selling-qty') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = '{{ url('/images/product') }}';
                    data.forEach(function(item) {
                        if (item.product_images)
                            var images = item.product_images.split(',');
                        else
                            var images = ['zummXD2dvAtI.png'];
                        $('#monthly-best-selling-qty').find('tbody').append(
                            '<tr><td><div class="d-flex align-items-center"><img src="' +
                            url + '/' + images[0] +
                            '" width="30" height="25" class="ml-3 mr-3"> ' + item
                            .product_name + ' [' + item.product_code + ']</div></td><td>' +
                            item.sold_qty + '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: "{{ url('/recent-sale') }}",
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        var sale_date = dateFormat(item.created_at.split('T')[0],
                            '{{ $general_setting->date_format }}')
                        if (item.sale_status == 1) {
                            var status =
                                '<div class="badge badge-success">{{ __('db.Completed') }}</div>';
                        } else if (item.sale_status == 2) {
                            var status =
                                '<div class="badge badge-danger">{{ __('db.Pending') }}</div>';
                        } else {
                            var status =
                                '<div class="badge badge-warning">{{ __('db.Draft') }}</div>';
                        }
                        $('#recent-sale').find('tbody').append('<tr><td>' + sale_date +
                            '</td><td>' + item.reference_no + '</td><td>' + item.name +
                            '</td><td>' + status + '</td><td>' + (item.grand_total/item.exchange_rate).toString()
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/recent-purchase') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        var payment_date = dateFormat(item.created_at.split('T')[0],
                            '{{ $general_setting->date_format }}')
                        if (item.status == 1) {
                            var status =
                                '<div class="badge badge-success">{{ __('db.Recieved') }}</div>';
                        } else if (item.status == 2) {
                            var status =
                                '<div class="badge badge-danger">{{ __('db.Partial') }}</div>';
                        } else if (item.status == 3) {
                            var status =
                                '<div class="badge badge-danger">{{ __('db.Pending') }}</div>';
                        } else {
                            var status =
                                '<div class="badge badge-warning">{{ __('db.Ordered') }}</div>';
                        }
                        $('#recent-purchase').find('tbody').append('<tr><td>' + payment_date +
                            '</td><td>' + item.reference_no + '</td><td>' + item.name +
                            '</td><td>' + status + '</td><td>' + (item.grand_total/item.exchange_rate).toString()
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/recent-quotation') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        var quotation_date = dateFormat(item.created_at.split('T')[0],
                            '{{ $general_setting->date_format }}')
                        if (item.quotation_status == 1) {
                            var status =
                                '<div class="badge badge-success">{{ __('db.Pending') }}</div>';
                        } else if (item.quotation_status == 2) {
                            var status =
                                '<div class="badge badge-danger">{{ __('db.Sent') }}</div>';
                        }
                        $('#recent-quotation').find('tbody').append('<tr><td>' +
                            quotation_date + '</td><td>' + item.reference_no + '</td><td>' +
                            item.name + '</td><td>' + status + '</td><td>' + item
                            .grand_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                            '</td></tr>');
                    })
                }
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/recent-payment') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        var payment_date = dateFormat(item.created_at.split('T')[0],
                            '{{ $general_setting->date_format }}')
                        $('#recent-payment').find('tbody').append('<tr><td>' + payment_date +
                            '</td><td>' + item.payment_reference + '</td><td>' + (item.amount/item.exchange_rate)
                            .toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                            '</td><td>' + item.paying_method + '</td></tr>');
                    })
                }
            });
        });

        function dateFormat(inputDate, format) {
            const date = new Date(inputDate);
            //extract the parts of the date
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            //replace the month
            format = format.replace("m", month.toString().padStart(2, "0"));
            //replace the year
            format = format.replace("Y", year.toString());
            //replace the day
            format = format.replace("d", day.toString().padStart(2, "0"));
            return format;
        }


        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#userShowModal').modal('show');
                    $('#user-id').text(data.id);
                    $('#user-name').text(data.name);
                    $('#user-email').text(data.email);
                }
            });
        })
        // Show and hide color-switcher
        $(".color-switcher .switcher-button").on('click', function() {
            $(".color-switcher").toggleClass("show-color-switcher", "hide-color-switcher", 300);
        });

        // Color Skins
        $('a.color').on('click', function() {
            /*var title = $(this).attr('title');
            $('#style-colors').attr('href', 'css/skin-' + title + '.css');
            return false;*/
            $.get('setting/general_setting/change-theme/' + $(this).data('color'), function(data) {});
            var style_link = $('#custom-style').attr('href').replace(/([^-]*)$/, $(this).data('color'));
            $('#custom-style').attr('href', style_link);
        });

        $(".date-btn").on("click", function() {
            $(".date-btn").removeClass("active");
            $(this).addClass("active");
            var start_date = $(this).data('start_date');
            var end_date = $(this).data('end_date');
            var warehouse_id = $("#warehouse_btn").val();
            $.get('dashboard-filter/' + start_date + '/' + end_date + '/' + warehouse_id, function(data) {
                dashboardFilter(data);
            });
        });

        $("#warehouse_btn").on("change", function() {
            var warehouse_id = $(this).val();
            var start_date = $('.date-btn.active').data('start_date');
            var end_date = $('.date-btn.active').data('end_date');
            $.get('dashboard-filter/' + start_date + '/' + end_date + '/' + warehouse_id, function(data) {
                dashboardFilter(data);
            });
        });

        function dashboardFilter(data) {
            // data is an array:
            // [revenue, sale_return, profit, purchase_return, total_sale, invoice_due, total_purchase, purchase_due]

            $('.total_sale-data').hide();
            $('.total_sale-data').html(parseFloat(data[4] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.total_sale-data').show(500);

            $('.revenue-data').hide();
            $('.revenue-data').html(parseFloat(data[0] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.revenue-data').show(500);

            $('.invoice-due-data').hide();
            $('.invoice-due-data').html(parseFloat(data[5] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.invoice-due-data').show(500);

            $('.return-data').hide();
            $('.return-data').html(parseFloat(data[1] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.return-data').show(500);

            $('.total_purchase-data').hide();
            $('.total_purchase-data').html(parseFloat(data[6] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.total_purchase-data').show(500);

            $('.purchase_due-data').hide();
            $('.purchase_due-data').html(parseFloat(data[7] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.purchase_due-data').show(500);

            $('.purchase_return-data').hide();
            $('.purchase_return-data').html(parseFloat(data[3] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.purchase_return-data').show(500);

            $('.profit-data').hide();
            $('.profit-data').html(parseFloat(data[2] ?? 0).toFixed({{ $general_setting->decimal }}));
            $('.profit-data').show(500);
        }
    </script>
@endpush
