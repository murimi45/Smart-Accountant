<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\TenantCache;
use App\Support\TenantStorage;
use Tests\TestCase;

class TenantOpsTest extends TestCase
{
    public function test_tenant_cache_key_is_prefixed_with_school_id(): void
    {
        $this->assertSame('school:42:dashboard:summary', TenantCache::key('dashboard:summary', 42));
    }

    public function test_tenant_storage_path_includes_school_id(): void
    {
        $this->assertSame(
            'schools/7/exports/statements/file.zip',
            TenantStorage::path('exports/statements/file.zip', 7)
        );
    }

    public function test_platform_admin_requires_null_school_id(): void
    {
        $platform = new User(['role' => User::ROLE_PLATFORM, 'school_id' => null]);
        $tenantAdmin = new User(['role' => User::ROLE_ADMIN, 'school_id' => 1]);

        $this->assertTrue($platform->isPlatformAdmin());
        $this->assertFalse($tenantAdmin->isPlatformAdmin());
    }
}
