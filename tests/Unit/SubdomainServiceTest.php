<?php

namespace Tests\Unit;

use App\Services\SubdomainService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubdomainServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        putenv('CENTRAL_DOMAIN=example.com');
        $_ENV['CENTRAL_DOMAIN'] = 'example.com';
        $_SERVER['CENTRAL_DOMAIN'] = 'example.com';
    }

    protected function setUpDatabase(): void
    {
        if (!Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('domains')) {
            Schema::create('domains', function (Blueprint $table) {
                $table->increments('id');
                $table->string('domain', 255)->unique();
                $table->string('tenant_id');
                $table->timestamps();
            });
        }
    }

    public function test_sanitize_subdomain_basic_examples(): void
    {
        $service = new SubdomainService();

        $this->assertSame('mystorename', $service->sanitizeSubdomain('My Store Name'));
        $this->assertSame('abctraders', $service->sanitizeSubdomain('A.B.C Traders'));
        $this->assertSame('shreeganeshagro2', $service->sanitizeSubdomain('Shree Ganesh Agro 2'));
        $this->assertSame('storename-name', $service->sanitizeSubdomain('Store Name!!   --Name'));
    }

    public function test_generate_unique_subdomain_increments_on_tenant_table_collision(): void
    {
        $this->setUpDatabase();

        DB::table('tenants')->insert([
            'id' => 'mystore',
            'data' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = new SubdomainService();
        $this->assertSame('mystore2', $service->generateUniqueSubdomain('My Store'));
    }

    public function test_generate_unique_subdomain_increments_on_domains_table_collision(): void
    {
        $this->setUpDatabase();

        DB::table('domains')->insert([
            'domain' => 'mystore.example.com',
            'tenant_id' => 'someTenant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = new SubdomainService();
        $this->assertSame('mystore2', $service->generateUniqueSubdomain('mystore'));
    }

    public function test_generate_unique_subdomain_defaults_when_input_sanitizes_empty(): void
    {
        $this->setUpDatabase();

        $service = new SubdomainService();
        $generated = $service->generateUniqueSubdomain('!!!');

        $this->assertSame('client', $generated);
    }
}

