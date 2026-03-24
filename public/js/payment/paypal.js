$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Render the PayPal button into #paypal-button-container
paypal_sdk.Buttons({
    // Call your server to set up the transaction
    createOrder: function(data, actions) {
        console.log(currency);
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: $('input[name="total_amount"]').val(),
                    currency_code: currency,
                }
            }]
        });
    },

    // Call your server to finalize the transaction
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            if (details.status=="COMPLETED") {
                console.log($('#paypalPaymentForm').serialize());
                $.post({
                    url: targetURL,
                    data: $('#paypalPaymentForm').serialize(),
                    success: function (response) {
                        console.log(response);
                        if (response.errors) {
                            let html = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            ${response.errors}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>`;
                            $('#errorMessage').html(html);
                        }
                        else if(response.success){
                            try {
                                window.top.location.href = redirectURL;
                            } catch (e) {
                                try {
                                    window.open(redirectURL, '_blank', 'noopener');
                                } catch (e2) {}
                            }
                        }
                    }
                });
            }
        });
    }
}).render('#paypal-button-container');

$("#payCancelBtn").click(function(e){
    e.preventDefault();

    if (confirm('Are you sure to cancel ?')) {
        $.ajax({
            url: cancelURL,
            type: 'POST',
            data: {},
            dataType: 'JSON',
            success: function (data) {
                try {
                    window.top.location.href = redirectURLAfterCancel;
                } catch (e) {
                    try {
                        window.open(redirectURLAfterCancel, '_blank', 'noopener');
                    } catch (e2) {}
                }
            }
        });
    }
});
