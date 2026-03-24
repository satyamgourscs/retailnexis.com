<!DOCTYPE html>
<html dir="@if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl){{'rtl'}}@endif">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @if(!config('database.connections.saleprosaas_landlord'))
  <link rel="icon" type="image/png" href="{{url('logo', $general_setting->site_logo)}}" />
  <title>{{$general_setting->site_title}}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="all,follow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS-->
  <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
  <link rel="preload" href="<?php echo asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo asset('vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('vendor/bootstrap/css/bootstrap-select.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/bootstrap/css/bootstrap-select.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Font Awesome CSS-->
  <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Drip icon font-->
  <link rel="preload" href="<?php echo asset('vendor/dripicons/webfont.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/dripicons/webfont.css') ?>" rel="stylesheet">
  </noscript>

  <!-- jQuery Circle-->
  <link rel="preload" href="<?php echo asset('css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Custom Scrollbar-->
  <link rel="preload" href="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" rel="stylesheet">
  </noscript>

  @if(optional(Route::current())->getName() != '/')
  <!-- date range stylesheet-->
  <link rel="preload" href="<?php echo asset('vendor/daterange/css/daterangepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/daterange/css/daterangepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- table sorter stylesheet-->
  <link rel="preload" href="<?php echo asset('vendor/datatable/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('vendor/datatable/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
  </noscript>
  @endif

  <link rel="stylesheet" href="<?php echo asset('css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('css/dropzone.css') ?>">
  <!-- Custom stylesheet - for your changes-->
  <link rel="stylesheet" href="<?php echo asset('css/custom-' . $general_setting->theme) ?>" type="text/css" id="custom-style">
  <link rel="stylesheet" href="<?php echo asset('css/app-premium-theme.css') ?>" type="text/css" id="app-premium-theme">

  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <!-- RTL css -->
  <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap-rtl.min.css') ?>" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('css/custom-rtl.css') ?>" type="text/css" id="custom-style">
  @endif
  @else
  <link rel="icon" type="image/png" href="{{url('../../logo', $general_setting->site_logo)}}" />
  <title>{{$general_setting->site_title}}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="all,follow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS-->
  <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Font Awesome CSS-->
  <link rel="preload" href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Drip icon font-->
  <link rel="preload" href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" rel="stylesheet">
  </noscript>

  <!-- jQuery Circle-->
  <link rel="preload" href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Custom Scrollbar-->
  <link rel="preload" href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" rel="stylesheet">
  </noscript>

  @if(optional(Route::current())->getName() != '/')
  <!-- date range stylesheet-->
  <link rel="preload" href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- table sorter stylesheet-->
  <link rel="preload" href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
  </noscript>
  @endif

  <link rel="stylesheet" href="<?php echo asset('../../css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('../../css/dropzone.css') ?>">
  <!-- Custom stylesheet - for your changes-->
  <link rel="stylesheet" href="<?php echo asset('../../css/custom-' . $general_setting->theme) ?>" type="text/css" id="custom-style">
  <link rel="stylesheet" href="<?php echo asset('../../css/app-premium-theme.css') ?>" type="text/css" id="app-premium-theme">

  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <!-- RTL css -->
  <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-rtl.min.css') ?>" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('../../css/custom-rtl.css') ?>" type="text/css" id="custom-style">
  @endif
  @endif
  <!-- Google fonts - Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&display=swap" rel="stylesheet">

  @stack('css')
</head>

