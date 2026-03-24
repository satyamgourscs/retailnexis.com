<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{$general_setting->developed_by}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{url('landlord/images/logo', $general_setting->site_logo)}}" />
    <!-- Document Title -->
    <title>{{$general_setting->meta_title ?? 'Nexa Technologies'}}</title>
    <!-- Links -->
    <meta name="description" content="{{$general_setting->meta_description ?? 'Nexa Technologies — SaaS POS & inventory management platform'}}" />
    <meta property="og:url" content="{{url()->full()}}" />
    <meta property="og:title" content="{{$general_setting->og_title ?? 'Nexa Technologies'}}" />
    <meta property="og:description" content="{{$general_setting->og_description ?? 'Nexa Technologies — SaaS POS & inventory management platform'}}" />
    <meta property="og:image" content="{{url('/landlord/images/og-image')}}/{{$general_setting->og_image ?? 'saleprosaas.jpg'}}" />

    <!-- Bootstrap CSS -->
    <link href="{{url('/')}}/landlord/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>

    <!-- Plugins CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{url('/')}}/landlord/css/plugins.css">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{url('/')}}/landlord/css/plugins.css">
    </noscript>

    <!-- common style CSS -->
    <link id="switch-style" href="{{url('/')}}/landlord/css/common-style-light.css" rel="stylesheet">
    <!-- Nova (Lindy UI Kit) — design system; no duplicate Bootstrap loaded -->
    <link href="{{url('/')}}/landlord/nova/css/LineIcons.2.0.css?v=1" rel="stylesheet">
    <link href="{{url('/')}}/landlord/nova/css/lindy-uikit.css?v=1" rel="stylesheet">
    <link href="{{url('/')}}/landlord/css/nova-landing-bridge.css?v=1" rel="stylesheet">
    <link href="{{url('/')}}/landlord/css/landing-saas-premium.css?v=1" rel="stylesheet">

    @if(isset($general_setting->fb_pixel_script))
    {!!$general_setting->fb_pixel_script!!}
    @endif

    <style>

    </style>
</head>

