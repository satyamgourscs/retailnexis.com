<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="LionCoders" />
    <meta name="csrf-token" content="CmSeExxpkZmScDB9ArBZKMGKAyzPqnxEriplXWrS">
    <link rel="icon" type="image/png" href="{{url('public/landlord/images/logo', $general_setting->site_logo)}}" />
    <!-- Document Title -->
    <title>{{$general_setting->meta_title ?? 'SalePro SAAS'}}</title>
    <!-- Links -->
    <meta name="description" content="{{$general_setting->meta_description ?? 'SalePro SAAS'}}" />
    <meta property="og:url" content="{{url()->full()}}" />
    <meta property="og:title" content="{{$general_setting->og_title ?? 'SalePro SAAS'}}" />
    <meta property="og:description" content="{{$general_setting->og_description ?? 'SalePro SAAS'}}" />
    <meta property="og:image" content="{{url('/public/landlord/images/og-image')}}/{{$general_setting->og_image ?? 'saleprosaas.jpg'}}" />
    
    <!-- Bootstrap CSS -->
    <link href="{{url('/')}}/landlord/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('public/vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('public/vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>

    <!-- Plugins CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{url('/')}}/landlord/css/plugins.css">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{url('/')}}/landlord/css/plugins.css">
    </noscript>

    <!-- common style CSS -->
    <link href="{{url('/')}}/landlord/css/common-style-light.css" rel="stylesheet">

    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-color: #f16232;
        }
    </style>
    
    @if(isset($general_setting->fb_pixel_script))
    {!!$general_setting->fb_pixel_script!!}
    @endif
</head>

<body class="home">

    <!--Header-->
    <!--Header Area starts-->
    <header id="header-middle" class="header-middle">

        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-7">
                    <div class="mobile-menu-icon d-lg-none"><i class="ti-menu"></i></div>
                    <div class="logo">
                        <a href="{{url('/')}}">
                            <img class="lazy" src="{{url('public/landlord/images/logo', $general_setting->site_logo)}}" alt="Brand logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-flex d-xl-flex middle-column justify-content-center">
                    <div id="main-menu" class="main-menu">
                        <ul class="pl-0">
                            <li><a href="{{url('/')}}#features">Features</a></li>

                            <li><a href="{{url('/')}}#faq">FAQ</a></li>

                            <li><a href="{{url('/')}}#packages">Pricing</a></li>
                            
                            <li><a href="{{url('/blog')}}">Blog</a></li>

                            <li><a href="{{url('/')}}#contact">Contact</a></li>

                           
                            <li class="d-lg-none dropdown">
                                <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Language
                                </a>
                                <div class="dropdown-menu">
                                @foreach($languages as $language)
                                    <a class="dropdown-item" href="{{url('switch-landing-page-language/'.$language->id)}}">{{$language->name}}</a>
                                @endforeach
                                </div>
                            </li>
                            
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-5" style="text-align:right">
                    <ul class="offset-menu-wrapper p-0 d-none d-lg-flex d-xl-flex">                        
                        <li class="has-dropdown">
                            <i class="fa fa-globe" aria-hidden="true"></i> English
                            <div class="dropdown">
                                @foreach($languages as $language)
                                <a href="{{url('switch-landing-page-language/'.$language->id)}}">{{$language->name}}</a>
                                @endforeach
                            </div>
                        </li>
                        
                        <li>
                            <a class="button style2" href="../#packages">Try Now</a>
                        </li>
                    </ul>
                    <a class="button style2 d-lg-none" href="../#packages">Try Now</a>
                </div>
            </div>
        </div>
        <nav id="mobile-nav"></nav>
    </header>

    <section id="blog">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-5">
                    <h1 class="mt-3 text-center">{{$page->title}}</h1>
                </div>
                <div class="col-md-6 offset-md-3 mb-5">
                    {!! $page->text !!}
                </div>
            </div> 
        </div>
    </section>
    
    <!-- Footer section Starts-->
    <div class="footer-wrapper">
        <div class="container">
            @if(!env('USER_VERIFIED'))
            <div class="mt-5 mb-5 cta">
                <h3 class="h1 mb-5">Start your software subscription business</h3>
                <a class="button lg style2" href="https://lion-coders.com/software/salepro-saas-pos-inventory-saas-php-script">Get SalePro SAAS</a>
            </div>
            <hr>
            @endif
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

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop"><i class="ti-arrow-up"></i></a>
    <!--Scroll to top ends-->

    <div class="body__overlay"></div>

    <!-- Cookie consent Starts-->


    <!-- Cookie consent Ends-->


    <!--Plugin js -->
    <script src="{{ asset('public/landlord/js/plugin.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
    <script src="{{ asset('public/vendor/jquery/jquery-ui.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('public/landlord/js/sweetalert2@11.js')}}"></script>
    <!-- Main js -->
    <script src="{{ asset('public/landlord/js/main.js')}}"></script>

    @if(isset($general_setting->ga_script))
    {!!$general_setting->ga_script!!}
    @endif

    @if(isset($general_setting->chat_script))
    {!!$general_setting->chat_script!!}
    @endif
</body>
</html>