<body class="@if($theme == 'dark')dark-mode dripicons-brightness-low @endif  @if(optional(Route::current())->getName() == 'sale.pos') pos-page @endif" onload="myFunction()">
  <div id="loader"></div>
  <!-- Side Navbar -->
  <nav class="side-navbar shrink d-print-none">
    <span class="brand-big">
      <a href="{{url('/dashboard') }}">
      @if($general_setting->site_logo)
      <img src="{{url('logo', $general_setting->site_logo)}}" width="115">
      @else
        <h1 class="d-inline">{{$general_setting->site_title}}</h1>
      @endif
      </a>
    </span>
    @include('backend.layout.sidebar')
  </nav>

  <div class="page app-premium-theme">
    <!-- navbar-->
    @if(optional(Route::current())->getName() != 'sale.pos')
    <header class="container-fluid premium-top-header">
      <nav class="navbar premium-top-navbar border-0">
        <div class="premium-top-navbar__inner d-flex align-items-center flex-wrap w-100">
        <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0 mr-md-3 mb-2 mb-md-0 premium-top-navbar__left">
        <a id="toggle-btn" href="#" class="menu-btn premium-top-toggle"><i class="fa fa-bars"> </i></a>
        @php
          $products_index_active = $role_has_permissions_list->where('name', 'products-index')->first();
        @endphp
        @if($products_index_active)
        <form class="premium-top-search d-none d-sm-flex" action="{{ route('products.index') }}" method="get" role="search">
          <span class="premium-top-search__icon" aria-hidden="true"><i class="fa fa-search"></i></span>
          <input type="search" name="search" class="form-control premium-top-search__input" placeholder="{{ __('db.Type Product Name or Code') }}" autocomplete="off" value="{{ request('search') }}">
        </form>
        @endif
        </div>

        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center flex-wrap mb-0 premium-top-navbar__actions ml-md-auto">
          <li class="nav-item dropdown">
            <a class="btn-pos btn-sm premium-top-quick-add dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
              <i class="dripicons-plus"></i>
            </a>
            <ul class="dropdown-menu">
              <?php
              $category_permission_active = $role_has_permissions_list->where('name', 'category')->first();
              ?>
              @if($category_permission_active)
              <li class="dropdown-item"><a data-toggle="modal" data-target="#category-modal">{{__('db.Add Category') }}</a></li>
              @endif
              <?php
              $add_permission_active = $role_has_permissions_list->where('name', 'products-add')->first();
              ?>
              @if($add_permission_active)
              <li class="dropdown-item"><a href="{{route('products.create') }}">{{__('db.add_product') }}</a></li>
              @endif
              <?php
              $add_permission_active = $role_has_permissions_list->where('name', 'purchases-add')->first();
              ?>
              @if($add_permission_active)
              <li class="dropdown-item"><a href="{{route('purchases.create') }}">{{ __('db.Add Purchase') }}</a></li>
              @endif
              <?php
              $sale_add_permission_active = $role_has_permissions_list->where('name', 'sales-add')->first();
              ?>
              @if($sale_add_permission_active)
              <li class="dropdown-item"><a href="{{route('sales.create') }}">{{ __('db.Add Sale') }}</a></li>
              @endif
              <?php
              $expense_add_permission_active = $role_has_permissions_list->where('name', 'expenses-add')->first();
              ?>
              @if($expense_add_permission_active)
              <li class="dropdown-item"><a data-toggle="modal" data-target="#expense-modal"> {{ __('db.Add Expense') }}</a></li>
              @endif
              <?php
              $quotation_add_permission_active = $role_has_permissions_list->where('name', 'quotes-add')->first();
              ?>
              @if($quotation_add_permission_active)
              <li class="dropdown-item"><a href="{{route('quotations.create') }}">{{ __('db.Add Quotation') }}</a></li>
              @endif
              <?php
              $transfer_add_permission_active = $role_has_permissions_list->where('name', 'transfers-add')->first();
              ?>
              @if($transfer_add_permission_active)
              <li class="dropdown-item"><a href="{{route('transfers.create') }}">{{ __('db.Add Transfer') }}</a></li>
              @endif
              <?php
              $return_add_permission_active = $role_has_permissions_list->where('name', 'returns-add')->first();
              ?>
              @if($return_add_permission_active)
              <li class="dropdown-item"><a href="#" data-toggle="modal" data-target="#add-sale-return"> {{ __('db.Add Return') }}</a></li>
              @endif
              <?php
              $purchase_return_add_permission_active = $role_has_permissions_list->where('name', 'purchase-return-add')->first();
              ?>
              @if($purchase_return_add_permission_active)
              <li class="dropdown-item"><a href="#" data-toggle="modal" data-target="#add-purchase-return"> {{ __('db.Add Purchase Return') }}</a></li>
              @endif
              <?php
              $user_add_permission_active = $role_has_permissions_list->where('name', 'users-add')->first();
              ?>
              @if($user_add_permission_active)
                <li class="dropdown-item"><a href="{{route('user.create') }}">{{ __('db.Add User') }}</a></li>
              @endif
              <?php
              $customer_add_permission_active = $role_has_permissions_list->where('name', 'customers-add')->first();
              ?>
              @if($customer_add_permission_active)
              <li class="dropdown-item"><a href="{{route('customer.create') }}">{{ __('db.Add Customer') }}</a></li>
              @endif
              <?php
              $biller_add_permission_active = $role_has_permissions_list->where('name', 'billers-add')->first();
              ?>
              @if($biller_add_permission_active)
              <li class="dropdown-item"><a href="{{route('biller.create') }}">{{ __('db.Add Biller') }}</a></li>
              @endif
              <?php
              $supplier_add_permission_active = $role_has_permissions_list->where('name', 'suppliers-add')->first();
              ?>
              @if($supplier_add_permission_active)
              <li class="dropdown-item"><a href="{{route('supplier.create') }}">{{ __('db.Add Supplier') }}</a></li>
              @endif
            </ul>
          </li>
          <?php
          $empty_database_permission_active = $role_has_permissions_list->where('name', 'empty_database')->first();

          $sale_add_permission_active = $role_has_permissions_list->where('name', 'sales-add')->first();

          $product_qty_alert_active = $role_has_permissions_list->where('name', 'product-qty-alert')->first();

          $general_setting_permission_active = $role_has_permissions_list->where('name', 'general_setting')->first();

          $language_setting_active = $role_has_permissions_list->where('name', 'language_setting')->first();

          $reminderNotifications = \Auth::user()->unreadNotifications->where('data.reminder_date', date('Y-m-d'));
          $reminderTodayCount = $reminderNotifications->count();
          $stockAlertSum = (int) $alert_product + (int) $dso_alert_product_no;
          if ($product_qty_alert_active) {
              $notificationBadgeCount = $stockAlertSum + $reminderTodayCount;
              $showNotificationsDropdown = $notificationBadgeCount > 0;
          } else {
              $notificationBadgeCount = $reminderTodayCount;
              $showNotificationsDropdown = $reminderTodayCount > 0;
          }

          ?>
          @if($sale_add_permission_active)
          <li class="nav-item"><a class="btn-pos btn-sm premium-top-pill" href="{{route('sale.pos') }}"><i class="dripicons-shopping-bag"></i><span> POS</span></a></li>
          <li class="nav-item ml-1 ml-md-2"><a class="btn-pos btn-sm text-nowrap premium-top-pill" href="{{ route('sales.create') }}" data-toggle="tooltip" data-placement="bottom" title="Click to create a new sale"><i class="dripicons-cart"></i><span> Create Sales</span></a></li>
          @endif
          <li class="nav-item d-none d-lg-block"><a id="switch-theme" class="premium-top-icon" href="#" data-toggle="tooltip" title="{{ __('Switch Theme') }}"><i class="dripicons-brightness-max"></i></a></li>
          @if(config('database.connections.saleprosaas_landlord'))
          <li class="nav-item"><a class="premium-top-icon" target="_blank" href="{{ route('contactForRenewal', ['id' => $subdomain]) }}" data-toggle="tooltip" title="{{ __('Renew Subscription') }}"><i class="dripicons-clockwise"></i></a></li>
          @endif
          <li class="nav-item d-none d-lg-block"><a id="btnFullscreen" class="premium-top-icon" href="#" data-toggle="tooltip" title="{{ __('Full Screen') }}"><i class="dripicons-expand"></i></a></li>
          @if(\Auth::user()->role_id <= 2)
          <li class="nav-item"><a class="premium-top-icon" href="{{route('cashRegister.index') }}" data-toggle="tooltip" title="{{ __('Cash Register List') }}"><i class="dripicons-archive"></i></a></li>
          @endif
            @if($showNotificationsDropdown)
            <li class="nav-item dropdown">
              <a rel="nofollow" href="#" class="premium-top-icon premium-notification-bell dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('Notifications') }}">
                <i class="dripicons-bell premium-top-bell"></i>
                <span class="premium-top-badge notification-number">{{ $notificationBadgeCount }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right premium-top-dropdown premium-top-dropdown--notify shadow-lg">
                <div class="premium-top-dropdown__head px-3 py-2">
                  <span class="premium-top-dropdown__title">{{ __('Notifications') }}</span>
                </div>
                <div class="premium-top-dropdown__body">
                  @if($product_qty_alert_active && $alert_product > 0)
                  <a class="dropdown-item premium-top-notify-item" href="{{ route('report.qtyAlert') }}">
                    <span class="premium-top-notify-item__dot"></span>
                    <span>{{ $alert_product }} product exceeds alert quantity</span>
                  </a>
                  @endif
                  @if($product_qty_alert_active && $dso_alert_product_no)
                  <a class="dropdown-item premium-top-notify-item" href="{{ route('report.dailySaleObjective') }}">
                    <span class="premium-top-notify-item__dot premium-top-notify-item__dot--amber"></span>
                    <span>{{ $dso_alert_product_no }} product could not fulfill daily sale objective</span>
                  </a>
                  @endif
                  @foreach($reminderNotifications as $key => $notification)
                  @if(!empty($notification->data['document_name']))
                  <a class="dropdown-item premium-top-notify-item" target="_blank" href="{{ url('documents/notification', $notification->data['document_name']) }}">
                    <span class="premium-top-notify-item__dot premium-top-notify-item__dot--violet"></span>
                    <span>{{ $notification->data['message'] }}</span>
                  </a>
                  @else
                  <a href="#" class="dropdown-item premium-top-notify-item premium-top-notify-item--text" onclick="return false;">
                    <span class="premium-top-notify-item__dot premium-top-notify-item__dot--violet"></span>
                    <span>{{ $notification->data['message'] ?? '' }}</span>
                  </a>
                  @endif
                  @endforeach
                </div>
              </div>
            </li>
            @endif
            <li class="nav-item dropdown">
              <a rel="nofollow" href="#" class="premium-top-icon dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('db.language') }}">
                <i class="dripicons-web"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right premium-top-dropdown shadow-lg">
                <div class="premium-top-dropdown__head px-3 py-2">
                  <span class="premium-top-dropdown__title">{{ __('db.language') }}</span>
                </div>
                @foreach ($languages as $language)
                <a class="dropdown-item premium-top-menu-link" href="{{ url('language_switch/'.$language->id) }}">{{ $language->name }}</a>
                @endforeach
                @if (!config('app.user_verified'))
                  @if ($language_setting_active)
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item premium-top-menu-link premium-top-menu-link--accent" href="{{ route('languages') }}">{{ __('db.Languages') }} <span class="float-right">→</span></a>
                  @endif
                @endif
              </div>
            </li>
            <li class="nav-item dropdown">
              @php
                  $headerUser = Auth::user();
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
                  $nameParts = preg_split('/\s+/', trim((string) ($headerUser?->name ?? 'User')));
                  $headerInitials = '';
                  if (!empty($nameParts[0])) {
                      $headerInitials .= strtoupper(mb_substr($nameParts[0], 0, 1));
                  }
                  if (!empty($nameParts[1])) {
                      $headerInitials .= strtoupper(mb_substr($nameParts[1], 0, 1));
                  }
                  if ($headerInitials === '') {
                      $headerInitials = 'U';
                  }
              @endphp
              <a rel="nofollow" href="#" class="premium-top-profile dropdown-toggle d-flex align-items-center" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="premium-top-avatar" aria-hidden="true">{{ $headerInitials }}</span>
                <span class="premium-top-profile__name d-none d-xl-inline">{{ ucfirst(Str::limit($headerDisplayName, 22)) }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right premium-top-dropdown premium-top-dropdown--profile shadow-lg">
                <div class="premium-top-profile-card px-3 py-3 mb-0">
                  <div class="d-flex align-items-center">
                    <span class="premium-top-avatar premium-top-avatar--lg mr-2">{{ $headerInitials }}</span>
                    <div class="min-w-0">
                      <div class="premium-top-profile-card__name text-truncate">{{ ucfirst($headerDisplayName) }}</div>
                      <div class="premium-top-profile-card__email text-truncate small text-muted">{{ $headerUser?->email }}</div>
                    </div>
                  </div>
                </div>
                <div class="dropdown-divider my-0"></div>
                <a class="dropdown-item premium-top-menu-link" href="{{ route('user.profile', ['id' => Auth::id()]) }}"><i class="dripicons-user mr-2"></i> {{ __('db.profile') }}</a>
                @if($general_setting_permission_active)
                <a class="dropdown-item premium-top-menu-link" href="{{ route('setting.general') }}"><i class="dripicons-gear mr-2"></i> {{ __('db.settings') }}</a>
                @endif
                <a class="dropdown-item premium-top-menu-link" href="{{ url('my-transactions/'.date('Y').'/'.date('m')) }}"><i class="dripicons-swap mr-2"></i> {{ __('db.My Transaction') }}</a>
                @if(Auth::user()->role_id != 5)
                <a class="dropdown-item premium-top-menu-link" href="{{ url('holidays/my-holiday/'.date('Y').'/'.date('m')) }}"><i class="dripicons-vibrate mr-2"></i> {{ __('db.My Holiday') }}</a>
                @endif
                @if($empty_database_permission_active)
                <div class="dropdown-divider"></div>
                <a class="dropdown-item premium-top-menu-link text-danger" onclick="return confirm('Are you sure want to delete? If you do this all of your data will be lost.')" href="{{ route('setting.emptyDatabase') }}"><i class="dripicons-stack mr-2"></i> {{ __('db.Empty Database') }}</a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item premium-top-menu-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dripicons-power mr-2"></i> {{ __('db.logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
              </div>
            </li>
        </ul>
        </div>
      </nav>
    </header>
    @endif


    <div style="display:none" id="content" class="animate-bottom">
      @yield('content')
    </div>

    <footer class="main-footer">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-12">
            @if(function_exists('tenancy') && tenancy()->initialized)
              {{-- Tenant-facing: keep branding tenant-only (no central developer credits) --}}
              <p>&copy; {{$general_setting->site_title}}. All rights reserved | V {{env('VERSION') }}</p>
            @else
              {{-- Central/superadmin-facing --}}
              <p>&copy; {{$general_setting->site_title}} | {{ __('Developed') }} {{ __('By') }} <span class="external">{{$general_setting->developed_by}}</span> | V {{env('VERSION') }}</p>
            @endif
          </div>
        </div>
      </div>
    </footer>

    <!-- notification modal -->
    <div id="notification-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Send Notification') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'notifications.store', 'method' => 'post', 'files'=> true]) !!}
            <div class="row">
              <?php
              $lims_user_list = DB::table('users')->where([
                ['is_active', true],
                ['id', '!=', \Auth::user()->id]
              ])->get();
              ?>
              <div class="col-md-4 form-group">
                <input type="hidden" name="sender_id" value="{{\Auth::id()}}">
                <label>{{ __('User') }} *</label>
                <select name="receiver_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select user...">
                  @foreach($lims_user_list as $user)
                  <option value="{{$user->id}}">{{$user->name . ' (' . $user->email. ')'}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 form-group">
                <label>{{ __('Reminder Date') }}</label>
                <input type="text" name="reminder_date" class="form-control date" value="{{date('d-m-Y') }}">
              </div>
              <div class="col-md-4 form-group">
                <label>{{ __('Attach Document') }}</label>
                <input type="file" name="document" class="form-control">
              </div>
              <div class="col-md-12 form-group">
                <label>{{ __('Message') }} *</label>
                <textarea rows="5" name="message" class="form-control" required></textarea>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary ">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end notification modal -->

    <!-- Category Modal -->
    <div id="category-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          {!! Form::open(['route' => 'category.store', 'method' => 'post', 'files' => true, 'id' => 'category-form']) !!}
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Add Category') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>{{ __('name') }} *</label>
                {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control', 'placeholder' => __('db.Type category name')))}}
                <x-validation-error fieldName="name" />
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Image') }}</label>
                <input type="file" name="image" class="form-control">
                <x-validation-error fieldName="image" />
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Parent Category') }}</label>
                <select name="parent_id" class="form-control selectpicker" id="parent">
                  <option value="">No {{ __('parent') }}</option>
                  @foreach($categories_list as $category)
                  <option value="{{$category->id}}">{{$category->name}}</option>
                  @endforeach
                </select>
                <x-validation-error fieldName="parent_id" />
              </div>
              @if (\Schema::hasColumn('categories', 'woocommerce_category_id'))
              <div class="col-md-6 form-group mt-4">
                <input class="mt-3" name="is_sync_disable" type="checkbox" id="is_sync_disable" value="1">&nbsp; {{ __('Disable Woocommerce Sync') }}
                <x-validation-error fieldName="is_sync_disable" />
              </div>
              @endif

              @if(in_array('ecommerce', explode(',', (string) ($general_setting->modules ?? ''))))
              <div class="col-md-12 mt-3">
                <h6><strong>{{ __('For Website') }}</strong></h6>
                <hr>
              </div>

              <div class="col-md-6 form-group">
                <label>{{ __('Icon') }}</label>
                <input type="file" name="icon" class="form-control">
              </div>
              <div class="col-md-6 form-group">
                <input class="mt-5" type="checkbox" name="featured" id="featured" value="1"> <label>{{ __('List on category dropdown') }}</label>
              </div>
              @endif
            </div>

            @if(in_array('ecommerce', explode(',', (string) ($general_setting->modules ?? ''))))
            <div class="row">
              <div class="col-md-12 mt-3">
                <h6><strong>{{ __('For SEO') }}</strong></h6>
                <hr>
              </div>
              <div class="col-md-12 form-group">
                <label>{{ __('Meta Title') }}</label>
                {{Form::text('page_title',null,array('class' => 'form-control', 'placeholder' => __('db.Meta Title')))}}
              </div>
              <div class="col-md-12 form-group">
                <label>{{ __('Meta Description') }}</label>
                {{Form::text('short_description',null,array('class' => 'form-control', 'placeholder' => __('db.Meta Description')))}}
              </div>
            </div>
            @endif

            <div class="form-group">
              <input type="hidden" class="category-ajax-check" name="ajax" value="0">
              <button type="submit" class="btn btn-primary category-submit-btn">{{ __('submit') }}</button>
            </div>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
    <!-- Category Modal -->

    <!-- expense modal -->
    <div id="expense-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Add Expense') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'expenses.store', 'method' => 'post','files' => true]) !!}
            <?php
            $lims_expense_category_list = DB::table('expense_categories')->where('is_active', true)->get();
            if (Auth::user()->role_id > 2)
              $lims_warehouse_list = DB::table('warehouses')->where([
                ['is_active', true],
                ['id', Auth::user()->warehouse_id]
              ])->get();
            else
              $lims_warehouse_list = DB::table('warehouses')->where('is_active', true)->get();
            $lims_account_list = \App\Models\Account::where('is_active', true)->get();
            ?>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>{{ __('Date') }}</label>
                <input type="text" name="created_at" class="form-control date" placeholder="{{__('db.Choose date')}}" value="{{date($general_setting->date_format,strtotime('now'))}}"/>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Expense Category') }} *</label>
                <select name="expense_category_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Expense Category...">
                  @foreach($lims_expense_category_list as $expense_category)
                  <option value="{{$expense_category->id}}">{{$expense_category->name . ' (' . $expense_category->code. ')'}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Warehouse') }} *</label>
                <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
                  @foreach($lims_warehouse_list as $warehouse)
                  <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Amount') }} *</label>
                <input type="number" name="amount" step="any" required class="form-control">
              </div>
              <div class="col-md-6 form-group">
                <label> {{ __('Account') }}</label>
                <select class="form-control selectpicker" name="account_id">
                  @foreach($lims_account_list as $account)
                  @if($account->is_default)
                  <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                  @else
                  <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                  @endif
                  @endforeach
                </select>
              </div>

               <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('db.Attach Document')}}</label>
                        <i class="dripicons-question" data-toggle="tooltip" title="Only jpg, jpeg, png, gif, pdf, csv, docx, xlsx and txt file is supported"></i>
                        <input type="file" name="document" class="form-control" />
                        @if($errors->has('extension'))
                            <span>
                                <strong>{{ $errors->first('extension') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="form-group">
              <label>{{ __('Note') }}</label>
              <textarea name="note" rows="3" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end expense modal -->
    <!-- income modal start -->
    <div id="income-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Add Income') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'incomes.store', 'method' => 'post']) !!}
            <?php
            $lims_income_category_list = DB::table('income_categories')->where('is_active', true)->get();
            if (Auth::user()->role_id > 2)
              $lims_warehouse_list = DB::table('warehouses')->where([
                ['is_active', true],
                ['id', Auth::user()->warehouse_id]
              ])->get();
            else
              $lims_warehouse_list = DB::table('warehouses')->where('is_active', true)->get();
            $lims_account_list = \App\Models\Account::where('is_active', true)->get();
            ?>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>{{ __('Date') }}</label>
                <input type="text" name="created_at" class="form-control date" placeholder="{{__('db.Choose date')}}" value="{{date($general_setting->date_format,strtotime('now'))}}"/>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Income Category') }} *</label>
                <select name="income_category_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Income Category...">
                  @foreach($lims_income_category_list as $income_category)
                  <option value="{{$income_category->id}}">{{$income_category->name . ' (' . $income_category->code. ')'}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Warehouse') }} *</label>
                <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
                  @foreach($lims_warehouse_list as $warehouse)
                  <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>{{ __('Amount') }} *</label>
                <input type="number" name="amount" step="any" required class="form-control">
              </div>
              <div class="col-md-6 form-group">
                <label> {{ __('Account') }}</label>
                <select class="form-control selectpicker" name="account_id">
                  @foreach($lims_account_list as $account)
                  @if($account->is_default)
                  <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                  @else
                  <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                  @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>{{ __('Note') }}</label>
              <textarea name="note" rows="3" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- income modal end -->

    <!-- sale return modal -->
    <div id="add-sale-return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          {!! Form::open(['route' => 'return-sale.create', 'method' => 'get']) !!}
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">Add Sale Return</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('Sale Reference') }} *</label>
                  <input type="text" name="reference_no" class="form-control">
                </div>
              </div>
            </div>
            {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
    <!-- end sale return modal -->

    <!-- purchase return modal -->
    <div id="add-purchase-return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          {!! Form::open(['route' => 'return-purchase.create', 'method' => 'get']) !!}
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">Add Purchase Return</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('Purchase Reference') }} *</label>
                  <input type="text" name="reference_no" class="form-control">
                </div>
              </div>
            </div>
            {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
    <!-- end purchase return modal -->

    <!-- account modal -->
    <div id="account-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Add Account') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'accounts.store', 'method' => 'post']) !!}
            <div class="form-group">
              <label>{{ __('Account No') }} *</label>
              <input type="text" name="account_no" required class="form-control">
            </div>
            <div class="form-group">
              <label>{{ __('name') }} *</label>
              <input type="text" name="name" required class="form-control">
            </div>
            <div class="form-group">
              <label>{{ __('Initial Balance') }}</label>
              <input type="number" name="initial_balance" step="any" class="form-control">
            </div>
            <div class="form-group">
              <label>{{ __('Note') }}</label>
              <textarea name="note" rows="3" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end account modal -->

    <!-- account statement modal -->
    <div id="account-statement-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Account Statement') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'accounts.statement', 'method' => 'post']) !!}
            <div class="row">
              <div class="col-md-6 form-group">
                <label> {{ __('Account') }}</label>
                <select class="form-control selectpicker" name="account_id">
                  @foreach($lims_account_list as $account)
                  <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label> {{ __('Type') }}</label>
                <select class="form-control selectpicker" name="type">
                  <option value="0">{{ __('All') }}</option>
                  <option value="1">{{ __('Debit') }}</option>
                  <option value="2">{{ __('Credit') }}</option>
                </select>
              </div>
              <div class="col-md-12 form-group">
                <label>{{ __('Choose Your Date') }}</label>
                <div class="input-group">
                  <input type="text" class="account-statement-daterangepicker-field form-control" required />
                  <input type="hidden" name="start_date" />
                  <input type="hidden" name="end_date" />
                </div>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end account statement modal -->

    <!-- warehouse modal -->
    <div id="warehouse-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Warehouse Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.warehouse', 'method' => 'post']) !!}

            <div class="form-group">
              <label>{{ __('Warehouse') }} *</label>
              <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true" id="warehouse-id" data-live-search-style="begins" title="Select warehouse...">
                @foreach($lims_warehouse_list as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end warehouse modal -->

    <!-- user modal -->
    <div id="user-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('User Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.user', 'method' => 'post']) !!}
            <?php
            $lims_user_list = DB::table('users')->where('is_active', true)->get();
            ?>
            <div class="form-group">
              <label>{{ __('User') }} *</label>
              <select name="user_id" class="selectpicker form-control" required data-live-search="true" id="user-id" data-live-search-style="begins" title="Select user...">
                @foreach($lims_user_list as $user)
                <option value="{{$user->id}}">{{$user->name . ' (' . $user->phone. ')'}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end user modal -->

    <!-- biller modal -->
    <div id="biller-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Biller Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.biller', 'method' => 'post']) !!}
            <?php
            $lims_biller_list = DB::table('billers')->where('is_active', true)->get();
            ?>
            <div class="form-group">
              <label>{{ __('Biller') }} *</label>
              <select name="biller_id" class="selectpicker form-control" required data-live-search="true" id="user-id" data-live-search-style="begins" title="Select biller...">
                @foreach($lims_biller_list as $biller)
                <option value="{{$biller->id}}">{{$biller->name . ' (' . $biller->phone_number. ')'}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end biller modal -->

    <!-- customer modal -->
    <div id="customer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Customer Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.customer', 'method' => 'post']) !!}
            <?php
            $lims_customer_list = DB::table('customers')->where('is_active', true)->get();
            ?>
            <div class="form-group">
              <label>{{ __('customer') }} *</label>
              <select name="customer_id" class="selectpicker form-control" required data-live-search="true" id="customer-id" data-live-search-style="begins" title="Select customer...">
                @foreach($lims_customer_list as $customer)
                <option value="{{$customer->id}}">{{$customer->name . ' (' . $customer->phone_number. ')'}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end customer modal -->

    <!-- customer group modal -->
    <div id="customer-group-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Customer Group Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.customer_group', 'method' => 'post']) !!}
            <?php
            $lims_customer_group_list = DB::table('customer_groups')->where('is_active', true)->get();
            ?>
            <div class="form-group">
              <label>{{ __('Customer Group') }} *</label>
              <select name="customer_group_id" class="selectpicker form-control" required data-live-search="true" id="customer-group-id" data-live-search-style="begins" title="Select customer group...">
                @foreach($lims_customer_group_list as $customer_group)
                <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end customer group modal -->

    <!-- supplier modal -->
    <div id="supplier-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{ __('Supplier Report') }}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{ __('The field labels marked with * are required input fields') }}.</small></p>
            {!! Form::open(['route' => 'report.supplier', 'method' => 'post']) !!}
            <?php
            $lims_supplier_list = DB::table('suppliers')->where('is_active', true)->get();
            ?>
            <div class="form-group">
              <label>{{ __('Supplier') }} *</label>
              <select name="supplier_id" class="selectpicker form-control" required data-live-search="true" id="supplier-id" data-live-search-style="begins" title="Select Supplier...">
                @foreach($lims_supplier_list as $supplier)
                <option value="{{$supplier->id}}">{{$supplier->name . ' (' . $supplier->phone_number. ')'}}</option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d') }}" />

            <div class="form-group">
              <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- end supplier modal -->
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
  @if(optional(Route::current())->getName() == 'sale.pos')
  <script type="text/javascript" src="<?php echo asset('vendor/keyboard/js/jquery.keyboard.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('vendor/keyboard/js/jquery.keyboard.extension-autocomplete.js') ?>"></script>
  @endif
  <script type="text/javascript" src="<?php echo asset('js/grasp_mobile_progress_circle-1.0.0.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('vendor/jquery.cookie/jquery.cookie.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('vendor/chart.js/Chart.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('js/charts-custom.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') ?>"></script>
  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <script type="text/javascript" src="<?php echo asset('js/front_rtl.js') ?>"></script>
  @else
  <script type="text/javascript" src="<?php echo asset('js/front.js') ?>"></script>
  @endif

  @if(optional(Route::current())->getName() != '/')
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
  <script type="text/javascript" src="<?php echo asset('vendor/datatable/buttons.bootstrap4.min.js') ?>"></script>
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
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery.cookie/jquery.cookie.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/chart.js/Chart.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../js/charts-custom.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') ?>"></script>
  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <script type="text/javascript" src="<?php echo asset('../../js/front_rtl.js') ?>"></script>
  @else
  <script type="text/javascript" src="<?php echo asset('../../js/front.js') ?>"></script>
  @endif

  @if(optional(Route::current())->getName() != '/')
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
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/jszip.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.bootstrap4.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.colVis.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.html5.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.printnew.js') ?>"></script>

  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/sum().js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.checkboxes.min.js') ?>"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

  <script type="text/javascript" src="{{ asset('js/barcode-qrcode-scanner_plugin.js') }}"></script>

  @endif
  @endif
  @stack('scripts')

  <script type="text/javascript">
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var theme = <?php echo json_encode($theme); ?>;
    if (theme == 'dark') {
      $('body').addClass('dark-mode');
      $('#switch-theme i').addClass('dripicons-brightness-low');
    } else {
      $('body').removeClass('dark-mode');
      $('#switch-theme i').addClass('dripicons-brightness-max');
    }
    $('#switch-theme').click(function() {
      if (theme == 'light') {
        theme = 'dark';
        var url = <?php echo json_encode(route('switchTheme', 'dark')); ?>;
        $('body').addClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-low');
      } else {
        theme = 'light';
        var url = <?php echo json_encode(route('switchTheme', 'light')); ?>;
        $('body').removeClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-max');
      }

      $.get(url, function(data) {
        console.log('theme changed to ' + theme);
      });
    });

    var alert_product = <?php echo json_encode($alert_product) ?>;

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

    $("div.alert").delay(4000);

    function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
        return true;
      }
      return false;
    }

    $(document).on("click", ".premium-notification-bell", function() {
      $.get('notifications/mark-as-read', function(data) {
        $("span.notification-number").text(alert_product);
      });
    });

    $("a#add-expense").click(function(e) {
      e.preventDefault();
      $('#expense-modal').modal();
    });

    $("a#add-income").click(function(e) {
      e.preventDefault();
      $('#income-modal').modal();
    });

    $("a#send-notification").click(function(e) {
      e.preventDefault();
      $('#notification-modal').modal();
    });

    $("a#add-account").click(function(e) {
      e.preventDefault();
      $('#account-modal').modal();
    });

    $("a#account-statement").click(function(e) {
      e.preventDefault();
      $('#account-statement-modal').modal();
    });

    $("a#profitLoss-link").click(function(e) {
      e.preventDefault();
      $("#profitLoss-report-form").submit();
    });

    $("a#report-link").click(function(e) {
      e.preventDefault();
      $("#product-report-form").submit();
    });

    $("a#purchase-report-link").click(function(e) {
      e.preventDefault();
      $("#purchase-report-form").submit();
    });

    $("a#sale-report-link").click(function(e) {
      e.preventDefault();
      $("#sale-report-form").submit();
    });
    $("a#sale-report-chart-link").click(function(e) {
      e.preventDefault();
      $("#sale-report-chart-form").submit();
    });

    $("a#payment-report-link").click(function(e) {
      e.preventDefault();
      $("#payment-report-form").submit();
    });

    $("a#warehouse-report-link").click(function(e) {
      e.preventDefault();
      $('#warehouse-modal').modal();
    });

    $("a#user-report-link").click(function(e) {
      e.preventDefault();
      $('#user-modal').modal();
    });

    $("a#biller-report-link").click(function(e) {
      e.preventDefault();
      $('#biller-modal').modal();
    });

    $("a#customer-report-link").click(function(e) {
      e.preventDefault();
      $('#customer-modal').modal();
    });

    $("a#customer-group-report-link").click(function(e) {
      e.preventDefault();
      $('#customer-group-modal').modal();
    });

    $("a#supplier-report-link").click(function(e) {
      e.preventDefault();
      $('#supplier-modal').modal();
    });

    $("a#due-report-link").click(function(e) {
      e.preventDefault();
      $("#customer-due-report-form").submit();
    });

    $("a#supplier-due-report-link").click(function(e) {
      e.preventDefault();
      $("#supplier-due-report-form").submit();
    });

    $(".account-statement-daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period) {
        var start_date = startDate.format('YYYY-MM-DD');
        var end_date = endDate.format('YYYY-MM-DD');
        var title = start_date + ' To ' + end_date;
        $(this).val(title);
        $('#account-statement-modal input[name="start_date"]').val(start_date);
        $('#account-statement-modal input[name="end_date"]').val(end_date);
      }
    });

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