<body class="home nova-landing">
    @if(session()->has('not_permitted'))
      <div class="alert alert-danger alert-dismissible text-center">{{ session()->get('not_permitted') }}</div>
    @endif
    <!--Header-->
    <!--Header Area starts-->
    @if(env('USER_VERIFIED')==1)
    <div style="display:none;position:fixed;right:0;top:200px;z-index:99">
        <span id="light-theme" class="btn btn-light d-block"><i class="fa fa-sun-o"></i></span>
        <span id="dark-theme" class="btn btn-dark d-block"><i class="fa fa-moon-o"></i></span>
    </div>
    @endif

    {{-- Single dynamic header + hero --}}
    <section class="hero-section-wrapper-5">
        <header id="header-middle" class="header-middle saas-header">
            <div class="container">
                <nav class="navbar navbar-expand-lg saas-navbar">
                        <a class="navbar-brand saas-navbar-brand m-0 p-0" href="{{url('/')}}">
                            <img class="lazy" src="{{url('landlord/images/logo', $general_setting->site_logo)}}" alt="{{ $general_setting->site_title ?? 'Brand logo' }}">
                        </a>

                        <button class="navbar-toggler saas-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbarDesk" aria-controls="landingNavbarDesk" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="saas-navbar-toggler__line"></span>
                            <span class="saas-navbar-toggler__line"></span>
                            <span class="saas-navbar-toggler__line"></span>
                        </button>

                        <div class="collapse navbar-collapse middle-column" id="landingNavbarDesk">
                            <div id="main-menu" class="main-menu ms-lg-auto">
                                <ul class="navbar-nav align-items-lg-center">
                                    <li class="nav-item"><a class="page-scroll nav-link" href="{{url('/')}}#features">{{__('db.Features')}}</a></li>
                                    <li class="nav-item"><a class="page-scroll nav-link" href="{{url('/')}}#faq">{{__('db.FAQ')}}</a></li>
                                    <li class="nav-item"><a class="page-scroll nav-link" href="{{url('/')}}#packages">{{__('db.Pricing')}}</a></li>
                                    <li class="nav-item"><a class="page-scroll nav-link" href="{{url('/blog')}}">{{__('db.Blog')}}</a></li>
                                    <li class="nav-item"><a class="page-scroll nav-link" href="{{url('/')}}#contact">{{__('db.Contact')}}</a></li>
                                    <li class="nav-item d-lg-none mt-2">
                                        <a href="#packages" class="button saas-nav-cta w-100 text-center">{{__('db.Try Now')}}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="d-none d-lg-flex ms-lg-3">
                                <a href="#packages" class="button saas-nav-cta">{{__('db.Try Now')}}</a>
                            </div>
                        </div>
                </nav>
            </div>
        </header>

        {{-- Hero: single 2-col grid; decorative blobs scoped inside .saas-hero-block only --}}
        <div class="saas-hero-block">
            <div class="saas-hero-blobs" aria-hidden="true">
                <span class="saas-hero-blob saas-hero-blob--a"></span>
                <span class="saas-hero-blob saas-hero-blob--b"></span>
            </div>
            <div class="container saas-hero-container">
                <div class="row align-items-center gy-5">
                    <div class="col-12 col-lg-6">
                        <div class="saas-hero-copy">
                            <h1 class="hero-title">{{$hero->heading}}</h1>
                            <p class="hero-lead">{{$hero->sub_heading}}</p>
                            <a href="#packages" class="button button-lg saas-hero-cta">{{$hero->button_text}} <i class="lni lni-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="saas-hero-visual d-flex justify-content-center justify-content-lg-end align-items-center">
                            <div class="saas-hero-mockup w-100">
                                <div class="saas-hero-mockup__inner">
                                    <img src="{{url('/landlord/images')}}/{{$hero->image}}" alt="{{ $hero->heading }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="testimonial-section bg-white pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
                    <div class="section-title text-center mb-60">
                        <h3 class="mb-15">{{ __('db.Testimonials') }}</h3>
                    </div>
                </div>
            </div>
            <div class="swiper mySwiper nova-swiper-testimonials swiper-container-horizontal swiper-container-autoheight">
                <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                    <div class="swiper-slide h-100">
                        <div class="review nova-testimonial-card d-flex flex-column h-100 w-100">
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
                            <div class="reviewer d-flex align-items-center mt-auto">
                                <img src="{{asset('/landlord/images/testimonial')}}/{{$testimonial->image}}" alt="{{$testimonial->name}}">
                                <span>{{$testimonial->name}}@if($testimonial->business_name), {{$testimonial->business_name}}@endif</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="swiper-nav-next" tabindex="0" role="button" aria-label="Next slide"><i class="ti-arrow-right"></i></div>
                <div class="swiper-nav-prev" tabindex="0" role="button" aria-label="Previous slide"><i class="ti-arrow-left"></i></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </section>

    @if(count($modules) > 0)
    <section id="features" class="feature-section feature-style-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
                    <div class="section-title text-center mb-60">
                        @if($module_description)
                            <h3 class="mb-15">{{$module_description->heading}}</h3>
                            <div class="module-desc">{!! $module_description->sub_heading !!}</div>
                        @else
                            <h3 class="mb-15">One App, all the features</h3>
                            <p>Nexa Technologies is packed with all the features you will need to seamlessly run your business</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row g-4">
                @foreach($modules as $module)
                <div class="col-12 col-md-6 col-lg-3">
                    <div id="feature-{{ \Illuminate\Support\Str::slug($module->name) }}-{{ $loop->index }}" class="single-feature saas-feature-card">
                        <div class="icon">
                            <i class="{{$module->icon}}"></i>
                            <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z" fill="#EBF4FF"/></svg>
                        </div>
                        <div class="content">
                            <h5>{{$module->name}}</h5>
                            <p>{{$module->description}}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(count($features) > 0)
    <section class="nova-mini-features pt-80 pb-80">
        <div class="container">
            <div class="row">
                @foreach($features as $feature)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="nova-mini-card">
                        <div class="icon"><i class="{{$feature->icon}}"></i></div>
                        <h6>{{$feature->name}}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(count($faqs) > 0)
    <section id="faq" class="faq-section saas-faq-section pt-100 pb-100 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-10">
                    <div class="section-title faq-heading-block text-center mb-50">
                        @if($faq_description)
                            <h3 class="mb-15">{{$faq_description->heading}}</h3>
                            <p class="faq-subtitle">{{$faq_description->sub_heading}}</p>
                        @else
                            <h3 class="mb-15">Frequently Asked Questions</h3>
                            <p class="faq-subtitle">Have questions? we have answered common ones below.</p>
                        @endif
                    </div>
                </div>
            </div>
            @php
                $faqItems = $faqs->take(6)->values();
                $faqLeft = $faqItems->take(3)->values();
                $faqRight = $faqItems->slice(3, 3)->values();
            @endphp
            <div class="row g-4 justify-content-center">
                <div class="col-xl-5 col-lg-6 col-md-10">
                    <div class="accordion nova-accordion saas-faq-accordion" id="accordionLeft">
                    @foreach($faqLeft as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{'headingLeft'.$index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="{{'#collapseLeft'.$index}}" aria-expanded="false" aria-controls="{{'collapseLeft'.$index}}">
                            {{$faq->question}}
                        </button>
                        </h2>
                        <div id="{{'collapseLeft'.$index}}" class="accordion-collapse collapse" aria-labelledby="{{'headingLeft'.$index}}" data-bs-parent="#accordionLeft">
                            <div class="accordion-body">
                                {!!$faq->answer!!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-10">
                    <div class="accordion nova-accordion saas-faq-accordion" id="accordionRight">
                    @foreach($faqRight as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{'headingRight'.$index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="{{'#collapseRight'.$index}}" aria-expanded="false" aria-controls="{{'collapseRight'.$index}}">
                            {{$faq->question}}
                        </button>
                        </h2>
                        <div id="{{'collapseRight'.$index}}" class="accordion-collapse collapse" aria-labelledby="{{'headingRight'.$index}}" data-bs-parent="#accordionRight">
                            <div class="accordion-body">
                                {!!$faq->answer!!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section id="packages" class="pricing-section pricing-style-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-6 col-lg-8 col-md-10 text-center mb-60">
                    <div class="section-title">
                        <h3 class="mb-15">{{__('db.Pricing Plans')}}</h3>
                    </div>
                    <ul class="nav nav-tabs pricing-tab" id="pricingTab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly-tab-pane" type="button" role="tab" aria-controls="monthly-tab-pane" aria-selected="true">{{ __('db.Monthly') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly-tab-pane" type="button" role="tab" aria-controls="yearly-tab-pane" aria-selected="false">{{ __('db.Yearly') }} <span class="badge rounded-pill nova-pricing-badge">Save 20%</span></button>
                        </li>
                    </ul>
                    {{-- Empty panes satisfy Bootstrap tab targets (pricing amounts still driven by existing JS) --}}
                    <div class="tab-content visually-hidden" id="pricingTabPanes" aria-hidden="true">
                        <div class="tab-pane fade show active" id="monthly-tab-pane" role="tabpanel"></div>
                        <div class="tab-pane fade" id="yearly-tab-pane" role="tabpanel"></div>
                    </div>
                </div>
            </div>
            <div class="row g-4 justify-content-center saas-pricing-desktop d-none d-lg-flex">
                @foreach($packages as $package)
                @php
                    $package_features = json_decode($package->features);
                    $saasPopular = $loop->iteration === (int) round(($loop->count + 1) / 2);
                @endphp
                <div class="col-xl-4 col-lg-4 d-flex">
                    <div class="saas-pricing-card w-100 {{ $saasPopular ? 'is-popular' : '' }}">
                        @if($saasPopular)
                        <span class="saas-pricing-card__badge">{{ __('db.Most Popular') }}</span>
                        @endif
                        <h3 class="saas-pricing-card__title">{{$package->name}}</h3>
                        <div class="saas-pricing-card__price-block">
                            <span class="saas-pricing-card__currency">{{$general_setting->currency}}</span>
                            <span class="package-price saas-pricing-card__amount" data-monthly="{{$package->monthly_fee}}" data-yearly="{{$package->yearly_fee}}">{{$package->monthly_fee}}/month</span>
                        </div>
                        <ul class="saas-pricing-card__list">
                            <li>
                                <span class="feat-label">{{__('db.Free Trial')}}</span>
                                <span class="feat-val">@if($package->is_free_trial){{$general_setting->free_trial_limit}} days @else N/A @endif</span>
                            </li>
                            @foreach ($all_features as $key => $feature)
                            <li>
                                <span class="feat-label">{{ $feature['name'] }}</span>
                                <span class="feat-icon">
                                    @if(in_array($key, $package_features))
                                    <i class="ti-check"></i>
                                    @else
                                    <i class="ti-close"></i>
                                    @endif
                                </span>
                            </li>
                            @endforeach
                            <li>
                                <span class="feat-label">{{__('db.Number of Warehouses')}}</span>
                                <span class="feat-val">@if($package->number_of_warehouse){{$package->number_of_warehouse}}@else{{__('db.Unlimited')}}@endif</span>
                            </li>
                            <li>
                                <span class="feat-label">{{__('db.Number of Products')}}</span>
                                <span class="feat-val">@if($package->number_of_product){{$package->number_of_product}}@else{{__('db.Unlimited')}}@endif</span>
                            </li>
                            <li>
                                <span class="feat-label">{{__('db.Number of Invoices')}}</span>
                                <span class="feat-val">@if($package->number_of_invoice){{$package->number_of_invoice}}@else{{__('db.Unlimited')}}@endif</span>
                            </li>
                            <li>
                                <span class="feat-label">{{__('db.Number of User Account')}}</span>
                                <span class="feat-val">@if($package->number_of_user_account){{$package->number_of_user_account}}@else{{__('db.Unlimited')}}@endif</span>
                            </li>
                            <li>
                                <span class="feat-label">{{__('db.Number of Employees')}}</span>
                                <span class="feat-val">@if($package->number_of_employee){{$package->number_of_employee}}@else{{__('db.Unlimited')}}@endif</span>
                            </li>
                        </ul>
                        @if($general_setting->disable_frontend_signup)
                        <a class="button saas-pricing-card__btn saas-pricing-card__btn--outline" href="{{url('/')}}#contact">{{__('db.Contact')}}</a>
                        @else
                        <button type="button" data-bs-toggle="modal" data-bs-target="#signupModal" data-free="{{$package->is_free_trial}}" data-package_id="{{$package->id}}" class="button signup-btn saas-pricing-card__btn">Sign Up</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-lg-none">
            @foreach($packages as $package)
            @php
                $package_features = json_decode($package->features);
                $saasPopularM = $loop->iteration === (int) round(($loop->count + 1) / 2);
            @endphp
            <div class="saas-pricing-card saas-pricing-card--mobile {{ $saasPopularM ? 'is-popular' : '' }}">
                @if($saasPopularM)
                <span class="saas-pricing-card__badge">{{ __('db.Most Popular') }}</span>
                @endif
                <h3 class="saas-pricing-card__title">{{$package->name}}</h3>
                <div class="saas-pricing-card__price-block">
                    <span class="saas-pricing-card__currency">{{$general_setting->currency}}</span>
                    <span class="package-price saas-pricing-card__amount" data-monthly="{{$package->monthly_fee}}" data-yearly="{{$package->yearly_fee}}">{{$package->monthly_fee}}/month</span>
                </div>
                <ul class="saas-pricing-card__list">
                    <li>
                        <span class="feat-label">{{__('db.Free Trial')}}</span>
                        <span class="feat-val">@if($package->is_free_trial){{$general_setting->free_trial_limit}} days @else N/A @endif</span>
                    </li>
                    @foreach ($all_features as $key => $feature)
                    <li>
                        <span class="feat-label">{{ $feature['name'] }}</span>
                        <span class="feat-icon">
                            @if(in_array($key, $package_features))
                            <i class="ti-check"></i>
                            @else
                            <i class="ti-close"></i>
                            @endif
                        </span>
                    </li>
                    @endforeach
                    <li>
                        <span class="feat-label">{{__('db.Number of Warehouses')}}</span>
                        <span class="feat-val">@if($package->number_of_warehouse){{$package->number_of_warehouse}}@else{{__('db.Unlimited')}}@endif</span>
                    </li>
                    <li>
                        <span class="feat-label">{{__('db.Number of Products')}}</span>
                        <span class="feat-val">@if($package->number_of_product){{$package->number_of_product}}@else{{__('db.Unlimited')}}@endif</span>
                    </li>
                    <li>
                        <span class="feat-label">{{__('db.Number of Invoices')}}</span>
                        <span class="feat-val">@if($package->number_of_invoice){{$package->number_of_invoice}}@else{{__('db.Unlimited')}}@endif</span>
                    </li>
                    <li>
                        <span class="feat-label">{{__('db.Number of User Account')}}</span>
                        <span class="feat-val">@if($package->number_of_user_account){{$package->number_of_user_account}}@else{{__('db.Unlimited')}}@endif</span>
                    </li>
                    <li>
                        <span class="feat-label">{{__('db.Number of Employees')}}</span>
                        <span class="feat-val">@if($package->number_of_employee){{$package->number_of_employee}}@else{{__('db.Unlimited')}}@endif</span>
                    </li>
                </ul>
                @if($general_setting->disable_frontend_signup)
                <a class="button saas-pricing-card__btn saas-pricing-card__btn--outline" href="{{url('/')}}#contact">{{__('db.Contact')}}</a>
                @else
                <button type="button" data-bs-toggle="modal" data-bs-target="#signupModal" data-free="{{$package->is_free_trial}}" data-package_id="{{$package->id}}" class="button signup-btn saas-pricing-card__btn">Sign Up</button>
                @endif
            </div>
            @endforeach
            </div>
        </div>
    </section>

    @if(count($blogs) > 0)
    <section id="blog" class="feature-section feature-style-5 pt-50 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
                    <div class="section-title text-center mb-60">
                        <h3 class="mb-15">{{ __('db.Blog') }}</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach($blogs as $blog)
                <div class="col-lg-4 col-md-6">
                    <a href="{{url('/blog')}}/{{$blog->slug}}" class="text-decoration-none d-block h-100">
                        <div class="nova-blog-card">
                            <img src="{{asset('landlord/images/blog')}}/{{$blog->featured_image}}" alt="{{$blog->title}}">
                            <h5>{{$blog->title}}</h5>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-auto text-center">
                    <a href="{{url('blog')}}" class="button border-button radius-30">{{ __('db.All Blogs') }}</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section id="contact" class="contact-section contact-style-3 pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-10">
                    <div class="section-title text-center mb-50">
                        <h3 class="mb-15">{{ __('db.Contact Us') }}</h3>
                        @if(!empty($general_setting->meta_description))
                            <p>{{ $general_setting->meta_description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="contact-form-wrapper">
                        <form action="{{route('contactForm')}}" method="POST" class="form contact-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="single-input">
                                        <input class="form-control" type="text" name="name" placeholder="name..." required>
                                        <i class="lni lni-user"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-input">
                                        <input class="form-control" type="text" name="phone" placeholder="contact number..." required>
                                        <i class="lni lni-phone"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="single-input">
                                        <input class="form-control" type="text" name="email" placeholder="email..." required>
                                        <i class="lni lni-envelope"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="single-input">
                                        <textarea class="form-control" name="message" placeholder="your message" rows="6" required></textarea>
                                        <i class="lni lni-comments-alt"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <p id="contact-waiting-msg" class="mb-2"></p>
                                    <div class="form-button">
                                        <input id="contact-submit-btn" type="submit" class="button" value="{{__('db.submit')}}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="left-wrapper">
                        <div class="row">
                            <div class="col-lg-12 col-md-6">
                                <div class="single-item">
                                    <div class="icon"><i class="lni lni-phone"></i></div>
                                    <div class="text"><p>{{$general_setting->phone}}</p></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6">
                                <div class="single-item">
                                    <div class="icon"><i class="lni lni-envelope"></i></div>
                                    <div class="text"><p>{{$general_setting->email}}</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $landingPages = collect($pages ?? []);
        $policyKeywords = ['term', 'privacy', 'refund', 'cookie', 'gdpr', 'legal', 'disclaimer'];
        $companyKeywords = ['about', 'support', 'help', 'career', 'team', 'contact'];

        $policyPages = $landingPages->filter(function ($page) use ($policyKeywords) {
            $slug = strtolower((string) ($page->slug ?? ''));
            $title = strtolower((string) ($page->title ?? ''));
            foreach ($policyKeywords as $kw) {
                if ($kw !== '' && (str_contains($slug, $kw) || str_contains($title, $kw))) {
                    return true;
                }
            }
            return false;
        })->values();

        $companyPages = $landingPages->filter(function ($page) use ($policyPages, $companyKeywords) {
            if ($policyPages->contains('id', $page->id)) {
                return false;
            }
            $slug = strtolower((string) ($page->slug ?? ''));
            $title = strtolower((string) ($page->title ?? ''));
            foreach ($companyKeywords as $kw) {
                if ($kw !== '' && (str_contains($slug, $kw) || str_contains($title, $kw))) {
                    return true;
                }
            }
            return false;
        })->values();

        $policyOrder = [
            'terms-conditions' => 1,
            'terms_and_conditions' => 1,
            'terms-and-conditions' => 1,
            'privacy-policy' => 2,
            'privacy_policy' => 2,
            'refund-policy' => 3,
            'refund_policy' => 3,
        ];
        $policyPages = $policyPages->sortBy(function ($page) use ($policyOrder) {
            $slug = (string) ($page->slug ?? '');
            return $policyOrder[$slug] ?? 50;
        })->values();

        $companyPages = $companyPages->sortBy(function ($page) {
            return strtolower((string) ($page->title ?? ''));
        })->values();

        $hasSitemapFile = file_exists(public_path('sitemap.xml'));
        $hasFeaturesSection = isset($modules) && count($modules) > 0;

        $footerFeatureAnchor = function (array $needles) use ($modules, $hasFeaturesSection) {
            foreach ($modules ?? [] as $index => $module) {
                $hay = strtolower(($module->name ?? '').' '.($module->description ?? ''));
                foreach ($needles as $needle) {
                    $n = strtolower((string) $needle);
                    if ($n !== '' && str_contains($hay, $n)) {
                        if ($hasFeaturesSection) {
                            return url('/').'#feature-'.\Illuminate\Support\Str::slug($module->name).'-'.$index;
                        }
                        return url('/');
                    }
                }
            }
            return $hasFeaturesSection ? url('/').'#features' : url('/');
        };
    @endphp

    <!-- Footer (Nova footer-style-4) -->
    <footer class="footer footer-style-4 footer-dark footer-wrapper saas-premium-footer">
        <div class="container">
            <div class="saas-premium-footer__main widget-wrapper pt-5 pb-2">
                <div class="row g-4 g-lg-5">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="footer-widget saas-premium-footer__brand">
                            <div class="logo saas-premium-footer__logo">
                                <a href="{{url('/')}}"><img src="{{url('landlord/images/logo', $general_setting->site_logo)}}" alt="{{ $general_setting->site_title ?? '' }}"></a>
                            </div>
                            @if(!empty($general_setting->meta_description))
                                <p class="desc saas-premium-footer__desc">{{ $general_setting->meta_description }}</p>
                            @endif
                            <ul class="saas-premium-footer__contact list-unstyled small mt-3 mb-0">
                                @if(!empty($general_setting->phone))
                                    <li><a href="tel:{{ preg_replace('/\s+/', '', $general_setting->phone) }}"><i class="lni lni-phone"></i> {{ $general_setting->phone }}</a></li>
                                @endif
                                @if(!empty($general_setting->email))
                                    <li><a href="mailto:{{ $general_setting->email }}"><i class="lni lni-envelope"></i> {{ $general_setting->email }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="footer-widget">
                            <h6 class="saas-premium-footer__heading">{{ __('db.Quick Links') }}</h6>
                            <ul class="saas-premium-footer__links list-unstyled">
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li><a href="{{ url('/#features') }}">{{ __('db.Features') }}</a></li>
                                <li><a href="{{ url('/#packages') }}">{{ __('db.Pricing') }}</a></li>
                                <li><a href="{{ url('/#faq') }}">{{ __('db.FAQ') }}</a></li>
                                <li><a href="{{ url('/blog') }}">{{ __('db.Blog') }}</a></li>
                                <li><a href="{{ url('/#contact') }}">{{ __('db.Contact') }}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="footer-widget">
                            <h6 class="saas-premium-footer__heading">{{ __('db.Legal & Company') }}</h6>
                            @if($policyPages->isNotEmpty())
                                <p class="saas-premium-footer__subheading">{{ __('db.Policies') }}</p>
                                <ul class="saas-premium-footer__links list-unstyled mb-3">
                                    @foreach($policyPages as $page)
                                        <li><a href="{{ url('page/'.$page->slug) }}">{{ $page->title }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($companyPages->isNotEmpty())
                                <p class="saas-premium-footer__subheading">{{ __('db.Company') }}</p>
                                <ul class="saas-premium-footer__links list-unstyled mb-3">
                                    @foreach($companyPages as $page)
                                        <li><a href="{{ url('page/'.$page->slug) }}">{{ $page->title }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                            <ul class="saas-premium-footer__links list-unstyled">
                                <li><a href="{{ url('/#contact') }}">{{ __('db.Support') }}</a></li>
                                <li><a href="{{ url('/#faq') }}">{{ __('db.Help Center') }}</a></li>
                                @if($hasSitemapFile)
                                    <li><a href="{{ url('/sitemap.xml') }}" rel="nofollow">{{ __('db.Sitemap') }}</a></li>
                                @endif
                            </ul>
                            @php $shownIds = $policyPages->pluck('id')->merge($companyPages->pluck('id')); @endphp
                            @if($landingPages->pluck('id')->diff($shownIds)->isNotEmpty())
                                <p class="saas-premium-footer__subheading mt-3">{{ __('db.More pages') }}</p>
                                <ul class="saas-premium-footer__links list-unstyled">
                                    @foreach($landingPages as $page)
                                        @if($shownIds->contains($page->id))
                                            @continue
                                        @endif
                                        <li><a href="{{ url('page/'.$page->slug) }}">{{ $page->title }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="footer-widget">
                            <h6 class="saas-premium-footer__heading">{{ __('db.Resources') }}</h6>
                            <ul class="saas-premium-footer__links list-unstyled">
                                <li><a href="{{ $footerFeatureAnchor(['inventory', 'stock', 'warehouse', 'barcode']) }}">{{ __('db.Inventory Management') }}</a></li>
                                <li><a href="{{ $footerFeatureAnchor(['pos', 'point of sale', 'billing', 'invoice', 'checkout']) }}">{{ __('db.POS Billing') }}</a></li>
                                <li><a href="{{ $footerFeatureAnchor(['purchase', 'supplier', 'procurement']) }}">{{ __('db.Purchase Management') }}</a></li>
                                <li><a href="{{ $footerFeatureAnchor(['sale', 'return', 'customer']) }}">{{ __('db.Sales & Returns') }}</a></li>
                                <li><a href="{{ $footerFeatureAnchor(['tax', 'gst', 'vat', 'e-way', 'eway']) }}">{{ __('db.GST / Tax Support') }}</a></li>
                                <li><a href="{{ $footerFeatureAnchor(['report', 'analytic', 'dashboard']) }}">{{ __('db.Reports & Analytics') }}</a></li>
                            </ul>
                            <h6 class="saas-premium-footer__heading mt-4">{{ __('db.Connect with Us') }}</h6>
                            <ul class="saas-premium-footer__socials socials justify-content-start flex-wrap">
                                @foreach($socials as $social)
                                    <li>
                                        <a href="{{ $social->link }}" rel="noopener noreferrer" target="_blank" aria-label="social">
                                            <i class="{{ $social->icon }}"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="saas-premium-footer__bottom copyright-wrapper text-center">
                <p class="mb-0">&copy; {{$general_setting->site_title}} {{date('Y')}}. {{ __('db.All rights reserved') }}</p>
            </div>
        </div>
    </footer>

    {{-- <a href="https://wa.me/8801924756759" target="_blank">
        <div class="contact-button" style="background-color: #9fe870;border-radius: 50%;bottom: 20px;height: 70px;right: 20px;width: 70px;font-size: 30px;color: #f5f6f7;text-align: center;line-height: 64px;position: fixed;z-index: 999;">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#101010" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"></path>
            </svg>

        </div>
    </a> --}}

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop" class="scroll-top"><i class="lni lni-chevron-up"></i></a>
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
                                <p class="lead mb-3">Nexa Technologies is packed with all the features you'll need to seamlessly run your business</p>
                            @endif
                        </div>
                        <form action="/tenant-checkout" method="POST"  class="form row customer-signup-form">
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
                    <img src="landlord/images/setup_custom_domain.png">
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
                var $ctx = $(this).closest('.saas-pricing-card');
                if (!$ctx.length) {
                    $ctx = $(this).closest('.pricing-m, .pricing');
                }
                var $pp = $ctx.find('.package-price').first();
                if($('input[name=subscription_type]').val() == 'monthly') {
                    $('input[name=price]').val($pp.data('monthly'));
                } else {
                    $('input[name=price]').val($pp.data('yearly'));
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
                    url: '/send-otp',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
                    url: '/verify-otp',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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

    @if(env('USER_VERIFIED')==1)
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
