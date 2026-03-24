@extends('landlord.layout.main') @section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif

    <section>
        <div class="container-fluid">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createModal"><i
                    class="dripicons-plus"></i> {{ __('db.Add Client') }}</button>
            <a href="{{ route('superadmin.backupTenantDB', [], false) }}" class="btn btn-dark"><i class="dripicons-cloud-download"></i>
                {{ __('db.Backup Client DB') }}</a>
            <a href="{{ route('superadmin.updateTenantDB', [], false) }}" class="btn btn-primary"><i class="dripicons-stack"></i>
                {{ __('db.Update Client DB') }}</a>
            <a href="{{ route('superadmin.updateSuperadminDB', [], false) }}" class="btn btn-info"><i class="dripicons-stack"></i>
                {{ __('db.Update SuperAdmin DB') }}</a>
        </div>

        <div class="table-responsive">
            <table id="client-table" class="table">
                <thead>
                    <tr>
                        <th class="not-exported"></th>
                        <th>{{ __('db.name') }}</th>
                        <th>DB</th>
                        <th>{{ __('db.Domain') }}</th>
                        <th>{{ __('db.Package') }}</th>
                        <th>{{ __('db.Subscription Type') }}</th>
                        <th>{{ __('db.Company Name') }}</th>
                        <th>{{ __('db.Phone Number') }}</th>
                        <th>{{ __('db.Email') }}</th>
                        <th>{{ __('db.Created At') }}</th>
                        <th>{{ __('db.Expiry Date') }}</th>
                        <th>{{ __('db.Last Login at') }}</th>
                        <th class="not-exported">{{ __('db.action') }}</th>
                    </tr>
                    <tr class="filters">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <select class="form-control selectpicker column-filter" data-live-search="true" data-column="3">
                                <option value="">Select Domain</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-control selectpicker column-filter" data-live-search="true" data-column="4">
                                <option value="">Select Package</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-control selectpicker column-filter" data-live-search="true" data-column="5">
                                <option value="">Select Type</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <select class="form-control selectpicker column-filter" data-column="10">
                                <option value="">All</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lims_client_all as $key => $client)
                        <?php
                        if ($client->package_id) {
                            $package_name = \App\Models\landlord\Package::find($client->package_id)->name;
                        } else {
                            $package_name = 'N/A';
                        }
                        ?>
                        <tr data-id="{{ $client->id }}">
                            <td>{{ $key }}</td>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->database()->getName() }}</td>
                            <td>
                                @foreach ($client->domains as $index => $domain)
                                    @if ($index)
                                        <br>
                                    @endif
                                    <a target="_blank" href="{!! 'https://' . $domain->domain !!}">{{ $domain->domain }}</a>
                                @endforeach
                            </td>
                            <td>{{ $package_name }}</td>
                            @php
                                $provStatus = strtolower((string) ($client->provisioning_status ?? 'active'));
                            @endphp
                            <td>
                                {{ $client->subscription_type }}
                                <div class="mt-1">
                                    @if ($provStatus === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif ($provStatus === 'provisioning')
                                        <span class="badge badge-info">Provisioning</span>
                                    @elseif ($provStatus === 'failed')
                                        <span class="badge badge-danger">Failed</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $client->company_name }}</td>
                            <td>{{ $client->phone_number }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ date($general_setting->date_format, strtotime($client->created_at->toDateString())) }}
                            </td>
                            @if ($client->expiry_date >= date('Y-m-d'))
                                <td data-search="Not Expired">
                                    <div class="badge badge-success">
                                        {{ date($general_setting->date_format, strtotime($client->expiry_date)) }}
                                    </div>
                                </td>
                            @else
                                <td data-search="Expired">
                                    <div class="badge badge-danger">
                                        {{ date($general_setting->date_format, strtotime($client->expiry_date)) }}
                                    </div>
                                </td>
                            @endif
                            <td>{{ $client->last_login_at }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">{{ __('db.action') }}
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                        user="menu">
                                        <li>
                                            @if ($provStatus === 'active')
                                                <button type="button" data-id="{{ $client->id }}"
                                                    data-subscription_type="{{ $client->subscription_type }}"
                                                    data-expiry_date="{{ date('d-m-Y', strtotime($client->expiry_date)) }}"
                                                    class="renew-btn btn btn-link" data-toggle="modal"
                                                    data-target="#renewModal"><i class="dripicons-clockwise"></i>
                                                    {{ __('db.Renew Subscription') }}</button>
                                            @else
                                                <span class="btn btn-link disabled" aria-disabled="true"><i
                                                        class="dripicons-clockwise"></i> {{ __('db.Renew Subscription') }}</span>
                                            @endif
                                        </li>
                                        <li>
                                            @if ($provStatus === 'active')
                                                <button type="button" data-id="{{ $client->id }}"
                                                    data-package_id="{{ $client->package_id }}" class="switch-btn btn btn-link"
                                                    data-toggle="modal" data-target="#switchModal"><i
                                                        class="dripicons-swap"></i> {{ __('db.Change Package') }}</button>
                                            @else
                                                <span class="btn btn-link disabled" aria-disabled="true"><i
                                                        class="dripicons-swap"></i> {{ __('db.Change Package') }}</span>
                                            @endif
                                        </li>
                                        <li>
                                            <button type="button" data-id="{{ $client->id }}"
                                                class="add-custom-domain-btn btn btn-link" data-toggle="modal"
                                                data-target="#customDomainModal"><i class="dripicons-plus"></i>
                                                {{ __('db.Add Custom Domain') }}</button>
                                        </li>
                                        {{ Form::open(['url' => '/superadmin/clients/destroy/'.$client->id, 'method' => 'DELETE']) }}
                                        <li>
                                            <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i
                                                    class="dripicons-trash"></i> {{ __('db.delete') }}</button>
                                        </li>
                                        {{ Form::close() }}
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create Modal -->
    <div id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/superadmin/clients/store', 'method' => 'post', 'id' => 'client-form']) !!}
                <div class="modal-header">
                    <h5 id="createModalLabel" class="modal-title">{{ __('db.Add Client') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Package') }} *</label>
                                <select required class="form-control selectpicker" name="package_id">
                                    @foreach ($lims_package_all as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Subscription Type') }} *</label>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="free trial"
                                        class="form-check-input" id="subscription-type-create-free-trial" checked>
                                    <label class="form-check-label" for="subscription-type-create-free-trial">
                                        Free Trial
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="monthly"
                                        class="form-check-input" id="subscription-type-create-monthly">
                                    <label class="form-check-label" for="subscription-type-create-monthly">
                                        Monthly
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="yearly"
                                        class="form-check-input" id="subscription-type-create-yearly">
                                    <label class="form-check-label" for="subscription-type-create-yearly">
                                        Yearly
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Company Name') }} *</label>
                                <input id="company-name" class="form-control" type="text" name="company_name" required
                                    placeholder="company name...">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Phone Number') }} *</label>
                                <input class="form-control" type="text" name="phone_number" required
                                    placeholder="contact number...">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.UserName') }} *</label>
                                <input class="form-control" type="text" name="name" required
                                    placeholder="username...">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Password') }} *</label>
                                <input class="form-control" type="password" name="password" required
                                    placeholder="password...">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Email') }} *</label>
                                <input class="form-control" type="text" name="email" required
                                    placeholder="email...">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Subdomain</label>
                                <div class="input-group">
                                    <input id="tenant-field" class="form-control mt-0" type="text" name="tenant"
                                        placeholder="subdomain..." aria-label="subdomain..."
                                        aria-describedby="basic-addon2">
                                    <span class="input-group-text"
                                        id="basic-addon2">{{ '@' . config('app.central_domain') }}</span>
                                </div>
                                <small id="subdomain-hint" class="form-text text-muted"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="{{ __('db.submit') }}" id="submit-btn"
                                class="btn btn-primary">
                        </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- custom domain Modal -->
    <div id="customDomainModal" tabindex="-1" role="dialog" aria-labelledby="customDomainModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/superadmin/clients/add-custom-domain', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="customDomainModalLabel" class="modal-title">{{ __('db.Add Custom Domain') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <div class="mb-2">
                        <small class="text-muted">
                            A record / DNS point to server required for this domain to work.
                            <br>
                            cPanel credentials required for auto-addon-domain creation (if SERVER_TYPE=cpanel).
                        </small>
                    </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Custom Domain') }} *</label>
                                <input type="text" name="domain" required class="form-control"
                                    placeholder="example.com">
                                <input type="hidden" name="id">
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="{{ __('db.submit') }}" class="btn btn-primary">
                        </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Renew Modal -->
    <div id="renewModal" tabindex="-1" role="dialog" aria-labelledby="renewModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/superadmin/clients/renew', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="renewModalLabel" class="modal-title">{{ __('db.Renew Subscription') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Expiry Date') }} *</label>
                                <input type="text" name="expiry_date" required class="date form-control">
                                <input type="hidden" name="id">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Subscription Type') }} *</label>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="monthly"
                                        class="form-check-input" required id="subscription-type-1">
                                    <label class="form-check-label" for="subscription-type-1">
                                        Monthly
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="yearly"
                                        class="form-check-input" required id="subscription-type-2">
                                    <label class="form-check-label" for="subscription-type-2">
                                        Yearly
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="{{ __('db.submit') }}" class="btn btn-primary">
                        </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Change Package Modal -->
    <div id="switchModal" tabindex="-1" role="dialog" aria-labelledby="switchModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/superadmin/clients/change-package', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="switchModalLabel" class="modal-title">{{ __('db.Change Package') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                        <div class="form-group">
                            <label>{{ __('db.Package') }} *</label>
                            <select required class="form-control selectpicker" name="package_id">
                                @foreach ($lims_package_all as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="client_id">
                            <input type="hidden" name="previous_package_id">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="{{ __('db.submit') }}" class="btn btn-primary">
                        </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div id="deleteModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Deleting Clients</h5>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Deleting Clients, please wait...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $("ul#client").siblings('a').attr('aria-expanded', 'true');
        $("ul#client").addClass("show");
        $("ul#client #client-list-menu").addClass("active");

        var client_id = [];
        var user_verified = <?php echo json_encode((string) env('USER_VERIFIED')); ?>;
        var checkSubdomainUrl = '{{ route('clients.checkSubdomain', [], false) }}';

        let tenantTouched = false;
        let suppressTenantTouched = false;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.add-custom-domain-btn', function() {
            $("#customDomainModal input[name='id']").val($(this).data('id'));

        });

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        function sanitizeSubdomainClientSide(value) {
            value = String(value ?? '').toLowerCase().trim();
            // Keep only a-z, 0-9 and hyphen.
            value = value.replace(/[^a-z0-9-]+/g, '');
            value = value.replace(/-+/g, '-');
            value = value.replace(/^-+|-+$/g, '');
            return value;
        }

        function setTenantFieldValue(nextValue) {
            suppressTenantTouched = true;
            $('input[name=tenant]').val(nextValue);
            suppressTenantTouched = false;
        }

        var $companyNameInput = $("input[name='company_name']");
        var $tenantInput = $("input[name='tenant']");
        var $subdomainHint = $("#subdomain-hint");

        // Manual edits => stop overwriting from Company Name.
        $tenantInput.on('input', function() {
            if (suppressTenantTouched) return;
            tenantTouched = true;
            $subdomainHint.text('');
        });

        // Auto-generate while user hasn't edited Subdomain manually.
        var companyDebounce = null;
        $companyNameInput.on('input', function() {
            if (tenantTouched) return;
            clearTimeout(companyDebounce);
            companyDebounce = setTimeout(function() {
                var raw = $companyNameInput.val();
                if (!raw) return;

                $.get(checkSubdomainUrl, { value: raw })
                    .done(function(resp) {
                        if (!resp || !resp.ok) return;
                        var suggested = resp.suggested_subdomain;
                        setTenantFieldValue(suggested);

                        if (resp.sanitized_subdomain && suggested !== resp.sanitized_subdomain) {
                            $subdomainHint.text('Adjusted to "' + suggested + '" because it already exists.');
                        } else {
                            $subdomainHint.text('');
                        }
                    });
            }, 400);
        });

        // On blur, sanitize + enforce uniqueness (but only when user leaves the field).
        $tenantInput.on('blur', function() {
            var raw = $tenantInput.val();
            if (!raw) return;
            var sanitized = sanitizeSubdomainClientSide(raw);
            if (!sanitized) return;

            $.get(checkSubdomainUrl, { value: raw })
                .done(function(resp) {
                    if (!resp || !resp.ok) return;
                    if (resp.suggested_subdomain && resp.suggested_subdomain !== $tenantInput.val()) {
                        setTenantFieldValue(resp.suggested_subdomain);
                        $subdomainHint.text('Adjusted to "' + resp.suggested_subdomain + '" because it already exists.');
                    }
                });
        });

        // When modal opens (e.g. after validation error with old input), auto-fill if user didn't touch Subdomain.
        $('#createModal').on('shown.bs.modal', function() {
            if (tenantTouched) return;
            if ($tenantInput.val()) return; // keep old/manual subdomain intact
            var rawCompany = $companyNameInput.val();
            if (!rawCompany) return;

            $.get(checkSubdomainUrl, { value: rawCompany })
                .done(function(resp) {
                    if (!resp || !resp.ok) return;
                    setTenantFieldValue(resp.suggested_subdomain);
                });
        });

        $(document).on('click', '.renew-btn', function() {
            $("#renewModal input[name='id']").val($(this).data('id'));

            var subType = $(this).data('subscription_type');
            $("#renewModal input[name='expiry_date']").val($(this).data('expiry_date'));

            // Renew modal only supports monthly/yearly; if current is free trial, default to monthly.
            $("#renewModal input[name='subscription_type'][value='monthly']").prop(
                "checked",
                subType === 'monthly' || subType === 'free trial'
            );
            $("#renewModal input[name='subscription_type'][value='yearly']").prop(
                "checked",
                subType === 'yearly'
            );
        });

        $(document).on('click', '.switch-btn', function() {
            $("#switchModal input[name='client_id']").val($(this).data('id'));
            $("#switchModal input[name='previous_package_id']").val($(this).data('package_id'));
            $("#switchModal select[name='package_id']").val($(this).data('package_id'));
            $('.selectpicker').selectpicker('refresh');
        });

        $(document).on('submit', '#client-form', function(e) {
            $("#submit-btn").prop('disabled', true);
        });

        $('#client-table').DataTable({
            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{ __('db.records per page') }}',
                "info": '<small>{{ __('db.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{ __('db.Search') }}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            'columnDefs': [{
                    'targets': [0, 2, 3, 4, 5, 6, 8, 9, 11, 12],
                    "orderable": false
                },
                {
                    'targets': 0,
                    'render': function(data, type, row) {
                        if (type === 'display') {
                            data =
                                '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                        }

                        return data;
                    },
                    'checkboxes': {
                        'selectRow': true,
                        'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                    }
                },
                {
                    targets: 10, // Force to column search using this data-search attr
                    render: function(data, type, row) {
                        return $(data).attr('data-search') || data;
                    }
                }
            ],
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            'lengthMenu': [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: '<"row"lfB>rtip',
            buttons: [{
                    extend: 'pdf',
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    footer: true
                },
                {
                    extend: 'excel',
                    text: '<i title="export to excel" class="dripicons-document-new"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    footer: true
                },
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    footer: true
                },
                {
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    footer: true
                },
                {
                    text: '<i title="delete" class="dripicons-cross"></i>',
                    className: 'buttons-delete',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    footer: true,
                    action: function(e, dt, node, config) {
                        if (user_verified == '1') {
                            var clients_id = [];
                            $('table tbody :checkbox:checked').each(function(i) {
                                clients_id[i] = $(this).closest('tr').data('id');
                            });

                            if (clients_id.length && confirm("Are you sure want to delete?")) {
                                $('#deleteModal').modal('show');
                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('clients.deleteBySelection', [], false) }}',
                                    data: {
                                        clientsIdArray: clients_id
                                    },
                                    success: function(data) {
                                        $('#deleteModal').modal('hide');
                                        var selectedRows = dt.rows({page: 'current'}).nodes().to$().find('input[type="checkbox"]:checked').closest('tr');
                                        dt.rows(selectedRows).remove().draw(false);
                                        var msg = (data && data.message) ? data.message : data;
                                        alert(msg);
                                    },
                                    error: function(xhr) {
                                        $('#deleteModal').modal('hide');
                                        var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message)
                                            ? xhr.responseJSON.message
                                            : 'Delete failed. Please try again.';
                                        alert(msg);
                                    }
                                });
                            }
                            else if (!clients_id.length)
                                alert('No client is selected!');
                        }
                        else
                            alert('This feature is disable for demo!');
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
            ],

            orderCellsTop: true,
            initComplete: function() {
                // Add filtering to the individual columns
                this.api().columns().every(function() {
                    var column = this;
                    // Find the relevant select dropdown based on column index
                    var select = $('.column-filter[data-column="' + column.index() + '"]');

                    if (column.index() === 3) {
                        var domains = new Set(); // Use Set to store unique domains
                        // Loop through the data of the column
                        column.data().each(function(d, j) {
                            // Create a DOM element to parse the HTML content
                            var tempDiv = $('<div>').html(d);
                            // Find all <a> tags and extract the href or text (domains)
                            tempDiv.find('a').each(function() {
                                var domain = $(this).text().trim();
                                if (domain) {
                                    domains.add(domain);
                                }
                            });
                        });
                        // Populate the select dropdown with unique domains
                        domains.forEach(function(domain) {
                            select.append('<option value="' + domain + '">' + domain +
                                '</option>');
                        });
                        select.selectpicker('refresh');
                        // Add change event listener for filtering
                        select.on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val, true, false).draw();
                        });

                    } else if (column.index() === 10) {
                        var uniqueValues = new Set(); // Set to store unique values
                        // Loop through the column data
                        column.nodes().to$().each(function() {
                            var searchValue = $(this).attr('data-search');
                            if (searchValue) {
                                uniqueValues.add(searchValue);
                            }
                        });
                        // Populate the select dropdown with unique values
                        uniqueValues.forEach(function(value) {
                            select.append('<option value="' + value + '">' + value +
                                '</option>');
                        });
                        select.selectpicker('refresh');
                        // Add change event listener for filtering
                        select.on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });

                    } else {
                        // Populate the select dropdown with unique values
                        column.data().unique().sort().each(function(d, j) {
                            if (d) {
                                // Strip HTML tags and get the text content
                                var plainText = $('<div>').html(d).text();
                                if (select.find('option[value="' + plainText + '"]').length ===
                                    0) {
                                    select.append('<option value="' + plainText + '">' +
                                        plainText +
                                        '</option>');
                                }
                            }
                        });
                        select.selectpicker('refresh');
                        // Add change event listener for filtering
                        select.on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });

                    }
                });
            }
        });
    </script>
@endpush
