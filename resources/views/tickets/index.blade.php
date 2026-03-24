@php
if (!tenant()) {
    $routePrefix = 'superadmin.';
    $layout = 'landlord.layout.main';
}
else {
    $routePrefix = '';
    $layout = 'backend.layout.main';
}
@endphp

@extends($layout)

@section('title','Support Tickets')
@section('content')

<x-success-message key="message" />
<x-error-message key="not_permitted" />


<section>
    @if(tenant())
    <div class="container-fluid">
        <a href="{{ route($routePrefix.'tickets.create') }}" class="btn btn-info"><i class="dripicons-plus"></i> {{ __('db.create_ticket') }}</a>
    </div>
    @endif
    <div class="table-responsive">
        <table id="ticket-table" class="table">
            <thead>
                <tr>
                    <th>{{ __('db.subject') }}</th>
                    @if(!tenant())
                    <th>{{ __('db.tenant') }}</th>
                    @endif
                    <th>{{ __('db.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->subject ?? __('db.no_subject') }}</td>
                        @if(!tenant())
                            <td>{{ $ticket->tenant_id }}</td>
                        @endif
                        <td>
                            <button type="button" class="btn btn-sm btn-info"
                            onclick="window.location='{{ route($routePrefix.'tickets.show', $ticket->id) }}'">
                            <i class="fa fa-eye"></i> {{ __('db.view') }}
                            </button>
                            {{ Form::open(['route' => [$routePrefix.'tickets.destroy', $ticket->id], 'method' => 'DELETE', 'class' => 'd-inline']) }}
                                <button type="submit" class="btn btn-sm btn-danger ms-2"
                                        onclick="return confirmDelete()">
                                <i class="fa fa-trash"></i> {{ __('db.delete') }}
                                </button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
    <script type="text/javascript">
        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        $('#ticket-table').DataTable({
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
                    "orderable": false,
                    'targets': [-1]
                },
            ],
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
                },
                {
                    extend: 'excel',
                    text: '<i title="export to excel" class="dripicons-document-new"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                },
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                },
                {
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
            ],
        });

    </script>
@endpush
