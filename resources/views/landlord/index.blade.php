<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{$general_setting->developed_by}}" />
    <meta name="csrf-token" content="CmSeExxpkZmScDB9ArBZKMGKAyzPqnxEriplXWrS">
    <link rel="icon" type="image/png" href="{{ asset('landlord/images/logo/'.$general_setting->site_logo) }}" />
    <!-- Document Title -->
    <title>{{$general_setting->meta_title ?? 'Retail Nexis'}}</title>
    <!-- Links -->
    <meta name="description" content="{{$general_setting->meta_description ?? 'Buy Retail Nexis inventory management & POS SaaS php script'}}" />
    <meta property="og:url" content="{{url()->full()}}" />
    <meta property="og:title" content="{{$general_setting->og_title ?? 'Retail Nexis'}}" />
    <meta property="og:description" content="{{$general_setting->og_description ?? 'Buy Retail Nexis inventory management & POS SaaS php script'}}" />
    <meta property="og:image" content="{{ asset('landlord/images/og-image/'.($general_setting->og_image ?? 'retail-nexis-default-og.jpg')) }}" />

    <!-- Bootstrap CSS -->
    <link href="{{ asset('landlord/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>

    <!-- Plugins CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('landlord/css/plugins.css') }}">
    <noscript>
        <link href="{{ asset('landlord/css/plugins.css') }}" rel="stylesheet">
    </noscript>

    <!-- common style CSS -->
    <link id="switch-style" href="{{ asset('landlord/css/common-style-light.css') }}" rel="stylesheet">

    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    @if(isset($general_setting->fb_pixel_script))
    {!!$general_setting->fb_pixel_script!!}
    @endif

    <style>

    </style>
</head>

