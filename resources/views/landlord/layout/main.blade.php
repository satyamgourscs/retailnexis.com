<!DOCTYPE html>
<html dir="@if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl){{'rtl'}}@endif">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if(!config('database.connections.saleprosaas_landlord'))
    <link rel="icon" type="image/png" href="{{url('public/logo', $general_setting->site_logo)}}" />
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <link rel="preload" href="<?php echo asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/bootstrap-select.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/bootstrap/css/bootstrap-select.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Drip icon font-->
    <link rel="preload" href="<?php echo asset('vendor/dripicons/webfont.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/dripicons/webfont.css') ?>" rel="stylesheet"></noscript>

    <!-- jQuery Circle-->
    <link rel="preload" href="<?php echo asset('css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Custom Scrollbar-->
    <link rel="preload" href="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" rel="stylesheet"></noscript>

    @if(Route::current()->getName() != '/')
    <!-- date range stylesheet-->
    <link rel="preload" href="<?php echo asset('vendor/daterange/css/daterangepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/daterange/css/daterangepicker.min.css') ?>" rel="stylesheet"></noscript>
    <!-- table sorter stylesheet-->
    <link rel="preload" href="<?php echo asset('vendor/datatable/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/datatable/dataTables.bootstrap4.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet"></noscript>
    @endif

    <link rel="stylesheet" href="<?php echo asset('css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('css/dropzone.css') ?>">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo asset('css/custom-default.css') ?>" type="text/css" id="custom-style">

    @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
      <!-- RTL css -->
      <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap-rtl.min.css') ?>" type="text/css">
      <link rel="stylesheet" href="<?php echo asset('css/custom-rtl.css') ?>" type="text/css" id="custom-style">
    @endif
    @else
    <link rel="icon" type="image/png" href="{{url('landlord/images/logo', $general_setting->site_logo)}}" />
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <link rel="preload" href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Drip icon font-->
    <link rel="preload" href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" rel="stylesheet"></noscript>

    <!-- jQuery Circle-->
    <link rel="preload" href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" rel="stylesheet"></noscript>
    <!-- Custom Scrollbar-->
    <link rel="preload" href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" rel="stylesheet"></noscript>

    @if(Route::current()->getName() != '/')
    <!-- date range stylesheet-->
    <link rel="preload" href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" rel="stylesheet"></noscript>
    <!-- table sorter stylesheet-->
    <link rel="preload" href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet"></noscript>
    @endif

    <link rel="stylesheet" href="<?php echo asset('../../css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('../../css/dropzone.css') ?>">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo asset('../../css/custom-default.css') ?>" type="text/css" id="custom-style">

    @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
      <!-- RTL css -->
      <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-rtl.min.css') ?>" type="text/css">
      <link rel="stylesheet" href="<?php echo asset('../../css/custom-rtl.css') ?>" type="text/css" id="custom-style">
    @endif
    @endif
    <!-- Google fonts - Roboto -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>

    @stack('custom-css')
  </head>

  <body @if($theme == 'dark') class="dark-mode dripicons-brightness-low" @else class="" @endif onload="myFunction()">
    <div id="loader"></div>
      <!-- Side Navbar -->
      <nav class="side-navbar">
        <span class="brand-big">
          <a href="{{url('superadmin/dashboard')}}">
              @if($general_setting->site_logo)
              <img src="{{url('landlord/images/logo', $general_setting->site_logo)}}" width="115">
              @else
              <h1 class="d-inline">{{$general_setting->site_title}}</h1>
              @endif
          </a>
        </span>
        <ul id="side-main-menu" class="side-menu list-unstyled">
            <li><a href="{{ route('superadmin.dashboard', [], false) }}"> <i class="dripicons-meter"></i><span>{{ __('db.dashboard') }}</span></a></li>
            <li><a target="_blank" href="{{url('/')}}"> <i class="dripicons-monitor"></i><span>{{ __('db.frontend') }}</span></a></li>
            <li><a href="#client" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-list"></i><span>{{__('db.Client')}}</span><span></a>
                <ul id="client" class="collapse list-unstyled ">
                    <li id="client-list-menu"><a href="{{ route('clients.index', [], false) }}">{{__('db.Client List')}}</a></li>
                </ul>
            </li>
            <li><a href="{{ route('payments.index', [], false) }}"><i class="dripicons-card"></i> {{__('db.Payments')}}</a></li>
            <li><a href="{{ route('coupon.index', [], false) }}"><i class="dripicons-card"></i> {{__('db.Coupon List')}}</a></li>
            <li><a href="#package" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-archive"></i><span>{{__('db.Package')}}</span><span></a>
                <ul id="package" class="collapse list-unstyled ">
                    <li id="package-list-menu"><a href="{{ route('packages.index', [], false) }}">{{__('db.Package List')}}</a></li>
                    <li id="package-create-menu"><a href="{{ route('packages.create', [], false) }}">{{__('db.Add Package')}}</a></li>
                </ul>
            </li>
            <li><a href="#cms" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-stack"></i><span>{{__('db.CMS')}}</span><span></a>
                <ul id="cms" class="collapse list-unstyled ">
                    <li id="cms-language-menu"><a href="{{url('superadmin/languages')}}">{{__('db.Languages')}}</a></li>
                    <li id="cms-hero-menu"><a href="{{url('superadmin/hero-section')}}">{{__('db.Hero Section')}}</a></li>
                    <li id="cms-module-menu"><a href="{{url('superadmin/module-section')}}">{{__('db.Module Section')}}</a></li>
                    <li id="cms-feature-menu"><a href="{{url('superadmin/feature-section')}}">{{__('db.Feature Section')}}</a></li>
                    <li id="cms-faq-menu"><a href="{{url('superadmin/faq-section')}}">{{__('db.FAQ Section')}}</a></li>
                    <li id="cms-testimonial-menu"><a href="{{url('superadmin/testimonial-section')}}">{{__('db.Testimonial Section')}}</a></li>
                    <li id="cms-tenant-signup-menu"><a href="{{url('superadmin/tenant-signup-description')}}">{{__('db.Tenant Signup Description')}}</a></li>
                    <li id="cms-blog-menu"><a href="{{url('superadmin/blog-section')}}">{{__('db.Blog Section')}}</a></li>
                    <li id="cms-page-menu"><a href="{{url('superadmin/page-section')}}">{{__('db.Page Section')}}</a></li>
                    <li id="cms-social-menu"><a href="{{url('superadmin/social-section')}}">{{__('db.Social Section')}}</a></li>
                </ul>
            </li>
            @if(!$general_setting->disable_tenant_support_tickets)
            <li><a href="{{ route('superadmin.tickets.index', [], false) }}"><i class="dripicons-ticket"></i> {{__('db.support_tickets')}}</a></li>
            @endif
            <li><a href="{{ route('superadminGeneralSetting', [], false) }}"><i class="dripicons-gear"></i> {{__('db.settings')}}</a></li>
            <li><a href="{{ route('superadminMailSetting', [], false) }}"><i class="dripicons-mail"></i> {{__('db.Mail Setting')}}</a></li>
            <li><a href="{{url('superadmin/addon-list')}}"><i class="dripicons-flag"></i> {{__('db.Addons')}}</a></li>
        </ul>
      </nav>

    <div class="page">
        <!-- navbar-->
      <header class="container-fluid">
        <nav class="navbar">
            <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>


            <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
            <li class="nav-item"><a id="switch-theme" data-toggle="tooltip" title="{{__('db.Switch Theme')}}"><i class="dripicons-brightness-max"></i></a></li>
            <li class="nav-item"><a id="btnFullscreen" data-toggle="tooltip" title="{{__('db.Full Screen')}}"><i class="dripicons-expand"></i></a></li>
            <li class="nav-item d-none">
                    <a rel="nofollow" title="{{__('db.language')}}" data-toggle="tooltip" class="nav-link dropdown-item"><i class="dripicons-web"></i></a>
                    <ul class="right-sidebar">
                        <li>
                        <a href="{{ url('language_switch/en') }}" class="btn btn-link"> English</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/es') }}" class="btn btn-link"> Español</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/ar') }}" class="btn btn-link"> عربى</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/s_chinese') }}" class="btn btn-link">中国人</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/t_chinese') }}" class="btn btn-link">中國人</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/pt_BR') }}" class="btn btn-link"> Portuguese</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/fr') }}" class="btn btn-link"> Français</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/de') }}" class="btn btn-link"> Deutsche</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/id') }}" class="btn btn-link"> Malay</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/hi') }}" class="btn btn-link"> हिंदी</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/vi') }}" class="btn btn-link"> Tiếng Việt</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/ru') }}" class="btn btn-link"> русский</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/bg') }}" class="btn btn-link"> български</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/tr') }}" class="btn btn-link"> Türk</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/it') }}" class="btn btn-link"> Italiano</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/nl') }}" class="btn btn-link"> Nederlands</a>
                        </li>
                        <li>
                        <a href="{{ url('language_switch/lao') }}" class="btn btn-link"> Lao</a>
                        </li>
                    </ul>
            </li>
            <li class="nav-item">
                @php
                    $headerUser = Auth::user();
                    // Tenant-safe display name:
                    $headerDisplayName = '';
                    if (function_exists('tenancy') && tenancy()->initialized) {
                        $tenantCompanyName = tenant()?->company_name ?? '';
                        $headerDisplayName = is_string($tenantCompanyName) ? trim($tenantCompanyName) : '';
                    }

                    if ($headerDisplayName === '') {
                        $headerDisplayName = $headerUser?->company_name ?: $headerUser?->name;
                        $headerDisplayName = is_string($headerDisplayName) ? trim($headerDisplayName) : '';
                    }
                    $headerLower = strtolower($headerDisplayName);

                    if (in_array($headerLower, ['superadmin', 'lioncoders'], true)) {
                        $headerDisplayName = tenant()?->id ?? 'Tenant';
                    }
                @endphp
                <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst($headerDisplayName)}}</span> <i class="fa fa-angle-down"></i>
                </a>
                <ul class="right-sidebar">
                    <li>
                        <a href="{{ route('user.superadminProfile', ['id' => Auth::id()], false) }}"><i class="dripicons-user"></i> {{__('db.profile')}}</a>
                    </li>
                    <li>
                        <a href="{{ route('superadminGeneralSetting', [], false) }}"><i class="dripicons-gear"></i> {{__('db.settings')}}</a>
                    </li>
                    <li>
                    <a href="{{ route('superadmin.logout', [], false) }}"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                        {{__('db.logout')}}
                    </a>
                    <form id="logout-form" action="{{ route('superadmin.logout', [], false) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    </li>
                </ul>
            </li>
            </ul>
        </nav>
      </header>

      <div style="display:none" id="content" class="animate-bottom">
          @yield('content')
      </div>

      <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12">
              @if(function_exists('tenancy') && tenancy()->initialized)
                <p>&copy; {{$general_setting->site_title}} | {{date("Y")}}. All rights reserved | V {{env('VERSION')}}</p>
              @else
                <p>&copy; {{$general_setting->site_title}} | {{__('db.Developed By')}} <span class="external">{{$general_setting->developed_by}}</span> | V {{env('VERSION')}}</p>
              @endif
            </div>
          </div>
        </div>
      </footer>
    </div>
    @if(!config('database.connections.saleprosaas_landlord'))
        <script type="text/javascript" src="<?php echo asset('vendor/jquery/jquery.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/jquery/jquery-ui.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/jquery/bootstrap-datepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/jquery/jquery.timepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/popper.js/umd/popper.min.js') ?>">
        </script>
        <script type="text/javascript" src="<?php echo asset('vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/bootstrap/js/bootstrap-select.min.js') ?>"></script>

        <script type="text/javascript" src="<?php echo asset('js/grasp_mobile_progress_circle-1.0.0.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/jquery.cookie/jquery.cookie.js') ?>">
        </script>
        <script type="text/javascript" src="<?php echo asset('vendor/chart.js/Chart.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('js/charts-custom.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')?>"></script>
        @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
          <script type="text/javascript" src="<?php echo asset('js/front_rtl.js') ?>"></script>
        @else
          <script type="text/javascript" src="<?php echo asset('js/front.js') ?>"></script>
        @endif

        @if(Route::current()->getName() != '/')
        <script type="text/javascript" src="<?php echo asset('vendor/daterange/js/moment.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/daterange/js/knockout-3.4.2.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/daterange/js/daterangepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/tinymce/js/tinymce/tinymce.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('js/dropzone.js') ?>"></script>

        <!-- table sorter js-->
        @if( Config::get('app.locale') == 'ar')
            <script type="text/javascript" src="<?php echo asset('vendor/datatable/pdfmake_arabic.min.js') ?>"></script>
            <script type="text/javascript" src="<?php echo asset('vendor/datatable/vfs_fonts_arabic.js') ?>"></script>
        @else
            <script type="text/javascript" src="<?php echo asset('vendor/datatable/pdfmake.min.js') ?>"></script>
            <script type="text/javascript" src="<?php echo asset('vendor/datatable/vfs_fonts.js') ?>"></script>
        @endif
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/jquery.dataTables.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/dataTables.bootstrap4.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/dataTables.buttons.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/jszip.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/buttons.bootstrap4.min.js') ?>">"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/buttons.colVis.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/buttons.html5.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/buttons.printnew.js') ?>"></script>

        <script type="text/javascript" src="<?php echo asset('vendor/datatable/sum().js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('vendor/datatable/dataTables.checkboxes.min.js') ?>"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
        @endif
    @else
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery-ui.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/bootstrap-datepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.timepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/popper.js/umd/popper.min.js') ?>">
        </script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap/js/bootstrap-select.min.js') ?>"></script>

        <script type="text/javascript" src="<?php echo asset('../../js/grasp_mobile_progress_circle-1.0.0.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery.cookie/jquery.cookie.js') ?>">
        </script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/chart.js/Chart.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../js/charts-custom.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')?>"></script>
        @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
          <script type="text/javascript" src="<?php echo asset('../../js/front_rtl.js') ?>"></script>
        @else
          <script type="text/javascript" src="<?php echo asset('../../js/front.js') ?>"></script>
        @endif

        @if(Route::current()->getName() != '/')
        <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/moment.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/knockout-3.4.2.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/daterangepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/tinymce/js/tinymce/tinymce.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../js/dropzone.js') ?>"></script>

        <!-- table sorter js-->
        @if( Config::get('app.locale') == 'ar')
            <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/pdfmake_arabic.min.js') ?>"></script>
            <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/vfs_fonts_arabic.js') ?>"></script>
        @else
            <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/pdfmake.min.js') ?>"></script>
            <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/vfs_fonts.js') ?>"></script>
        @endif
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/jquery.dataTables.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.buttons.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.bootstrap4.min.js') ?>">"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.colVis.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.html5.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.printnew.js') ?>"></script>

        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/sum().js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.checkboxes.min.js') ?>"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
        @endif
    @endif
    @stack('scripts')
    <script type="text/javascript">
        var theme = <?php echo json_encode($theme); ?>;
        if(theme == 'dark') {
            $('body').addClass('dark-mode');
            $('#switch-theme i').addClass('dripicons-brightness-low');
        }
        else {
            $('body').removeClass('dark-mode');
            $('#switch-theme i').addClass('dripicons-brightness-max');
        }
        $('#switch-theme').click(function() {
            if(theme == 'light') {
                theme = 'dark';
                var url = <?php echo json_encode(route('switchTheme', ['theme' => 'dark'], false)); ?>;
                $('body').addClass('dark-mode');
                $('#switch-theme i').addClass('dripicons-brightness-low');
            }
            else {
                theme = 'light';
                var url = <?php echo json_encode(route('switchTheme', ['theme' => 'light'], false)); ?>;
                $('body').removeClass('dark-mode');
                $('#switch-theme i').addClass('dripicons-brightness-max');
            }

            $.get(url, function(data) {
                console.log('theme changed to '+theme);
            });
        });


      if ($(window).outerWidth() > 1199) {
          $('nav.side-navbar').removeClass('shrink');
      }

      function myFunction() {
          setTimeout(showPage, 100);
      }

      function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("content").style.display = "block";
      }

      $("div.alert").not(".not-slide").delay(4000).slideUp(800);


      function confirmDelete() {
          if (confirm("Are you sure want to delete?")) {
              return true;
          }
          return false;
      }

      $('.date').datepicker({
         format: "dd-mm-yyyy",
         autoclose: true,
         todayHighlight: true
       });

      $('.selectpicker').selectpicker({
          style: 'btn-link',
      });
    </script>
  </body>
</html>
