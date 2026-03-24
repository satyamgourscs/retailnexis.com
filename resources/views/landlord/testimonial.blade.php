@extends('landlord.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            {!! Form::open(['route' => 'testimonial.store', 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Testimonial Section')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div id="custom-field">
                            <div class="row">
                                <div class="col-sm-2">
                                    <input type="file" name="image[]" class="form-control"/>
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="name[]" class="form-control" placeholder="{{__('db.name')}}"/>
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="business_name[]" class="form-control" placeholder="{{__('db.Business Name')}}"/>
                                </div>
                                <div class="col-sm-4">
                                    <textarea name="text[]" class="form-control" placeholder="{{__('db.Text')}}"></textarea>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-info mar-bot-30" id="custom-btn" type="button">+ {{__('db.Add More')}}</button>
                                <button type="submit" class="btn btn-primary mar-bot-30">{{__('db.submit')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="testimonial-table" class="table table-striped">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.Image')}}</th>
                    <th>{{__('db.name')}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
            <tbody id="tablecontents">
                @if($testimonials)
                @foreach($testimonials as $key=>$testimonial)
                <tr class="row1" data-id="{{$testimonial->id}}">
                    <td>{{$key}}</td>
                    <td><img src="{{asset('/public/landlord/images/testimonial')}}/{{ $testimonial->image }}"/></td>
                    <td>{{ $testimonial->name }}</td>
                    <td>
                        <button type="button" data-id="{{$testimonial->id}}" data-name="{{$testimonial->name}}" data-business-name="{{$testimonial->business_name}}" data-text="{{$testimonial->text}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i></button>
                        <form class="d-inline" method="post" action="{{ route('testimonial.delete', $testimonial->id) }}">
                        @csrf
                            <button type="submit" class="btn btn-link" onclick="return confirm('{{__('db.Are you sure you want to delete?')}}')"><i class="dripicons-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- edit Modal -->
    <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('testimonial.update')}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Testimonial')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}</small></p>
                    <form>
                        <div class="form-group">
                            <label><strong>{{__('db.Image')}} *</strong></label>
                            <input type="file" name="image" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Name')}} *</strong></label>
                            <input type="text" class="form-control" name="name" required />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Business Name')}} *</strong></label>
                            <input type="text" class="form-control" name="business_name" required />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Text')}} *</strong></label>
                            <textarea class="form-control" name="text"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="testimonial_id">
                            <input type="submit" value="{{__('db.Submit')}}" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </form>
        </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-testimonial-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-btn').on('click', function(){
        $("#editModal input[name='business_name']").val($(this).data('business_name'));
        $("#editModal input[name='name']").val($(this).data('name'));
        $("#editModal textarea[name='text']").html($(this).data('text'));
        $("#editModal input[name='testimonial_id']").val($(this).data('id'));
    });
    $('#custom-btn').on('click', function(){
        var count = $('#custom-field hr').length + 1;
        htmlText = '<div class="row"><div class="col-sm-2"><input type="file" name="image[]" class="form-control"/></div><div class="col-sm-4"><input type="text" name="name[]" class="form-control" placeholder="{{__('db.name')}}"/><input type="text" name="business_name[]" class="form-control" placeholder="{{__('db.Business name')}}"/></div><div class="col-sm-5"><textarea name="text[]" class="form-control" placeholder="{{__('db.text')}}"></textarea></div><div class="col-sm-1"><span class="input-group-btn delete-fields"><button class="btn btn-danger btn-lg delete-btn" type="button">X</button></span></div></div><hr>';
        $('#custom-field').append(htmlText);
    });

    $(document).on("click", ".delete-btn", function() {
        $(this).parent().parent().parent().next().remove();
        $(this).closest(".row").remove();
    });

    $('#testimonial-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi', selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                // customize: function(doc) {
                //     for (var i = 1; i < doc.content[1].table.body.length; i++) {
                //         if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                //             var imagehtml = doc.content[1].table.body[i][0].text;
                //             var regex = /<img.*?src=['"](.*?)['"]/;
                //             var src = regex.exec(imagehtml)[1];
                //             var tempImage = new Image();
                //             tempImage.src = src;
                //             var canvas = document.createElement("canvas");
                //             canvas.width = tempImage.width;
                //             canvas.height = tempImage.height;
                //             var ctx = canvas.getContext("2d");
                //             ctx.drawImage(tempImage, 0, 0);
                //             var imagedata = canvas.toDataURL("image/png");
                //             delete doc.content[1].table.body[i][0].text;
                //             doc.content[1].table.body[i][0].image = imagedata;
                //             doc.content[1].table.body[i][0].fit = [30, 30];
                //         }
                //     }
                // },
            },
            {
                extend: 'csv',
                text: 'CSV',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                }
            },
            {
                extend: 'colvis',
                text: 'Column visibility',
                columns: ':gt(0)'
            },
        ],
    } );

    $( "#tablecontents" ).sortable({
      items: "tr",
      cursor: 'move',
      opacity: 0.6,
      update: function() {
          sendOrderToServer();
      }
    });

    function sendOrderToServer() {
        var order = [];
        $('tr.row1').each(function(index,element) {
          order.push({
            id: $(this).attr('data-id'),
            position: index+1
          });
        });

        $.ajax({
          type: "POST",
          url: "{{ url('superadmin/testimonial-section/sort') }}",
          data: {
            order:order
          },
          success: function(response) {
              alert(response);
          }
        });

    }
</script>
@endpush
