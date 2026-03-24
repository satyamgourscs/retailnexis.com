@extends('landlord.layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message1'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message1') }}</div>
@endif
@if(session()->has('message2'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message2') }}</div>
@endif
@if(session()->has('message3'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message3') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Update User Profile')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <form method="POST" action="{{ route('user.superadminProfileUpdate', ['id' => $lims_user_data->id], false) }}">
                            @csrf
                            @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.UserName')}} *</strong> </label>
                                    <input type="text" name="name" value="{{$lims_user_data->name}}" required class="form-control" />
                                    @if($errors->has('name'))
                                    <span>
                                       <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{__('db.Email')}} *</strong> </label>
                                    <input type="email" name="email" value="{{$lims_user_data->email}}" required class="form-control">
                                    @if($errors->has('email'))
                                    <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{__('db.Phone Number')}} *</strong> </label>
                                    <input type="text" name="phone" value="{{$lims_user_data->phone}}" required class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>{{__('db.Company Name')}}</strong> </label>
                                    <input type="text" name="company_name" value="{{$lims_user_data->company_name}}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Change Password')}}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.superadminPassword', ['id' => $lims_user_data->id], false) }}">
                            @csrf
                            @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Current Password')}} *</strong> </label>
                                    <input type="password" name="current_pass" required class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>{{__('db.New Password')}} *</strong> </label>
                                    <input type="password" name="new_pass" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{__('db.Confirm Password')}} *</strong> </label>
                                    <input type="password" name="confirm_pass" id="confirm_pass" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="registrationFormAlert" id="divCheckPasswordMatch">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
<script type="text/javascript">
    $("#superadmin-profile-menu").addClass("active");



    $('#confirm_pass').on('input', function(){

        if($('input[name="new_pass"]').val() != $('input[name="confirm_pass"]').val())
            $("#divCheckPasswordMatch").html("Password doesn't match!");
        else
            $("#divCheckPasswordMatch").html("Password matches!");

    });
</script>
@endpush
