@extends('landlord.layout.main') @section('content')

@push('custom-css')
<style>
    .change-theme-color {
        align-items: center;
        cursor: pointer;
        display: flex;
        line-height:2
    }
    .change-theme-color span {
        border-radius: 3px;
        height:15px;
        margin-right: 10px;
        width:15px;
    }
    .custom-switch {
        padding-left: .5rem;
    }

    .custom-switch .custom-control-label::before {
        left: -2.25rem;
        width: 1.75rem;
        pointer-events: all;
        border-radius: .5rem;
    }

    .custom-switch .custom-control-label::after {
        top: calc(.25rem + 2px);
        left: calc(-2.25rem + 2px);
        width: calc(1rem - 4px);
        height: calc(1rem - 4px);
        background-color: #adb5bd;
        border-radius: .5rem;
        transition: background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-transform .15s ease-in-out;
        transition: transform .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: transform .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-transform .15s ease-in-out;
    }

    .custom-control-input:checked~.custom-control-label::before {
        color: #fff;
        border-color: #007bff;
        background-color: #007bff;
    }

    .custom-switch .custom-control-input:checked~.custom-control-label::after {
        background-color: #fff;
        -webkit-transform: translateX(.75rem);
        transform: translateX(.75rem);
    }
</style>
@endpush

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
            {!! Form::open(['url' => route('superadminGeneralSetting.store', [], false), 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#gs_collapse" aria-expanded="true" aria-controls="gs_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{__('db.General Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.System Title')}} *</label>
                                    <input type="text" name="site_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->site_title}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.System Logo')}} *</label>
                                    <input type="file" name="site_logo" class="form-control" value=""/>
                                </div>
                                @if($errors->has('site_logo'))
                                <span>
                                    <strong>{{ $errors->first('site_logo') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Phone Number')}} *</label>
                                    <input type="text" name="phone" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->phone}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Email')}} *</label>
                                    <input type="text" name="email" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->email}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('db.Free Trial Limit')}} *</label>
                                    <input type="number" name="free_trial_limit" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->free_trial_limit}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('db.Currency Code')}} *</label>
                                    <input type="text" name="currency" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->currency}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Frontend Layout</label>
                                    <div class="form-check">
                                        <input name="frontend_layout" id="regular" class="form-check-input" type="radio" value="regular" @if($lims_general_setting_data->frontend_layout == 'regular') checked @endif required>
                                        <label class="form-check-label" for="regular">
                                            Regular
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="frontend_layout" id="custom" class="form-check-input" type="radio" value="custom" @if($lims_general_setting_data->frontend_layout == 'custom') checked @endif  required>
                                        <label class="form-check-label" for="custom">
                                            Custom
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Date Format')}} *</label>
                                    @if($lims_general_setting_data)
                                    <input type="hidden" name="date_format_hidden" value="{{$lims_general_setting_data->date_format}}">
                                    @endif
                                    <select name="date_format" class="selectpicker form-control">
                                        <option value="d-m-Y"> dd-mm-yyy</option>
                                        <option value="d/m/Y"> dd/mm/yyy</option>
                                        <option value="d.m.Y"> dd.mm.yyy</option>
                                        <option value="m-d-Y"> mm-dd-yyy</option>
                                        <option value="m/d/Y"> mm/dd/yyy</option>
                                        <option value="m.d.Y"> mm.dd.yyy</option>
                                        <option value="Y-m-d"> yyy-mm-dd</option>
                                        <option value="Y/m/d"> yyy/mm/dd</option>
                                        <option value="Y.m.d"> yyy.mm.dd</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Dedicated IP')}}</label> <i class="dripicons-question" data-toggle="tooltip" title="{{__('db.If you purchase dedicated IP from your hosting service provider put it down here. By doing this your client will be able to set custom domain.')}}"></i>
                                    <input type="text" name="dedicated_ip" class="form-control" value="{{$lims_general_setting_data->dedicated_ip}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-3 mb-3">
                                        <input type="checkbox" class="custom-control-input ml-2" id="disable_frontend_signup" name="disable_frontend_signup" value="1" @if($lims_general_setting_data->disable_frontend_signup == 1) checked @endif>
                                        <label class="custom-control-label fw-500" for="disable_frontend_signup">{{__('db.Disable frontend signup')}}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-3 mb-3">
                                        <input type="checkbox" class="custom-control-input ml-2" id="disable_tenant_support_tickets" name="disable_tenant_support_tickets" value="1" @if($lims_general_setting_data->disable_tenant_support_tickets == 1) checked @endif>
                                        <label class="custom-control-label fw-500" for="disable_tenant_support_tickets">{{__('db.disable_tenant_support_tickets')}}</label>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Theme Color')}} *</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default btn-block dropdown-toggle" type="button" data-toggle="dropdown"><span id="def_color"><span style="background-color:{{$lims_general_setting_data->theme_color}};width:15px;height:15px"></span> {{$lims_general_setting_data->theme_color}}</span>
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li class="change-theme-color" data-color="#404eed"> <span style="background-color:#404eed;"></span> #404eed</li>
                                            <li class="change-theme-color" data-color="#f51e46"> <span style="background-color:#f51e46;"></span> #f51e46</li>
                                            <li class="change-theme-color" data-color="#fa9928"> <span style="background-color:#fa9928;"></span> #fa9928</li>
                                            <li class="change-theme-color" data-color="#fd6602"> <span style="background-color:#fd6602;"></span> #fd6602</li>
                                            <li class="change-theme-color" data-color="#59b210"> <span style="background-color:#59b210;"></span> #59b210</li>
                                            <li class="change-theme-color" data-color="#ff749f"> <span style="background-color:#ff749f;"></span> #ff749f</li>
                                            <li class="change-theme-color" data-color="#f8008c"> <span style="background-color:#f8008c;"></span> #f8008c</li>
                                            <li class="change-theme-color" data-color="#6453f7"> <span style="background-color:#6453f7;"></span> #6453f7</li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="theme_color" class="form-control" value="{{$lims_general_setting_data->theme_color}}" required />
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('db.Developed By')}}</label>
                                    <input type="text" name="developed_by" class="form-control" value="{{$lims_general_setting_data->developed_by}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#ps_collapse" aria-expanded="true" aria-controls="ps_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{__('db.Payment Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="ps_collapse">

                        <div class="row">
                            @foreach($payment_gateways as $pg)
                            <div class="col-md-12 mt-3 mb-3">
                                <h4 class="d-flex justify-content-between">
                                    {{$pg->name}} {{__('db.Details')}}

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" @if($pg->active == 1) checked @endif class="activate custom-control-input" id="{{$pg->name}}">
                                        <label class="custom-control-label" for="{{$pg->name}}">Activate {{$pg->name}}</label>
                                        <input type="hidden" name="active[]" value="{{$pg->active}}">
                                    </div>
                                </h4>
                                <hr>
                                <input type="hidden" name="pg_name[]" class="form-control" value="{{$pg->name}}" />
                                @php
                                $lines = explode(';',$pg->details);
                                $keys = explode(',', $lines[0]);
                                $vals = explode(',', $lines[1]);

                                $results = array_combine($keys, $vals);
                                @endphp
                                @foreach ($results as $key => $value)
                                <div class="form-group">
                                    <label>{{$key}}</label>
                                    @if($key == 'Mode')
                                        <select name="{{$pg->name.'_'.str_replace(' ','_',$key)}}" class="selectpicker form-control">
                                            <option @if($value == 'sandbox') selected @endif value="sandbox">Sandbox</option>
                                            <option @if($value == 'live') selected @endif value="live">Live</option>
                                        </select>
                                    @else
                                        <input type="text" name="{{$pg->name.'_'.str_replace(' ','_',$key)}}" class="form-control" value="{{$value}}" />
                                    @endif

                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#seos_collapse" aria-expanded="true" aria-controls="seos_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{__('db.SEO Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="seos_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Meta Title')}} * ({{__('db.50-60 characters')}})</label>
                                    <input type="text" name="meta_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->meta_title}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Meta Description')}} * {{__('db.150-160 characters')}}</label>
                                    <textarea name="meta_description" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->meta_description}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.og Title')}} *</label>
                                    <input type="text" name="og_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->og_title}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.og Description')}} *</label>
                                    <textarea name="og_description" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->og_description}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.og Image')}} *</label>
                                    <input type="file" name="og_image" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#as_collapse" aria-expanded="true" aria-controls="as_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{__('db.Analytics Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="as_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Google Analytics Script')}} *</label>
                                    <textarea name="ga_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->ga_script}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Facebook Pixel Script')}} *</label>
                                    <textarea name="fb_pixel_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->fb_pixel_script}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('db.Chat Script')}} *</label>
                                    <textarea name="chat_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->chat_script}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
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
<script type="text/javascript">
    $("#general-setting-menu").addClass("active");
    $('.selectpicker').selectpicker('refresh');
    $('[data-toggle="tooltip"]').tooltip();
    $('.card-header').on('click', function() {
        $(this).find('.icon').toggleClass('dripicons-chevron-down dripicons-chevron-up');
    });

    if ($('.activate').is(':checked')) {
        $(this).siblings('input[type="text"]').val(1);
    } else {
        $(this).siblings('input[type="text"]').val(0);
    }
    $(document).on('click', '.activate', function(){
        if ($(this).is(':checked')) {
            $(this).siblings('input[type="hidden"]').val(1);
        } else if (!$(this).is(':checked')) {
            $(this).siblings('input[type="hidden"]').val(0);
        }
    })

    $(document).on('click', '.change-theme-color', function(){
        var color = $(this).data('color');
        var def_color = '<span style="background-color:'+color+';width:15px;height:15px"></span> '+color;
        $('input[name=theme_color]').val(color);
        $('#def_color').html(def_color)
    })

</script>
@endpush
