<?php

use Illuminate\Support\Facades\Route;




Route::controller(landlord\SaasInstallController::class)->group(function () {
    Route::get('install/step-1', 'saasInstallStep1')->name('saas-install-step-1');
    Route::get('install/step-2', 'saasInstallStep2')->name('saas-install-step-2');
    Route::get('install/step-3', 'saasInstallStep3')->name('saas-install-step-3');
    Route::post('install/process', 'saasInstallProcess')->name('saas-install-process');
    Route::get('install/step-4', 'saasInstallStep4')->name('saas-install-step-4');
});

// Old installer URLs (…/saas/install/…) — keep working after URL change.
Route::redirect('saas/install/step-1', '/install/step-1', 301);
Route::redirect('saas/install/step-2', '/install/step-2', 301);
Route::redirect('saas/install/step-3', '/install/step-3', 301);
Route::redirect('saas/install/step-4', '/install/step-4', 301);



Route::get('/payment_cancel', [\App\Http\Controllers\WebUtilitiesController::class, 'paymentCancel']);
Route::get('/fail_url', [\App\Http\Controllers\WebUtilitiesController::class, 'failUrl']);


// Must not use the same URI as routes/tenant.php (switch-theme/{theme}) — Laravel keeps only one
// route per method+domain+URI, so the named route web.switchTheme was lost after tenant routes load.
Route::get('superadmin/switch-theme/{theme}', 'HomeController@switchTheme')->name('web.switchTheme');
Route::post('contact-form', 'landlord\LandingPageController@contactForm')->name('contactForm');
Route::post('tenant-checkout', 'Payment\PaymentController@tenantCheckout')->name('tenant.checkout');
Route::get('payment_success', 'Payment\PaymentController@success')->name('payment.success');
Route::get('bkash_payment_success', 'Payment\PaymentController@BkashSuccess')->name('payment.BkashSuccess');
Route::post('ssl_payment_success', 'Payment\PaymentController@SslSuccess')->name('payment.SslSuccess');

//bkash
Route::get('/bkash/callback','Payment\PaymentController@bkashCallback');


Route::get('update-coupon', 'CouponController@updateCoupon');
Route::post('payment/process', 'Payment\PaymentController@paymentProcees')->name('payment.process');
Route::post('payment/{payment_method}/pay', 'Payment\PaymentController@paymentPayPage')->name('payment.pay.page');
Route::post('payment/{payment_method}/pay/confirm', 'Payment\PaymentController@paymentPayConfirm')->name('payment.pay.confirm');
Route::post('payment/{payment_method}/pay/cancel', 'Payment\PaymentController@paymentPayCancel')->name('payment.pay.cancel');

// Paystack
Route::get('/payment/paystack/pay/callback', 'Payment\PaymentController@handleGatewayCallback')->name('payment.pay.callback');


Route::get('clear', [\App\Http\Controllers\WebUtilitiesController::class, 'clearCaches']);

Route::middleware(['cors'])->group(function () {
    Route::get('fetch-package-data/{id}', 'landlord\PackageController@fetchPackageData')->name('package.fetchData');
});

Route::controller(Auth\SuperAdminLoginController::class)->group(function () {
    Route::get('superadmin-login', 'login')->name('superadmin.login.form');
    Route::post('superadmin-login', 'store')->name('superadmin.login');
});

//landing page routes
Route::controller(landlord\LandingPageController::class)->group(function () {
    // Central GET / is in routes/central.php (loaded before tenant routes).
    //Route::get('sign-up', 'signUp')->name('signup');
    Route::post('send-otp', 'sendOTP')->name('send.otp');
    Route::post('verify-otp', 'verifyOTP')->name('verify.otp');
    Route::get('reset-client-db', 'resetClientDB');//This is only for reset tenant database for demo and check cron job of salprosaas cpanel
    Route::post('create-tenant', 'createTenant')->name('createTenant');
    Route::get('contact-for-renewal', 'contactForRenewal')->name('contactForRenewal');
    Route::post('renewal-subscription', 'renewSubscription')->name('renewSubscription');
    // Protected superadmin operations (client DB backup/update).
    Route::get('superadmin/backup-tenant-db', 'backupTenantDB')->name('superadmin.backupTenantDB')->middleware('superadminauth');
    Route::get('superadmin/update-tenant-db', 'updateTenantDB')->name('superadmin.updateTenantDB')->middleware('superadminauth');
    Route::get('superadmin/update-superadmin-db', 'updateSuperadminDB')->name('superadmin.updateSuperadminDB')->middleware('superadminauth');
});

