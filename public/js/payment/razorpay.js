$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".razorpay-payment-button").hide();
});

$("#payNowBtn").click(function(){
    $("#payNowBtn").text('Please wait...');
});

$("#payCancelBtn").click(function(){
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