<body class="home">
    @if(session()->has('not_permitted'))
      <div class="alert alert-danger alert-dismissible text-center">{{ session()->get('not_permitted') }}</div>
    @endif
    <!--Header-->
    <!--Header Area starts-->
    @if(!config('app.demo_unlocked'))
    <div class="notice">
        <a target="_blank" href="https://tryonedigital.com/software/retail-nexis">Buy Retail Nexis with full source code</a>
    </div>
    @endif

    @if(config('app.demo_unlocked')==1)
    <div style="position:fixed;right:0;top:200px;z-index:99">
        <span id="light-theme" class="btn btn-light d-block"><i class="fa fa-sun-o"></i></span>
        <span id="dark-theme" class="btn btn-dark d-block"><i class="fa fa-moon-o"></i></span>
    </div>
    @endif

    <header id="header-middle" class="header-middle">

        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-7">
                    <div class="mobile-menu-icon d-lg-none"><i class="ti-menu"></i></div>
                    <div class="logo">
                        <a href="{{url('/')}}">
                            <img class="lazy" src="{{ asset('landlord/images/logo/'.$general_setting->site_logo) }}" alt="Brand logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-flex d-xl-flex middle-column justify-content-center">
                    <div id="main-menu" class="main-menu">
                        <ul class="pl-0">
                            <li><a href="{{url('/')}}#features">{{__('db.Features')}}</a></li>

                            <li><a href="{{url('/')}}#faq">{{__('db.FAQ')}}</a></li>

                            <li><a href="{{url('/')}}#packages">{{__('db.Pricing')}}</a></li>

                            <li><a href="{{url('/blog')}}">{{__('db.Blog')}}</a></li>

                            <li><a href="{{url('/')}}#contact">{{__('db.Contact')}}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-5" style="text-align:right">
                    <ul class="offset-menu-wrapper p-0 d-none d-lg-flex d-xl-flex">
                        <li>
                            <a class="button style2" href="#packages">{{__('db.Try Now')}}</a>
                        </li>
                    </ul>
                    <a class="button style2 d-lg-none" href="#packages">{{__('db.Try Now')}}</a>
                </div>
            </div>
        </div>
        <nav id="mobile-nav">
            <div class="mobile-menu-close-icon d-lg-none" styel="line-height:50px"><i class="ti-close"></i></div>
            <div class="container"></div>
        </nav>
    </header>

    <section class="hero mt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center hero-text mb-5">
                    <h1 class="heading">{{$hero->heading}}</h1>
                    <h2 class="sub-heading light h5 mb-5">{{$hero->sub_heading}}</h2>
                    <a href="#packages" class="button lg style2">{{$hero->button_text}}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="hero-img">
        <img src="{{ asset('landlord/images/'.$hero->image) }}" alt=""/>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-5">
                    <h2 class="heading">{{ __('db.Testimonials') }}</h2>
                </div>
            </div>
            <div class="swiper mySwiper swiper-container-horizontal swiper-container-autoheight" style="border-bottom:1px solid #ddd">
                <div class="swiper-wrapper" style="height: 348px;">
                @foreach($testimonials as $testimonial)
                    <div class="swiper-slide swiper-slide-active" style="width: 563px; margin-right: 50px;">
                        <div class="review">
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="review-text">
                                {!!$testimonial->text!!}
                            </div>
                            <div class="reviewer"><img src="{{ asset('landlord/images/testimonial/'.$testimonial->image) }}" alt="{{$testimonial->name}}" /> {{$testimonial->name}}@if($testimonial->business_name), {{$testimonial->business_name}}@endif</div>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="swiper-nav-next" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"><i class="ti-arrow-right"></i></div>
                <div class="swiper-nav-prev swiper-button-disabled" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="true"><i class="ti-arrow-left"></i></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </section>

    @if(count($modules) > 0)
    <section id="features" class="">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-5">
                    @if($module_description)
                        <h2 class="heading">{{$module_description->heading}}</h2>
                        {!! $module_description->sub_heading !!}
                    @else
                        <h2 class="heading">One App, all the features</h2>
                        <p class="lead mb-5">Retail Nexis is packed with all the features you will need to seamlessly run your business</p>
                    @endif
                </div>
                @foreach($modules as $module)
                <div class="col-md-4">
                    <div class="feature">
                        <div class="icon m-auto mb-3">
                            <i class="{{$module->icon}}"></i>
                        </div>
                        <h3>{{$module->name}}</h3>
                        <p>{{$module->description}}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(count($features) > 0)
    <section class="grey-bg">
        <div class="container">
            <div class="row">
                @foreach($features as $feature)
                <div class="col-md-3 feature2">
                    <div class="icon">
                        <i class="{{$feature->icon}}"></i>
                    </div>
                    <h4 class="h6">{{$feature->name}}</h4>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(count($faqs) > 0)
    <section id="faq" class="accordion pb-0" id="accordionExample">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-5">
                    @if($faq_description)
                        <h2 class="heading">{{$faq_description->heading}}</h2>
                        <p class="lead">{{$faq_description->sub_heading}}</p>
                    @else
                        <h2 class="heading">Frequently Asked Questions</h2>
                        <p class="lead">Have questions? we have answered common ones below.</p>
                    @endif
                </div>
                <div class="col-md-6 offset-md-3 mb-5">
                    @foreach($faqs as $key => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{'heading'.$key}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="{{'#collapse'.$key}}" aria-expanded="false" aria-controls="{{'collapse'.$key}}">
                            {{$faq->question}}
                        </button>
                        </h2>
                        <div id="{{'collapse'.$key}}" class="accordion-collapse collapse" aria-labelledby="{{'heading'.$key}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                {!!$faq->answer!!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <section id="packages"class="grey-bg">
        <div class="container">
            <div class="col-md-6 offset-md-3 text-center mb-5">
                <h2 class="heading">{{__('db.Pricing Plans')}}</h2>

                <ul class="nav nav-tabs pricing-tab" id="pricingTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly-tab-pane" type="button" role="tab" aria-controls="monthly-tab-pane" aria-selected="true">{{ __('db.Monthly') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly-tab-pane" type="button" role="tab" aria-controls="yearly-tab-pane" aria-selected="false">{{ __('db.Yearly') }} <span class="badge">Save 20%</span>
                      </button>
                    </li>
                </ul>

            </div>
            <div class="d-none d-lg-flex d-xl-flex justify-content-between mb-5">
                <div class="col" style="min-width: 300px;">
                    <div class="pricing">
                        <div class="sticker">
                            <div class="pricing-header">
                                <span class="h3">{{__('db.Plan')}}</span>
                            </div>
                            <div class="price">
                                <span class="h4">{{__('db.Price')}}</span>
                            </div>
                        </div>
                        <div class="pricing-details">
                            <p>{{__('db.Free Trial')}}</p>
                            @foreach ($all_features as $feature)
                            <p>{{ $feature['name'] }}</p>
                            @endforeach
                            <p>{{__('db.Number of Warehouses')}}</p>
                            <p>{{__('db.Number of Products')}}</p>
                            <p>{{__('db.Number of Invoices')}}</p>
                            <p>{{__('db.Number of User Account')}}</p>
                            <p>{{__('db.Number of Employees')}}</p>
                        </div>
                    </div>
                </div>
                @foreach($packages as $package)
                <?php
                    $package_features = json_decode($package->features);
                ?>
                <div class="col">
                    <div class="pricing">
                        <div class="sticker">
                            <div class="pricing-header">
                                <span class="h3">{{$package->name}}</span>
                            </div>
                            <div class="price">
                                <div>
                                    <span class="h4"><span class="currency-code">{{$general_setting->currency}}</span> <span class="package-price" data-monthly="{{$package->monthly_fee}}" data-yearly="{{$package->yearly_fee}}">{{$package->monthly_fee}}/month</span></span><br>
                                    @if($general_setting->disable_frontend_signup)
                                    <a class="button style2 mt-2" href="{{url('/')}}#contact">{{__('db.Contact')}}</a>
                                    @else
                                    <button  data-bs-toggle="modal" data-bs-target="#signupModal" data-free="{{$package->is_free_trial}}" data-package_id="{{$package->id}}" class="button style2 signup-btn mt-2">Sign Up</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="pricing-details">
                            @if($package->is_free_trial)
                                <p>{{$general_setting->free_trial_limit}} days</p>
                            @else
                                <p>N/A</p>
                            @endif

                            @foreach ($all_features as $key => $feature)
                                @if(in_array($key, $package_features))
                                    <p><i class="ti-check"></i></p>
                                @else
                                    <p><i class="ti-close"></i></p>
                                @endif
                            @endforeach

                            @if($package->number_of_warehouse)
                                <p>{{$package->number_of_warehouse}}</p>
                            @else
                                <p>{{__('db.Unlimited')}}</p>
                            @endif

                            @if($package->number_of_product)
                                <p>{{$package->number_of_product}}</p>
                            @else
                                <p>{{__('db.Unlimited')}}</p>
                            @endif

                            @if($package->number_of_invoice)
                                <p>{{$package->number_of_invoice}}</p>
                            @else
                                <p>{{__('db.Unlimited')}}</p>
                            @endif

                            @if($package->number_of_user_account)
                                <p>{{$package->number_of_user_account}}</p>
                            @else
                                <p>{{__('db.Unlimited')}}</p>
                            @endif

                            @if($package->number_of_employee)
                                <p>{{$package->number_of_employee}}</p>
                            @else
                                <p>{{__('db.Unlimited')}}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @foreach($packages as $package)
            <?php
                $package_features = json_decode($package->features);
            ?>
            <div class="pricing-m d-block d-lg-none d-xl-none mb-5">
                <div class="d-flex justify-content-between">
                    <div class="pricing-header">
                        <span class="h3">{{$package->name}}</span>
                    </div>
                    <div class="price">
                        <span class="h4"><span class="currency-code">{{$general_setting->currency}}</span> <span class="package-price" data-monthly="{{$package->monthly_fee}}" data-yearly="{{$package->yearly_fee}}">{{$package->monthly_fee}}/month</span></span>
                    </div>
                </div>
                <div class="price">
                    @if($general_setting->disable_frontend_signup)
                    <a class="button style2 mt-2" href="{{url('/')}}#contact">{{__('db.Contact')}}</a>
                    @else
                    <button  data-bs-toggle="modal" data-bs-target="#signupModal" data-free="{{$package->is_free_trial}}" data-package_id="{{$package->id}}" class="button style2 d-block w-100 signup-btn mt-2">Sign Up</button>
                    @endif
                </div>
                <div class="d-flex justify-content-between">
                    <div class="pricing-details">
                        <p>{{__('db.Free Trial')}}</p>
                        @foreach ($all_features as $feature)
                        <p>{{ $feature['name'] }}</p>
                        @endforeach
                        <p>{{__('db.Number of Warehouses')}}</p>
                        <p>{{__('db.Number of Products')}}</p>
                        <p>{{__('db.Number of Invoices')}}</p>
                        <p>{{__('db.Number of User Account')}}</p>
                        <p>{{__('db.Number of Employees')}}</p>
                    </div>
                    <div class="pricing-details">
                        @if($package->is_free_trial)
                            <p>{{$general_setting->free_trial_limit}} days</p>
                        @else
                            <p>N/A</p>
                        @endif

                        @foreach ($all_features as $key => $feature)
                            @if(in_array($key, $package_features))
                                <p><i class="ti-check"></i></p>
                            @else
                                <p><i class="ti-close"></i></p>
                            @endif
                        @endforeach

                        @if($package->number_of_warehouse)
                            <p>{{$package->number_of_warehouse}}</p>
                        @else
                            <p>{{__('db.Unlimited')}}</p>
                        @endif

                        @if($package->number_of_product)
                            <p>{{$package->number_of_product}}</p>
                        @else
                            <p>{{__('db.Unlimited')}}</p>
                        @endif

                        @if($package->number_of_invoice)
                            <p>{{$package->number_of_invoice}}</p>
                        @else
                            <p>{{__('db.Unlimited')}}</p>
                        @endif

                        @if($package->number_of_user_account)
                            <p>{{$package->number_of_user_account}}</p>
                        @else
                            <p>{{__('db.Unlimited')}}</p>
                        @endif

                        @if($package->number_of_employee)
                            <p>{{$package->number_of_employee}}</p>
                        @else
                            <p>{{__('db.Unlimited')}}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    @if(count($blogs) > 0)
    <section id="blog">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-5">
                    <h2 class="heading">{{ __('db.Blog') }}</h2>
                </div>
                @foreach($blogs as $blog)
                <div class="col-md-4">
                    <a href="{{url('/blog')}}/{{$blog->slug}}">
                        <div class="blog-item">
                            <img src="{{ asset('landlord/images/blog/'.$blog->featured_image) }}" alt="{{$blog->title}}"/>
                            <h4 class="mt-3">{{$blog->title}}</h4>
                        </div>
                    </a>
                </div>
                @endforeach
                <div class="col-md-6 offset-md-3 text-center mt-3 mb-5">
                    <a href="{{url('blog')}}" class="button style1">{{ __('db.All Blogs') }}</a>
                </div>
            </div>
        </div>

    </section>
    @endif

    <section id="contact" class="grey-bg">
        <div class="container mb-5">
            <div class="row">
                <div class="col-md-5">
                    <h3 class="heading mt-5">{{ __('db.Contact Us') }}</h3>
                    <p class="lead contact-details"><i class="fa fa-phone"></i> {{$general_setting->phone}}</p>
                    <p class="lead contact-details"><i class="fa fa-envelope"></i> {{$general_setting->email}}</p>
                    <hr>
                    <h3 class="heading">{{ __('db.Connect with Us') }}</h3>
                    <ul class="footer-social p-0 pt-3 pb-3">
                        @foreach($socials as $social)
                        <li>
                            <a href="{{$social->link}}"><i class="{{$social->icon}}"></i></a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 offset-md-1">
                    <form action="{{route('contactForm')}}" method="POST"  class="form contact-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="name"  placeholder="name..." required>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="phone"  placeholder="contact number..." required>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="email"  placeholder="email..." required>
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control" name="message"  placeholder="your message" required></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <p id="contact-waiting-msg" class="mb-3"></p>
                                <input id="contact-submit-btn" type="submit" class="button style2 d-block w-100" value="{{__('db.submit')}}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer section Starts-->
    <div class="footer-wrapper">
        <div class="container">
            @if(!config('app.demo_unlocked'))
            <div class="mt-5 mb-5 cta">
                <h3 class="h1 mb-5">Start your software subscription business</h3>
                <a class="button lg style2" href="https://tryonedigital.com/software/retail-nexis">Get Retail Nexis</a>
            </div>
            @endif
            <hr>
            <div class="d-flex justify-content-between mt-5">
                <div class="footer-links">
                    @foreach($pages as $page)
                    <a href="{{url('page/'.$page->slug)}}">{{$page->title}}</a>
                    @endforeach
                </div>
                <div class="footer-bottom">
                    <p class="copyright">&copy; {{$general_setting->site_title}} {{date('Y')}}. All rights reserved</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer section Ends-->

    {{-- <a href="https://wa.me/8801924756759" target="_blank">
        <div class="contact-button" style="background-color: #9fe870;border-radius: 50%;bottom: 20px;height: 70px;right: 20px;width: 70px;font-size: 30px;color: #f5f6f7;text-align: center;line-height: 64px;position: fixed;z-index: 999;">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#101010" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"></path>
            </svg>

        </div>
    </a> --}}

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop"><i class="ti-arrow-up"></i></a>
    <!--Scroll to top ends-->

    <div class="body__overlay"></div>

    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" style="padding: 30px;">
                    <div class="col-md-8 offset-md-2">
                        <div class="text-center mb-3">
                            @if($tenant_signup_description)
                                <h2 class="heading">{{$tenant_signup_description->heading}}</h2>
                                <p class="lead mb-3">{{$tenant_signup_description->sub_heading}}</p>
                            @else
                                <h2 class="heading">Sign Up</h2>
                                <p class="lead mb-3">Retail Nexis is packed with all the features you'll need to seamlessly run your business</p>
                            @endif
                        </div>
                        <form action="{{route('tenant.checkout')}}" method="POST"  class="form row customer-signup-form">
                            @csrf
                            <div class="col-6">
                                <input type="hidden" name="package_id" value="1">
                                <input type="hidden" name="subscription_type" value="monthly">
                                <input type="hidden" name="price" value="">
                                <input class="form-control" type="text" name="company_name"  placeholder="company name..." required>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="phone_number"  placeholder="contact number..." required>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group mt-3">
                                    <input class="form-control mt-0" type="email" name="email" id="email" placeholder="Email..." required>
                                    <button type="button" id="send-otp-btn" class="btn btn-primary">Verify</button>
                                </div>
                                <small id="email-error" class="text-danger d-none"></small>
                            </div>
                            <!-- OTP Input Field -->
                            <div class="col-md-12 d-none" id="otp-section">
                                <div class="input-group mt-3">
                                    <input class="form-control mt-0" type="text" name="otp" id="otp" placeholder="Enter OTP">
                                    <button type="button" id="verify-otp-btn" class="btn btn-success">Verify OTP</button>
                                </div>
                                <small id="otp-error" class="text-danger d-none">Invalid OTP</small>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="name"  placeholder="username..." required>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="password" name="password" id="password" placeholder="password..." required>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password..." required>
                                <small id="confirm-password-error" class="text-danger d-none">{{__('db.Passwords do not match!')}}</small>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group mt-3">
                                    <input class="form-control mt-0" type="text" name="tenant"  placeholder="subdomain..." aria-label="subdomain..." aria-describedby="basic-addon2" required>
                                <span class="input-group-text" id="basic-addon2">{{'@'.env('CENTRAL_DOMAIN')}}</span>
                                </div>
                            </div>
                            @if($general_setting->dedicated_ip)
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="custom_domain"  placeholder="Set custom domain if you have any...">
                                <p>You have to put {{$general_setting->dedicated_ip}} as an A record on your domain control panel. It may take 24 hours to propagate. <a id="custom-domain-details" href="" style="color:red">{{__('db.Click here for details')}}</a></p>
                            </div>
                            @endif
                            <div class="col-12 mt-3">
                                <p id="waiting-msg" class="text-danger mb-3"></p>
                                <input id="submit-btn" type="submit" class="button lg style2 d-block w-100" value="{{__('db.submit')}}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- supplier modal -->
    <div id="custom-domain-details-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{__('db.Setting up Dedicated IP')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('landlord/images/setup_custom_domain.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- end supplier modal -->


    <!--Plugin js -->
    <script src="{{ asset('landlord/js/plugin.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
    <script src="{{ asset('vendor/jquery/jquery-ui.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('landlord/js/sweetalert2@11.js')}}"></script>
    <!-- Main js -->
    <script src="{{ asset('landlord/js/main.js')}}"></script>
    <!--Payment gateway js -->
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('js/payment/razorpay.js') }}"></script>

    <script>
        let targetURL = "{{ url('/payment/razorpay/pay/confirm')}}";
        let cancelURL = "{{ url('payment/razorpay/pay/cancel')}}";
        let redirectURL = "{{ url('/payment_success')}}";
        let redirectURLAfterCancel = "{{ url('/payment_cancel')}}";

        $("div.alert").delay(3000).slideUp(800);
        var public_key = <?php echo json_encode($general_setting->stripe_public_key)?>;
        var active_payment_gateway = <?php echo json_encode($general_setting->active_payment_gateway)?>;
        (function ($) {
            "use strict";

            $('.banner-slide-up').on('click', function () {
                $(this).parent().slideUp();
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $(document).ready(function () {
                $('#newsletter-modal').modal('toggle');
            });

            $(".signup-btn").on("click", function () {
                $('input[name=package_id]').val($(this).data('package_id'));
                if($('input[name=subscription_type]').val() == 'monthly') {
                    $('input[name=price]').val($(this).parent().parent().find('.package-price').data('monthly'));
                } else {
                    $('input[name=price]').val($(this).parent().parent().find('.package-price').data('yearly'));
                }
                // $('html, body').animate({
                //     scrollTop: $("#customer-signup").offset().top
                // });
            });

            $("#yearly-tab").on('click', function(){
                $('input[name=subscription_type]').val('yearly');

                $(".package-price").each(function(){
                    var plan = $(this).data('yearly')+'/year';
                    $(this).html(plan);
                })
            })
            $("#monthly-tab").on('click', function(){
                $('input[name=subscription_type]').val('monthly');
                $(".package-price").each(function(){
                    var plan = $(this).data('monthly')+'/month';
                    $(this).html(plan);
                })
            })

            var verified = false;
            // Send OTP
            $('#send-otp-btn').click(function () {
                var email = $('#email').val().trim();
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailPattern.test(email)) {
                    $('#email-error').removeClass('d-none').text('Please enter a valid email');
                    return;
                }

                $('#email-error').addClass('d-none');
                $(this).prop('disabled', true).text('Sending...');

                $.ajax({
                    url: "{{ route('send.otp') }}", // Route to send OTP
                    type: 'POST',
                    data: {
                        email: email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#send-otp-btn').text('OTP Sent');
                            $('#otp-section').removeClass('d-none'); // Show OTP input
                        }
                        else {
                            $('#email-error').removeClass('d-none').text('Contact with Admin');
                            $('#send-otp-btn').prop('disabled', false).text('Verify');
                        }
                    },
                    error: function (xhr) {
                        $('#email-error').removeClass('d-none').text('Contact with Admin');
                        $('#send-otp-btn').prop('disabled', false).text('Verify');
                    }
                });
            });

            // Verify OTP
            $('#verify-otp-btn').click(function () {
                var otp = $('#otp').val();
                var email = $('#email').val();

                if (otp == '') {
                    $('#otp-error').removeClass('d-none').text('Please enter OTP');
                    return;
                }

                $('#otp-error').addClass('d-none');
                $(this).prop('disabled', true).text('Verifying...');

                $.ajax({
                    url: "{{ route('verify.otp') }}", // Route to verify OTP
                    type: 'POST',
                    data: {
                        email: email,
                        otp: otp,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#verify-otp-btn').text('Verified').prop('disabled', true);
                            verified = true; // Set verification flag
                        } else {
                            $('#otp-error').removeClass('d-none').text('Invalid OTP');
                            $('#verify-otp-btn').prop('disabled', false).text('Verify OTP');
                        }
                    }
                });
            });

            $('#email').on('input', function() {
                $('#email-error').addClass('d-none');
            });

            $('input[name=tenant]').on('input', function () {
                var tenant = $(this).val();
                var letters = /^[a-zA-Z0-9]+$/;
                if(!letters.test(tenant)) {
                    alert('Tenant name must be alpha numeric(a-z and 0-9)!');
                    tenant = tenant.substring(0, tenant.length-1);
                    $('input[name=tenant]').val(tenant);
                }
            });

            $('#password, #confirm_password').on('input', function() {
                $('#confirm-password-error').addClass('d-none');
            });

            $(document).on('submit', '.customer-signup-form', function(e) {
                var password = $('#password').val();
                var confirmPassword = $('#confirm_password').val();

                if (!verified) {
                    $('#email-error').removeClass('d-none').text('Please verify your email before submitting.');
                    e.preventDefault();
                }
                else if (password !== confirmPassword) {
                    $('#confirm-password-error').removeClass('d-none');
                    e.preventDefault();
                }
                else {
                    $('#submit-btn').prop('disabled', true);
                    $('p#waiting-msg').text('Please wait. It will take some few seconds. System will redirect you to the tenant url automatically.');
                }
            });

            $('a#custom-domain-details').click(function(e) {
                e.preventDefault();
                $('#custom-domain-details-modal').modal('show');
            });

            //Search field
            $('#search_field').hide();

            $(document).ready(function () {
                $('#searchText').keyup(function () {
                    var txt = $(this).val();
                    if (txt != '') {
                        $('#search_field').show();
                        $('#result').html('<li>loading...</li>');
                        $.ajax({
                            url: "data_ajax_search",
                            type: "GET",
                            data: {
                                search_txt: txt
                            },
                            success: function (data) {
                                $('#search_field').show();
                                $('#result').empty().html(data);
                            }
                        })
                    } else if (txt.length === 0) {
                        $('#search_field').hide();
                    } else {
                        $('#search_field').hide();
                        $('#result').empty();
                    }
                })
            });

            $('#stripeContent').hide();

            $(window).on('load', function () {

                $('.lazy').Lazy();
            });

        })(jQuery);
    </script>
    <script>
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
    @if(isset($general_setting->ga_script))
    {!!$general_setting->ga_script!!}
    @endif

    @if(isset($general_setting->chat_script))
    {!!$general_setting->chat_script!!}
    @endif

    @if(config('app.demo_unlocked')==1)
    <script>
        $('#light-theme').on('click',function(){
            var css = $('#switch-style').attr('href');
            css = css.replace('dark','light');
            $('#switch-style').attr("href", css);
        })

        $('#dark-theme').on('click',function(){
            var css = $('#switch-style').attr('href');
            css = css.replace('light','dark');
            $('#switch-style').attr("href", css);
        })
    </script>
    @endif
</body>
</html>
