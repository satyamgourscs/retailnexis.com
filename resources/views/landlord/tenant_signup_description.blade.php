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
            {!! Form::open(['route' => 'tenantSignupDescription.store', 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Tenant Signup Description')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.language')}} *</label>
                                    <select name="language_id" class="form-control">
                                        @foreach($language_all as $language)
                                        <option value="{{$language->id}}">{{$language->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>{{__('db.Heading')}} *</label>
                                    <input type="text" name="heading" class="form-control" value="@if($tenant_signup_description){{$tenant_signup_description->heading}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Sub Heading')}} *</label>
                                    <input type="text" name="sub_heading" class="form-control" value="@if($tenant_signup_description){{$tenant_signup_description->sub_heading}}@endif" required />
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.Save')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')

<script>
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-tenant-signup-menu").addClass("active");

    var lang_id = <?php echo json_encode($lang_id); ?>;
    $("select[name=language_id]").val(lang_id);
    $('.selectpicker').selectpicker('refresh');
</script>

@endpush
