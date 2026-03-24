@extends('payment.master')
@section('payment')
    <div class="row">
        <div class="col-12">
            <h1 class="page-title h2 text-center uppercase mt-1 mb-5">{{ $paymentMethodName }}
                <small>
                    ({{ number_format((float) $totalAmount, 2, '.', '') }})
                </small>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid" id="errorMessage"></div>

                    <form action="{{ route('payment.process') }}" method="post" id="paypalPaymentForm">
                        <input type="hidden" name="requestData" value="{{ $requestData }}">
                        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                        <input type="hidden" name="central_domain" value="{{ env('CENTRAL_DOMAIN') }}">

                        <div id="paypal-button-container"></div>

                        <div class="mt-3 d-grid gap-2 mx-auto">
                            <button type="button" id="payCancelBtn" class="btn btn-outline-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
@endsection

@push('payment_scripts')
    <script src="https://www.paypal.com/sdk/js?client-id={{ $paypal_client_id }}&currency={{ $currency }}"
        data-namespace="paypal_sdk"></script>

    <script type="text/javascript">
        var currency = @json($currency);
        var requestData = JSON.parse(@json($requestData));
        var centralDomain = "{{ env('CENTRAL_DOMAIN') }}";
        if (requestData.tenant) {
            successUrl = 'https://' + requestData.tenant + '.' + centralDomain;
        }
        else {
            successUrl = 'https://' + requestData.id + '.' + centralDomain;
        }
        let targetURL = "{{ url('/payment/paypal/pay/confirm') }}";
        let cancelURL = "{{ url('payment/paypal/pay/cancel') }}";
        let redirectURL = successUrl;
        let redirectURLAfterCancel = "{{ url('/') }}";
    </script>
    <script type="text/javascript" src="{{ asset('js/payment/paypal.js') }}"></script>
@endpush
