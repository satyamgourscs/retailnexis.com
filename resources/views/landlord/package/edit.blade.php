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
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Add Package')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['packages.update', $packageData->id], 'method' => 'put', 'id' => 'package-form']) !!}
                            <div class="row">
                                <div class="col-md-3 form-group">
                                	<label>{{__('db.name')}} *</label>
                                    <input type="text" name="name" required class="form-control" value="{{$packageData->name}}">
                                </div>
                                <div class="col-md-2 mt-4">
                                    <input type="checkbox" id="is_free" name="is_free" @if($packageData->monthly_fee == 0 && $packageData->yearly_fee == 0){{'checked'}}@endif>
                                    <label>{{__('db.Free')}}</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <input type="checkbox" id="is_free_trial" name="is_free_trial" value="1" @if($packageData->is_free_trial){{'checked'}}@endif>
                                    <label>{{__('db.Free Trial')}}</label>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{__('db.Monthly Fee')}} *</label>
                                    <input type="number" id="monthly_fee" name="monthly_fee" required class="form-control" value="{{$packageData->monthly_fee}}">
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{__('db.Yearly Fee')}} *</label>
                                    <input type="number" id="yearly_fee" name="yearly_fee" required class="form-control" value="{{$packageData->yearly_fee}}">
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{__('db.Number of Warehouses')}}</label>
                                    <input type="number" name="number_of_warehouse" class="form-control" value="{{$packageData->number_of_warehouse}}" required>
                                    <p>0 = {{__('db.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{__('db.Number of Products')}}</label>
                                    <input type="number" name="number_of_product" class="form-control" value="{{$packageData->number_of_product}}" required>
                                    <p>0 = {{__('db.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{__('db.Number of Invoices')}}</label>
                                    <input type="number" name="number_of_invoice" class="form-control" value="{{$packageData->number_of_invoice}}" required>
                                    <p>0 = {{__('db.Infinity')}}</p>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{__('db.Number of User Account')}}</label>
                                    <input type="number" name="number_of_user_account" class="form-control" value="{{$packageData->number_of_user_account}}" required>
                                    <p>0 = {{__('db.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{__('db.Number of Employees')}}</label>
                                    <input type="number" name="number_of_employee" class="form-control" value="{{$packageData->number_of_employee}}" required>
                                    <p>0 = {{__('db.Infinity')}}</p>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{__('db.Features')}}</label>
                                    <ul style="list-style-type: none; margin-left: -30px;">
                                        @foreach ($features as $key => $feature)
                                        <li><input type="checkbox" class="features" name="features[]" value="{{ $key }}" {{ $feature['default'] ? 'checked disabled' : (in_array($key, json_decode($packageData->features)) ? 'checked' : '') }}>&nbsp; {{$feature['name']}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="checkbox" name="is_update_existing" value="1">&nbsp; <strong>{{__('db.Update existing clients who are using this package')}}</strong>
                                </div>
                                <input type="hidden" name="permission_id">
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">{{__('db.submit')}}</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#package").siblings('a').attr('aria-expanded','true');
    $("ul#package").addClass("show");
    $("ul#package #package-create-menu").addClass("active");

    setPermission();

    $(".features").on("change", function() {
        const val = $(this).val();

        if (val == 'ecommerce') {
            if ($(this).is(':checked')) {
                $('.features[value="woocommerce"]').prop('checked', false).prop('disabled', true);
            } else {
                $('.features[value="woocommerce"]').prop('disabled', false);
            }
        }
        else if (val == 'woocommerce') {
            if ($(this).is(':checked')) {
                $('.features[value="ecommerce"]').prop('checked', false).prop('disabled', true);
            } else {
                $('.features[value="ecommerce"]').prop('disabled', false);
            }
        }

        setPermission();
    });

    function setPermission() {
        var features = @json($features);
        permission_ids = '';
        $(".features").each(function(index) {
            if ($(this).is(':checked')) {
                permission_ids += features[$(this).val()]['permission_ids'];
            }
        });
        if(permission_ids) {
            // Step 1: Remove the last comma
            permission_ids = permission_ids.substring(0, permission_ids.length - 1);
            // Step 2: Split into array
            var idsArray = permission_ids.split(',');
            // Step 3: Remove duplicates
            idsArray = [...new Set(idsArray)];
            // Step 4: Join back into string
            permission_ids = idsArray.join(',');
        }
        $("input[name=permission_id]").val(permission_ids);
    }

    $(document).on('submit', '#package-form', function(e) {
	    $(".features").prop("disabled", false);
	});

    isFreeCheckedOrNot($('#is_free'));
    $('#is_free').change(function () {
        isFreeCheckedOrNot($(this));
    });
    function isFreeCheckedOrNot(element) {
        if (element.is(':checked')) {
            $('#is_free_trial').prop('checked', false);
            $('#monthly_fee').val(0);
            $('#yearly_fee').val(0);
            $('#is_free_trial, #monthly_fee, #yearly_fee').hide().siblings('label').hide();
        }
        else {
            $('#is_free_trial').prop('checked', true);
            $('#is_free_trial, #monthly_fee, #yearly_fee').show().siblings('label').show();
        }
    }
</script>
@endpush
