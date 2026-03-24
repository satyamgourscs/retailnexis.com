@extends('landlord.layout.main') @section('content')

<x-success-message key="message" />
<x-error-message key="not_permitted" />
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section>
    <div class="container-fluid">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> {{__('db.Add Language')}}</button>
    </div>
    <div class="table-responsive">
        <table id="language-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.Code')}}</th>
                    <th>{{__('db.Name')}}</th>
                    <th>{{__('db.Default')}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_language_all as $key => $language)
                <tr data-id="{{$language->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $language->code }}</td>
                    <td>{{ $language->name }}</td>
                    @if($language->is_default)
                        <td><div class="badge badge-success">Yes</div></td>
                    @else
                        <td><div class="badge badge-danger">No</div></td>
                    @endif
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('db.action')}}
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">

                                <li>
                                    <button type="button" class="btn btn-link"
                                    onclick="window.location='{{ route('languages.editTranslation', $language->code, false) }}'">
                                    <i class="fa fa-language"></i> {{ __('db.edit_tanslations') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button" data-id="{{$language->id}}" data-code="{{$language->code}}" data-name="{{$language->name}}" data-is_default="{{$language->is_default}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i>  {{__('db.edit')}}</button>
                                </li>
                                {{ Form::open(['url' => route('languages.destroy', $language->id, false), 'method' => 'POST'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirm('Are you sure want to delete?')"><i class="dripicons-trash"></i> {{__('db.delete')}}</button>
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
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['url' => 'superadmin/languages/store', 'method' => 'post']) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{__('db.Add Language')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>{{__('db.Code')}} *</label>
                    {{Form::text('code',null, array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Type language code...'))}}
                </div>
                <div class="col-md-4 form-group">
                    <label>{{__('db.name')}} *</label>
                    {{Form::text('name',null, array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Type language name...'))}}
                </div>
                <div class="col-md-4 form-group mt-4">
                    <input type="checkbox" name="is_default" value="1">&nbsp;
                    <label>{{__('db.Default')}}</label>
                </div>
            </div>
            <div class="form-group">
              <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
            </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
        {{ Form::open(['url' => route('languages.update', [], false), 'method' => 'POST'] ) }}
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Language')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
        <div class="modal-body">
            <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
            <div class="row">
                <div class="col-md-4 form-group">
                    <input type="hidden" name="language_id">
                    <label>{{__('db.name')}} *</label>
                    {{Form::text('name',null, array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Type language name...'))}}
                </div>
                <div class="col-md-4 form-group mt-4">
                    <input type="checkbox" name="is_default" value="1">&nbsp;
                    <label>{{__('db.Default')}}</label>
                </div>
            </div>
            <div class="form-group">
              <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
            </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
</div>


@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-language-menu").addClass("active");


    function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    $(document).on('click', '.edit-btn', function() {
        $("#editModal input[name='language_id']").val($(this).data('id'));
        $("#editModal input[name='name']").val($(this).data('name'));
        $("#editModal input[name='code']").val($(this).data('code'));
        if($(this).data('is_default')) {
            $("#editModal input[name='is_default']").prop("checked", true);
        }
        else {
            $("#editModal input[name='is_default']").prop("checked", false);
        }
    });

    $('#language-table').DataTable( {
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
                'targets': [0, 4]
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
                    rows: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            }
        ],
    } );
</script>
@endpush
