@extends('landlord.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section>
    <div class="container-fluid">
        <a href="{{ route('packages.create', [], false) }}" class="btn btn-info"><i class="dripicons-plus"></i> {{__('db.Add Package')}}</a>&nbsp;
    </div>
    <div class="table-responsive">
        <table id="package-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.name')}}</th>
                    <th>{{__('db.Free Trial')}}</th>
                    <th>{{__('db.Monthly Fee')}}</th>
                    <th>{{__('db.Yearly Fee')}}</th>
                    <th>{{__('db.Number of Warehouses')}}</th>
                    <th>{{__('db.Number of Products')}}</th>
                    <th>{{__('db.Number of Invoices')}}</th>
                    <th>{{__('db.Number of User Account')}}</th>
                    <th>{{__('db.Number of Employees')}}</th>
                    <th>{{__('db.Features')}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_package_all as $key=>$package)
                <?php
                    $features = json_decode($package->features);
                ?>
                <tr data-id="{{$package->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $package->name }}</td>
                    @if($package->is_free_trial)
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif
                    <td>{{ $package->monthly_fee}}</td>
                    <td>{{ $package->yearly_fee}}</td>
                    <td>
                        @if($package->number_of_warehouse)
                            {{ $package->number_of_warehouse}}
                        @else
                            {{__('db.Unlimited')}}
                        @endif
                    </td>
                    <td>
                        @if($package->number_of_product)
                            {{ $package->number_of_product}}
                        @else
                            {{__('db.Unlimited')}}
                        @endif
                    </td>
                    <td>
                        @if($package->number_of_invoice)
                            {{ $package->number_of_invoice}}
                        @else
                            {{__('db.Unlimited')}}
                        @endif
                    </td>
                    <td>
                        @if($package->number_of_user_account)
                            {{ $package->number_of_user_account}}
                        @else
                            {{__('db.Unlimited')}}
                        @endif
                    </td>
                    <td>
                        @if($package->number_of_employee)
                            {{ $package->number_of_employee}}
                        @else
                            {{__('db.Unlimited')}}
                        @endif
                    </td>
                    <td>
                    @foreach($features as $index => $feature)
                        @if($index)
                            {{', '.$feature}}
                        @else
                            {{$feature}}
                        @endif
                    @endforeach
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('db.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <a href="{{ route('packages.edit', ['package' => $package->id], false) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{__('db.edit')}}</a>
                                </li>
                                {{ Form::open(['url' => '/superadmin/packages/'.$package->id, 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{__('db.delete')}}</button>
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

{{ Form::close() }}


@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#package").siblings('a').attr('aria-expanded','true');
    $("ul#package").addClass("show");
    $("ul#package #package-list-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }
    var table = $('#package-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{__("db.records per page")}}',
             "info":      '<small>{{__("db.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{__("db.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 8]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                }
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                }
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                }
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );

</script>
@endpush