//blog routes
Route::controller(landlord\BlogController::class)->group(function () {
    Route::get('/blog', 'list');
    Route::get('blog/{slug}', 'details');
});
//page routes
Route::controller(landlord\PageController::class)->group(function () {
    Route::get('page/{slug}', 'details');
});


Route::group(['prefix' => 'superadmin', 'middleware' => ['superadminauth']], function() {
    Route::get('/', [\App\Http\Controllers\WebUtilitiesController::class, 'superadminHomeRedirect'])->name('superadmin.home');
    Route::controller(landlord\DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('superadmin.dashboard');
        Route::get('new-release', 'newVersionReleasePage')->name('saas-new-release');
        Route::post('version-upgrade', 'versionUpgrade')->name('saas-version-upgrade');
    });
    Route::get('addon-list', 'HomeController@addonList');
    Route::controller(AddonInstallController::class)->group(function () {
        Route::post('ecommerce-install','ecommerceInstall')->name('saas.ecommerce.install');
        Route::post('woocommerce-install','woocommerceInstall')->name('saas.woocommerce.install');
        Route::post('api-install', 'apiInstall')->name('saas.api.install');
    });

    Route::controller(Auth\SuperAdminLoginController::class)->group(function () {
        Route::post('logout', 'logout')->name('superadmin.logout');
    });
    //setting routes
    Route::controller(landlord\SettingController::class)->group(function () {
        Route::get('general-setting', 'superadminGeneralSetting')->name('superadminGeneralSetting');
        Route::post('general-setting/store', 'superadminGeneralSettingStore')->name('superadminGeneralSetting.store');
        Route::get('mail_setting', 'superadminMailSetting')->name('superadminMailSetting');
        Route::post('mail_setting_store', 'superadminMailSettingStore')->name('superadminMailSettingStore');
    });
    //user routes
    Route::controller(UserController::class)->group(function () {
        Route::get('user/profile/{id}','superadminProfile')->name('user.superadminProfile');
        Route::put('user/update_profile/{id}', 'profileUpdate')->name('user.superadminProfileUpdate');
        Route::put('user/changepass/{id}', 'changePassword')->name('user.superadminPassword');
    });
    //client routes
    Route::controller(landlord\ClientController::class)->group(function () {
        Route::get('clients', 'index')->name('clients.index');
        Route::post('clients/store', 'store')->name('clients.store');
        Route::delete('clients/destroy/{id}', 'destroy')->name('clients.destroy');
        Route::post('clients/deletebyselection', 'deleteBySelection')->name('clients.deleteBySelection');
        Route::post('clients/renew', 'renew')->name('clients.renew');
        Route::post('clients/change-package', 'changePackage')->name('clients.changePackage');
        Route::post('clients/add-custom-domain', 'addCustomDomain')->name('clients.addCustomDomain');
        Route::get('clients/check-subdomain', 'checkSubdomain')->name('clients.checkSubdomain');
    });
    //hero routes
    Route::controller(landlord\HeroController::class)->group(function () {
        Route::get('hero-section', 'index');
        Route::post('hero-section/store', 'store')->name('heroSection.store');
    });
    //faq routes
    Route::controller(landlord\FaqController::class)->group(function () {
        Route::get('faq-section', 'index');
        Route::post('faq-section/store', 'store')->name('faqSection.store');
    });
    //module routes
    Route::controller(landlord\ModuleController::class)->group(function () {
        Route::get('module-section', 'index');
        Route::post('module-section/store', 'store')->name('module.store');
    });
    //features routes
    Route::controller(landlord\FeaturesController::class)->group(function () {
        Route::get('feature-section', 'index');
        Route::post('feature-section/store', 'store')->name('feature.store');
    });
    //testimonial routes
    Route::controller(landlord\TestimonialController::class)->group(function () {
        Route::get('testimonial-section', 'index');
        Route::post('testimonial-section/store', 'store')->name('testimonial.store');
        Route::post('testimonial-section/update', 'update')->name('testimonial.update');
        Route::post('testimonial-section/sort', 'sort');
        Route::post('testimonial-section/delete/{id}', 'delete')->name('testimonial.delete');
    });
    //tenant signup description routes
    Route::controller(landlord\TenantSignupDescriptionController::class)->group(function () {
        Route::get('tenant-signup-description', 'index');
        Route::post('tenant-signup-description/store', 'store')->name('tenantSignupDescription.store');
    });
    //blog routes
    Route::controller(landlord\BlogController::class)->group(function () {
        Route::get('blog-section', 'index');
        Route::post('blog-section/store', 'store')->name('blog.store');
        Route::get('blog-section/edit/{id}', 'edit');
        Route::post('blog-section/update', 'update')->name('blog.update');
        Route::post('blog-section/sort', 'sort');
        Route::post('blog-section/delete/{id}', 'delete')->name('blog.delete');
    });
    //page routes
    Route::controller(landlord\PageController::class)->group(function () {
        Route::get('page-section', 'index');
        Route::post('page-section/store', 'store')->name('superadmin.page.store');
        Route::get('page-section/edit/{id}', 'edit');
        Route::post('page-section/update', 'update')->name('superadmin.page.update');
        Route::post('page-section/sort', 'sort');
        Route::post('page-section/delete/{id}', 'delete')->name('superadmin.page.delete');
    });
    //social routes
    Route::controller(landlord\SocialController::class)->group(function () {
        Route::get('social-section', 'index');
        Route::post('social-section/store', 'store')->name('social.store');
        Route::post('social-section/update', 'update')->name('social.update');
        Route::post('social-section/sort', 'sort');
        Route::post('social-section/delete/{id}', 'delete')->name('social.delete');
    });

    //package routes
    Route::resource('packages', landlord\PackageController::class);
    //payment routes
    Route::resource('payments', landlord\PaymentController::class);

    // coupon routes
    Route::controller(landlord\CouponController::class)->group(function () {
        Route::get('coupon/gencode', 'generateCode');
        Route::post('coupon/deletebyselection', 'deleteBySelection');
    });
    Route::resource('coupon', landlord\CouponController::class);

    // Route::resource('coupons', landlord\CouponController::class);
    //language routes
    Route::controller(landlord\LanguageController::class)->group(function () {
        Route::get('languages', 'index')->name('languages.index');
        Route::post('languages/store', 'store')->name('languages.store');
        Route::post('languages/update', 'update')->name('languages.update');
        Route::post('languages/destroy/{id}', 'destroy')->name('languages.destroy');
        Route::get('languages/{langCode}/translation', 'editTranslation')->name('languages.editTranslation');
        Route::post('languages/{langCode}/update',  'updateTranslation')->name('languages.updateTranslation');


    });
    //ticket routes
    Route::controller(landlord\TicketController::class)->group(function () {
        Route::get('tickets','index')->name('superadmin.tickets.index');
        Route::get('tickets/create','create')->name('superadmin.tickets.create');
        Route::post('tickets','store')->name('superadmin.tickets.store');
        Route::get('tickets/{id}','show')->name('superadmin.tickets.show');
        Route::post('tickets/{id}/reply','reply')->name('superadmin.tickets.reply');
        Route::delete('tickets/{id}','destroy')->name('superadmin.tickets.destroy');
    });
});

// Central domain GET / is registered in routes/central.php (loaded before this file and tenant routes).
