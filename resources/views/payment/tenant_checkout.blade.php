@extends('payment.master')
@section('payment')


        <form action="{{route('payment.process')}}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$request->id}}">
            <input type="hidden" name="package_id" value="{{$request->package_id}}">
            <input type="hidden" name="subscription_type" value="{{$request->subscription_type}}">
            <input type="hidden" name="price" value="{{$request->price}}">
            <input type="hidden" name="company_name" value="{{$request->company_name}}">
            <input type="hidden" name="phone_number" value="{{$request->phone_number}}">
            <input type="hidden" name="email" value="{{$request->email}}">
            <input type="hidden" name="name" value="{{$request->name}}">
            <input type="hidden" name="password" value="{{$request->password}}">
            <input type="hidden" name="tenant" value="{{$request->tenant}}">
            <input type="hidden" name="expiry_date" value="{{date('Y-m-d', strtotime('+'.$request->numberOfDaysToExpired.' days'))}}">
            <input type="hidden" name="permission_ids" value="{{$request->permission_ids}}">
            <input type="hidden" name="abandoned_permission_ids" value="{{$request->abandoned_permission_ids}}">
            <input type="hidden" name="renewal" value="{{$request->renewal}}">
            <input type="hidden" name="modules" value="{{$request->modules}}">

            <h4 class="mt-3">Payable Amount : {{$request->price}}</h4>
            <div class="mt-5">
                @foreach($payment_gateways as $pg)
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='{{ Str::lower($pg->name) }}' value="{{ Str::lower($pg->name) }}">
                    <span class="sm-heading">{{ Str::ucfirst($pg->name) }}</span>
                </label>
                @endforeach
            </div>
            @if($terms_and_condition_page)
                <p>
                    <input type="checkbox" required> I have read <a target="_blank" href="{{url('page/'.$terms_and_condition_page->slug)}}">Terms & Conditions</a>
                </p>
            @endif
            <div class="mar-top-30 mt-3">
                <button type="submit" class="btn btn-success text-center w-50">Payment Procced</button>
            </div>
        </form>

@endsection

