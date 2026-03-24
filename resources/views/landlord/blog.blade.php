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
            {!! Form::open(['route' => 'blog.store', 'files' => true, 'method' => 'post']) !!}
                <button type="button" class="btn btn-success mb-3" data-toggle="collapse" href="#collapse" aria-expanded="false" aria-controls="collapse"/>{{__('db.Add Post')}}</button>
                <div class="collapse" id="collapse">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{__('db.Blog Section')}}</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                            <div id="custom-field">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <input type="file" name="featured_image" class="form-control" required/>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <input type="text" name="title" class="form-control" placeholder="{{__('db.Blog Title')}}"/>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <textarea name="description" class="form-control textarea" placeholder="{{__('db.Description')}}"></textarea>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <input type="text" name="meta_title" class="form-control" placeholder="{{__('db.Meta Title')}}"/>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <textarea name="meta_description" class="form-control" placeholder="{{__('db.Meta Description')}}"></textarea>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <input type="text" name="og_title" class="form-control" placeholder="{{__('db.Open graph Title')}}"/>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <textarea name="og_description" class="form-control" placeholder="{{__('db.Open Graph Description')}}"></textarea>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary mar-bot-30">{{__('db.Submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="blog-table" class="table table-striped">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.Image')}}</th>
                    <th>{{__('db.Blog Title')}}</th>
                    <th class="not-exported">{{__('db.Action')}}</th>
                </tr>
            </thead>
            <tbody id="tablecontents">
                @if($blogs)
                @foreach($blogs as $key=>$blog)
                <tr class="row1" data-id="{{$blog->id}}">
                    <td>{{$key}}</td>
                    <td><img style="width:70px" src="{{asset('/public/landlord/images/blog')}}/{{ $blog->featured_image }}"/></td>
                    <td>{{ $blog->title }}</td>
                    <td>
                        <button type="button" data-id="{{$blog->id}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i></button>
                        <form class="d-inline" method="post" action="{{ route('blog.delete', $blog->id) }}">
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
            <form method="post" action="{{route('blog.update')}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Blog')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}</small></p>
                    <form>
                        <div class="form-group">
                            <label><strong>{{__('db.Image')}} *</strong></label>
                            <input type="file" name="featured_image" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Title')}} *</strong></label>
                            <input type="text" class="form-control" name="title" required />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Description')}} *</strong></label>
                            <textarea id="edit-description" class="form-control textarea" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Meta Title')}}</strong></label>
                            <input type="text" class="form-control" name="meta_title" />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Meta Description')}}</strong></label>
                            <textarea class="form-control" name="meta_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Open Graph Title')}}</strong></label>
                            <input type="text" class="form-control" name="og_title" />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Open Graph Description')}}</strong></label>
                            <textarea class="form-control" name="og_description"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="blog_id">
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
    $("ul#cms #cms-blog-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-btn').on('click', function(){
        var id = $(this).data('id');
        $.get("{{url('/superadmin/blog-section/edit')}}/"+ id, function(data) {
            $("#editModal input[name='blog_id']").val(data['id']);
            $("#editModal input[name='title']").val(data['title']);
            $("#editModal input[name='meta_title']").val(data['meta_title']);
            $("#editModal input[name='og_title']").val(data['og_title']);
            tinyMCE.get('edit-description').setContent(data['description']);
            $("#editModal textarea[name='meta_description']").html(data['meta_description']);
            $("#editModal textarea[name='og_description']").html(data['og_description']);
        });
    });

    tinymce.init({
      selector: '.textarea',
      height: 130,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor textcolor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code wordcount'
      ],
      toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
      branding:false
    });

    $(document).on("click", ".delete-btn", function() {
        $(this).closest("tr").remove();
    });

    $('#blog-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2, 3]
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
          url: "{{ url('superadmin/blog-section/sort') }}",
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
