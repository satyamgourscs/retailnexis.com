<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{$general_setting->developed_by}}" />
    <meta name="csrf-token" content="CmSeExxpkZmScDB9ArBZKMGKAyzPqnxEriplXWrS">

    <!-- Document Title -->
    <title>{{$general_setting->meta_title ?? 'Nexa Technologies'}}</title>
    <meta name="description" content="{{$general_setting->meta_description ?? 'Nexa Technologies — multi-tenant SaaS POS & inventory management. Sell subscriptions under your own brand.'}}" />
    <!-- Links -->
    <link rel="icon" type="image/png" href="{{url('landlord/images/logo', $general_setting->site_logo)}}" />

    <!-- Bootstrap CSS -->
    <link href="landlord/css/bootstrap.min.css" rel="stylesheet">

    <!-- Plugins CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="landlord/css/plugins.css">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="landlord/css/plugins.css">
    </noscript>

    <!-- common style CSS -->
    <link href="landlord/css/common-style.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>
    <!-- google fonts-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap">
    <noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;600&display=swap">
    </noscript>


    <style>
        :root {
            --theme-color: #f16232;
        }
    </style>


</head>

<body>
    @if(session()->has('not_permitted'))
      <div class="alert alert-danger alert-dismissible text-center">{{ session()->get('not_permitted') }}</div>
    @endif

    <section class="mt-0 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 hero-text mt-3 mb-5">
                    <center><a href="{{url('/')}}"><img src="{{url('landlord/images/logo', $general_setting->site_logo)}}" alt="Brand logo" style="max-width: 150px; margin-bottom: 30px"></a></center>
                    @if(!$payment_gateway_count)
                        <h1 class="heading h2 text-center">Your Subscription has expired! Please contact with superadmin</h1>
                    @else
                        <form action="{{route('renewSubscription')}}" method="POST" class="renew-subscription-form">
                            @csrf
                            <h1 class="heading h2 text-center">Your Subscription has expired!</h1>
                            <div class="row">
                                <div class="col-md-4 offset-md-2 form-group">
                                    <label>{{__('db.Subscription Type')}} *</label>
                                    <div class="form-check">
                                      <input type="radio" name="subscription_type" value="monthly" class="form-check-input" id="subscription-type-1" checked>
                                      <label class="form-check-label" for="subscription-type-1">
                                        Monthly
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input type="radio" name="subscription_type" value="yearly" class="form-check-input" id="subscription-type-2">
                                      <label class="form-check-label" for="subscription-type-2">
                                        Yearly
                                      </label>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-2 form-group">
                                    <label>{{__('db.Package')}} *</label>
                                    @foreach($packages as $key => $package)
                                    <div class="form-check">
                                      <input type="radio" name="package_id" value="{{$package->id}}" class="form-check-input" id="{{'package-type-'.$package->id}}" @if(!$key){{'checked'}}@endif>
                                      <label class="form-check-label" for="{{'package-type-'.$package->id}}">
                                        {{$package->name}}
                                      </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="col-md-8 offset-md-2">
                                    <div class="input-group mt-3">
                                        <input class="form-control mt-0" type="text" name="id" value="{{$subdomain}}" required placeholder="Type your subdomain to renew..." aria-label="subdomain..." aria-describedby="basic-addon2">
                                      <span class="input-group-text" id="basic-addon2">{{'@'.config('app.central_domain')}}</span>
                                    </div>
                                </div>
                                <div class="col-md-8 offset-md-2 mt-2 coupon-section">
                                    <div class="input-group mt-3">
                                        <input class="form-control mt-0 coupon-code" type="text" name="coupon_code" placeholder="Enter Coupon Code...">
                                        <span class="input-group-text apply-coupon" style="cursor: pointer;">Apply</span>
                                    </div>
                                </div>
                                <div class="col-md-8 offset-md-2 mt-3">
                                    <p class="mb-2">Payable Amount: <span id="payable-amount"></span></p>
                                    <input type="hidden" name="price"/>
                                    <input id="submit-btn" type="submit" class="button style1 d-block w-100" value="{{__('db.submit')}}">
                                </div>
                            </div>
                        </form>
                    @endif
                    <br>
                    <h5 class="text-center">You can call at <a href="tel:{{$general_setting->phone}}"><span>{{$general_setting->phone}}</span></a></h5>
                    <h5 class="text-center">Contact for details at - <a class="button style1" href="mailto:{{$general_setting->email}}">{{$general_setting->email}}</a></h5>
                </div>
                <!-- <div class="col-md-8 offset-md-2">
                    <img class="hero-img" src="images/preview.png" alt=""/>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Footer section Starts-->
    <div class="footer-wrapper pt-0">
        <div class="container">
            <div class="footer-bottom">
                <ul class="footer-social p-0 pt-3 pb-3">
                    @foreach($socials as $social)
                    <li>
                        <a href="{{$social->link}}"><i class="{{$social->icon}}"></i></a>
                    </li>
                    @endforeach
                </ul>
                <p class="copyright">&copy; {{$general_setting->meta_title.' '.date("Y")}}. All rights reserved</p>
            </div>
        </div>
    </div>
    <!-- Footer section Ends-->

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop"><i class="ti-arrow-up"></i></a>
    <!--Scroll to top ends-->

    <div class="body__overlay"></div>

    <!-- Cookie consent Starts-->


    <!-- Cookie consent Ends-->


    <!--Plugin js -->
    <script src="landlord/js/plugin.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>

    <!-- Main js -->
    <script src="landlord/js/main.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <script>
        var package_list = <?php echo json_encode($packages) ?>;
        var coupon_list = <?php echo json_encode($coupon_list) ?>;
        if(!coupon_list.length)
            $(".coupon-section").addClass('d-none');
        var payable_amount;
        calculateSubscriptionPrice();

        $("input[name=subscription_type]").on("change", function() {
            calculateSubscriptionPrice();
        });

        $("input[name=package_id]").on("change", function() {
            calculateSubscriptionPrice();
        });

        $(".apply-coupon").on("click", function() {
            var code = $('input[name=coupon_code]').val();
            for(var i = 0; i < coupon_list.length; i++) {
                if(code == coupon_list[i].code) {
                    var price = payable_amount;
                    if(coupon_list[i].type == 'percentage') {
                        price = price - (price * (coupon_list[i].amount / 100));
                    }
                    else {
                        price = price - coupon_list[i].amount;
                    }
                    $('input[name=price]').val(price);
                    $("#payable-amount").text(price);
                    $('.coupon-code').prop('disabled', true);
                    alert('Congratulation! You got discounts.');
                    break;
                }
            }
        });

        function calculateSubscriptionPrice() {
           subscriptionType = $("input[name=subscription_type]:checked").val();
           packageId = $("input[name=package_id]:checked").val();
           for(i = 0; i < package_list.length; i++) {
                if(package_list[i].id == packageId) {
                    if(subscriptionType == 'monthly') {
                        payable_amount = package_list[i].monthly_fee;
                    }
                    else {
                        payable_amount = package_list[i].yearly_fee;
                    }
                    $("input[name=price]").val(payable_amount);
                    $("#payable-amount").text(payable_amount);
                }
           }
        }
    </script>
</body>
</html>
