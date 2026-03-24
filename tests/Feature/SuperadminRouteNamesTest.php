<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Guards against RouteNotFoundException from typos in landlord/superadmin Blade views.
 */
class SuperadminRouteNamesTest extends TestCase
{
    /** @return iterable<string, array{string}>
     */
    public static function superadminRouteNames(): iterable
    {
        $names = [
            'superadmin.home',
            'superadmin.dashboard',
            'saas-new-release',
            'superadminGeneralSetting',
            'superadminGeneralSetting.store',
            'superadminMailSetting',
            'superadminMailSettingStore',
            'superadmin.logout',
            'user.superadminProfile',
            'user.superadminProfileUpdate',
            'user.superadminPassword',
            'clients.index',
            'clients.store',
            'clients.destroy',
            'clients.deleteBySelection',
            'clients.renew',
            'clients.changePackage',
            'clients.addCustomDomain',
            'clients.checkSubdomain',
            'superadmin.backupTenantDB',
            'superadmin.updateTenantDB',
            'superadmin.updateSuperadminDB',
            'payments.index',
            'coupon.index',
            'coupon.store',
            'coupon.destroy',
            'coupon.update',
            'packages.index',
            'packages.create',
            'packages.edit',
            'packages.store',
            'packages.update',
            'packages.destroy',
            'superadmin.tickets.index',
            'superadmin.tickets.create',
            'superadmin.tickets.store',
            'superadmin.tickets.show',
            'superadmin.tickets.reply',
            'superadmin.tickets.destroy',
            'heroSection.store',
            'module.store',
            'feature.store',
            'faqSection.store',
            'testimonial.store',
            'testimonial.update',
            'testimonial.delete',
            'tenantSignupDescription.store',
            'blog.store',
            'blog.update',
            'blog.delete',
            'superadmin.page.store',
            'superadmin.page.update',
            'superadmin.page.delete',
            'social.store',
            'social.update',
            'social.delete',
            'languages.index',
            'languages.store',
            'languages.update',
            'languages.destroy',
            'languages.editTranslation',
            'languages.updateTranslation',
            'switchTheme',
            'contactForm',
            'renewSubscription',
        ];
        foreach ($names as $name) {
            yield $name => [$name];
        }
    }

    /** @dataProvider superadminRouteNames */
    public function test_superadmin_named_route_is_registered(string $name): void
    {
        $this->assertTrue(
            Route::has($name),
            "Route [{$name}] is not registered — fix the name or register the route."
        );
    }
}
