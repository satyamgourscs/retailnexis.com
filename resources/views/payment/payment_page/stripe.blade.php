@extends('payment.master')
@section('payment')
<style>
    .stripe-input{
        background: #f0f2f5;
        border: 1px solid #ddd !important;
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
        color: #212121;
        padding: 15px;
        height: 50px;
        margin-top: 5px;
    }
    .stripe-input:focus {
        background: #fff;
        border: 1px solid #010ed0 !important;
        box-shadow: none;
    }
</style>
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

                    <form method="post" class="mb-3 require-validation payment-form" action="{{ url('/payment/stripe/pay/confirm')}}" id="stripePaymentForm" data-cc-on-file="false">
                        @csrf
                        <input type="hidden" name="stripeToken">
                        <input type="hidden" name="requestData" value="{{ $requestData }}">
                        <input type="hidden" name="central_domain" value="{{env('CENTRAL_DOMAIN')}}">
                    
                        <div class="row form-row mt-4" id="stripe_details">
                            <div class="col-6 form-group">
                                <label for="card-number">Card Number *</label>
                                <div id="card-number" class="stripe-input"></div>
                            </div>
                            <div class="col-3 form-group">
                                <label for="card-expiry">Expiry Date *</label>
                                <div id="card-expiry" class="stripe-input"></div>
                            </div>
                            <div class="col-3 form-group">
                                <label for="card-cvc">CVC *</label>
                                <div id="card-cvc" class="stripe-input"></div>
                            </div>
                            <div class="error hide">
                                <div class="alert"></div>
                            </div>
                        </div>
                    
                        <div class="mt-4 d-grid gap-2 mx-auto">
                            <button type="submit" id="payNowBtn" class="btn btn-outline-success">Pay Now</button>
                        </div>
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
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    $(function () {
        const stripe = Stripe("{{$stripe_public_key}}");
        const elements = stripe.elements();
    
        const style = {
            base: {
                color: "#32325d",
                fontSize: "16px",
                fontFamily: "'Helvetica Neue', Helvetica, sans-serif",
                "::placeholder": {
                    color: "#666",
                },
            },
            invalid: {
                color: "#fa755a",
                iconColor: "#fa755a",
            },
        };
    
        const cardNumber = elements.create("cardNumber", { style });
        const cardExpiry = elements.create("cardExpiry", { style });
        const cardCvc = elements.create("cardCvc", { style });
    
        cardNumber.mount("#card-number");
        cardExpiry.mount("#card-expiry");
        cardCvc.mount("#card-cvc");
    
        const $form = $(".payment-form");
    
        $form.on("submit", async function (e) {
            e.preventDefault();
            $('#payNowBtn').prop('disabled', true).text('Processing your payment...');
    
            $('.error').addClass('hide').find('.alert').text('');
        
            var requestData = JSON.parse(@json($requestData));
            var centralDomain = "{{ env('CENTRAL_DOMAIN') }}";
            var successUrl;
            if (requestData.tenant) {
                successUrl = 'https://' + requestData.tenant + '.' + centralDomain;
            } else {
                successUrl = 'https://' + requestData.id + '.' + centralDomain;
            }

            
    
            const { error, paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
                billing_details: {
                    name: $('input[name="name"]').val() || 'Anonymous',
                    email: $('input[name="email"]').val() || '',
                },
            });
    
            if (error) {
                $('.error').removeClass('hide').find('.alert').text(error.message);
                $('#payNowBtn').prop('disabled', false).text('Try Again');
                return;
            }
    
            const formData = $form.serializeArray();
    
            formData.push({ name: 'paymentMethodId', value: paymentMethod.id });
            formData.push({ name: 'successUrl', value: successUrl });
    
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $.param(formData),
                success: function (response) {
                    if (response.success) {
                        try {
                            window.top.location.href = successUrl;
                        } catch (e) {
                            try {
                                window.open(successUrl, '_blank', 'noopener');
                            } catch (e2) {}
                        }
                    } else {
                        $('.error').removeClass('hide').find('.alert').text(response.message || 'Something went wrong!');
                        $('#payNowBtn').prop('disabled', false).text('Try Again');
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('.error').removeClass('hide').find('.alert').text(errorMessage);
                    $('#payNowBtn').prop('disabled', false).text('Try Again');
                }
            });
        });
    });

</script>
@endpush
