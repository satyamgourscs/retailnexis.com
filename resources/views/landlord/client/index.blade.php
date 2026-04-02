@extends('landlord.layout.main') @section('content')
    @if ($errors->has('name'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
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
            <a href="{{ route('superadmin.backupTenantDB') }}" class="btn btn-dark"><i class="dripicons-cloud-download"></i>
                {{ __('db.Backup Client DB') }}</a>
            <a href="{{ route('superadmin.updateTenantDB') }}" class="btn btn-primary"><i class="dripicons-stack"></i>
                {{ __('db.Update Client DB') }}</a>
            <a href="{{ route('superadmin.updateSuperadminDB') }}" class="btn btn-info"><i class="dripicons-stack"></i>
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
                                @php
                                    $centralDomain = (string) env('CENTRAL_DOMAIN', '');
                                    $defaultHost = $client->id . '.' . $centralDomain;
                                    $domain = null;
                                    foreach ($client->domains as $d) {
                                        $h = (string) ($d->domain ?? '');
                                        if ($h !== '' && strcasecmp($h, $defaultHost) !== 0) {
                                            $domain = $h;
                                            break;
                                        }
                                    }
                                    if ($domain === null) {
                                        $domain = optional($client->domains->first())->domain;
                                    }
                                    $hasScheme = $domain && preg_match('#^https?://#i', (string) $domain);
                                    $url = $domain
                                        ? ($hasScheme ? $domain : 'https://' . ltrim((string) $domain, '/'))
                                        : 'https://' . $defaultHost;
                                    $display = $domain ? (string) $domain : $defaultHost;
                                @endphp
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                    class="client-domain-link" style="color:#4f46e5; font-weight:500;">{{ $display }}</a>
                            </td>
                            <td>{{ $package_name }}</td>
                            <td>{{ $client->subscription_type }}</td>
                            <td>{{ $client->company_name }}</td>
                            <td>{{ $client->phone_number }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ date($general_setting->date_format, strtotime($client->created_at->toDateString())) }}
                            </td>
                            @php
                                $clientExpiry = $client->expiry_date
                                    ? \Carbon\Carbon::parse($client->expiry_date)->startOfDay()
                                    : null;
                                $isExpired = $clientExpiry === null || $clientExpiry->lt(\Carbon\Carbon::today());
                            @endphp
                            @if (! $client->expiry_date)
                                <td data-search="Expired">
                                    <div class="badge badge-secondary">N/A</div>
                                </td>
                            @elseif (! $isExpired)
                                <td data-search="Not Expired">
                                    <div class="badge badge-success">
                                        {{ $client->expiry_date ? \Carbon\Carbon::parse($client->expiry_date)->format('d-m-Y') : 'N/A' }}
                                    </div>
                                </td>
                            @else
                                <td data-search="Expired">
                                    <div class="badge badge-danger">
                                        {{ \Carbon\Carbon::parse($client->expiry_date)->format('d-m-Y') }}
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
                                            <button type="button" data-id="{{ $client->id }}"
                                                data-subscription_type="{{ $client->subscription_type }}"
                                                data-expiry_date="{{ $client->expiry_date ? \Carbon\Carbon::parse($client->expiry_date)->format('d-m-Y') : '' }}"
                                                class="renew-btn btn btn-link" data-toggle="modal"
                                                data-target="#renewModal"><i class="dripicons-clockwise"></i>
                                                {{ __('db.Renew Subscription') }}</button>
                                        </li>
                                        <li>
                                            <button type="button" data-id="{{ $client->id }}"
                                                data-package_id="{{ $client->package_id }}" class="switch-btn btn btn-link"
                                                data-toggle="modal" data-target="#switchModal"><i
                                                    class="dripicons-swap"></i> {{ __('db.Change Package') }}</button>
                                        </li>
                                        <li>
                                            <button type="button" data-id="{{ $client->id }}"
                                                class="add-custom-domain-btn btn btn-link" data-toggle="modal"
                                                data-target="#customDomainModal"><i class="dripicons-plus"></i>
                                                {{ __('db.Add Custom Domain') }}</button>
                                        </li>
                                        {{ Form::open(['route' => ['clients.destroy', $client->id], 'method' => 'DELETE']) }}
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
    <div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'clients.store', 'method' => 'post', 'id' => 'client-form']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ __('db.Add Client') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <form>
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
                                        class="form-check-input" id="subscription-type-1" checked>
                                    <label class="form-check-label" for="subscription-type-1">
                                        Free Trial
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="monthly"
                                        class="form-check-input" id="subscription-type-1">
                                    <label class="form-check-label" for="subscription-type-1">
                                        Monthly
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="subscription_type" value="yearly"
                                        class="form-check-input" id="subscription-type-2">
                                    <label class="form-check-label" for="subscription-type-2">
                                        Yearly
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('db.Company Name') }} *</label>
                                <input class="form-control" type="text" name="company_name" required
                                    placeholder="company name...">
                                <small class="form-text text-muted">Subdomain and tenant database are created automatically from this name (unique slug).</small>
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
                        </div>
                        <div class="form-group">
                            <input type="submit" value="{{ __('db.submit') }}" id="submit-btn"
                                class="btn btn-primary">
                        </div>
                    </form>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- custom domain Modal -->
    <div id="customDomainModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'clients.addCustomDomain', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ __('db.Add Custom Domain') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <form>
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
                    </form>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Renew Modal -->
    <div id="renewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'clients.renew', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ __('db.Renew Subscription') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <form>
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
                    </form>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Change Package Modal -->
    <div id="switchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'clients.changePackage', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ __('db.Change Package') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ __('db.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <form>
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
                    </form>
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

@push('custom-css')
    <style>
        .client-domain-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript">
        $("ul#client").siblings('a').attr('aria-expanded', 'true');
        $("ul#client").addClass("show");
        $("ul#client #client-list-menu").addClass("active");

        var client_id = [];
        var user_verified = <?php echo json_encode(config('app.demo_unlocked')); ?>;

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

        $(document).on('click', '.renew-btn', function() {
            $("#renewModal input[name='id']").val($(this).data('id'));
            if ($(this).data('subscription_type') == 'monthly')
                $("#subscription-type-1").prop("checked", true);
            else
                $("#subscription-type-2").prop("checked", true);
            $("#renewModal input[name='expiry_date']").val($(this).data('expiry_date'));
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
                                    url: 'clients/deletebyselection',
                                    data: {
                                        clientsIdArray: clients_id
                                    },
                                    success: function(data) {
                                        $('#deleteModal').modal('hide');
                                        var selectedRows = dt.rows({page: 'current'}).nodes().to$().find('input[type="checkbox"]:checked').closest('tr');
                                        dt.rows(selectedRows).remove().draw(false);
                                        alert(data);
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
