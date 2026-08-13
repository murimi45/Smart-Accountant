<?php

namespace Tests\Feature;

use App\Core\Modules\ModuleRegistry;
use App\Models\Module;
use App\Models\User;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_school_with_accountant_can_open_invoices(): void
    {
        $this->assertTrue($this->fixtures['schoolA']->hasModule(Module::SLUG_ACCOUNTANT));

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('invoices.index'))
            ->assertOk();
    }

    public function test_school_without_accountant_cannot_open_invoices(): void
    {
        ModuleRegistry::disableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_ACCOUNTANT
        );

        $this->assertFalse($this->fixtures['schoolA']->fresh()->hasModule(Module::SLUG_ACCOUNTANT));

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('invoices.index'))
            ->assertForbidden();
    }

    public function test_school_without_accountant_can_still_open_dashboard_and_students(): void
    {
        ModuleRegistry::disableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_ACCOUNTANT
        );

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('listStudents'))
            ->assertOk();
    }

    public function test_platform_admin_can_toggle_school_module(): void
    {
        $platform = User::unguarded(fn () => User::create([
            'school_id'          => null,
            'admin_name'         => 'Platform Ops',
            'email'              => 'platform-ops@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_PLATFORM,
            'two_factor_enabled' => false,
        ]));

        $school = $this->fixtures['schoolA'];

        $this->actingAs($platform)
            ->get(route('platform.school-modules.index'))
            ->assertOk()
            ->assertSee($school->school_name);

        $this->actingAs($platform)
            ->post(route('platform.school-modules.disable', [$school, Module::SLUG_ACCOUNTANT]))
            ->assertRedirect();

        $this->assertFalse($school->fresh()->hasModule(Module::SLUG_ACCOUNTANT));

        $this->actingAs($platform)
            ->post(route('platform.school-modules.enable', [$school, Module::SLUG_ACCOUNTANT]), [
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertTrue($school->fresh()->hasModule(Module::SLUG_ACCOUNTANT));
    }

    public function test_tenant_admin_cannot_access_platform_module_ui(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->get(route('platform.school-modules.index'))
            ->assertForbidden();
    }

    public function test_sidebar_hides_finance_when_accountant_disabled(): void
    {
        ModuleRegistry::disableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_ACCOUNTANT
        );

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Academics is ready')
            ->assertDontSee('Fee Summary')
            ->assertDontSee('Transaction History')
            ->assertDontSee('Financial Reports')
            ->assertDontSee('SMS Logs');
    }

    public function test_sidebar_shows_finance_when_accountant_enabled(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Fee Summary')
            ->assertSee('Transaction History')
            ->assertDontSee('Academics is ready');
    }

    public function test_hr_manager_can_open_hr_home_when_module_enabled(): void
    {
        ModuleRegistry::enableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_HR
        );

        $hr = User::unguarded(fn () => User::create([
            'school_id'          => $this->fixtures['schoolA']->id,
            'admin_name'         => 'HR Lead',
            'email'              => 'hr-lead@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_HR_MANAGER,
            'two_factor_enabled' => false,
        ]));

        $this->actingAs($hr)
            ->get(route('hr.index'))
            ->assertOk()
            ->assertSee('HR');
    }

    public function test_hr_home_blocked_without_module(): void
    {
        $hr = User::unguarded(fn () => User::create([
            'school_id'          => $this->fixtures['schoolA']->id,
            'admin_name'         => 'HR Lead',
            'email'              => 'hr-blocked@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_HR_MANAGER,
            'two_factor_enabled' => false,
        ]));

        $this->actingAs($hr)
            ->get(route('hr.index'))
            ->assertForbidden();
    }

    public function test_teacher_can_open_grading_home_when_module_enabled(): void
    {
        ModuleRegistry::enableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_GRADING
        );

        $teacher = User::unguarded(fn () => User::create([
            'school_id'          => $this->fixtures['schoolA']->id,
            'admin_name'         => 'Class Teacher',
            'email'              => 'teacher@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_TEACHER,
            'two_factor_enabled' => false,
        ]));

        $this->actingAs($teacher)
            ->get(route('grading.index'))
            ->assertOk()
            ->assertSee('Grading');
    }

    public function test_grading_home_blocked_without_module(): void
    {
        $teacher = User::unguarded(fn () => User::create([
            'school_id'          => $this->fixtures['schoolA']->id,
            'admin_name'         => 'Blocked Teacher',
            'email'              => 'teacher-blocked@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_TEACHER,
            'two_factor_enabled' => false,
        ]));

        $this->actingAs($teacher)
            ->get(route('grading.index'))
            ->assertForbidden();
    }

    public function test_accountant_cannot_open_grading_home(): void
    {
        ModuleRegistry::enableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_GRADING
        );

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('grading.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_teacher_via_users_ui(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->post(route('admins.store'), [
                'admin_name' => 'New Teacher',
                'email' => 'new-teacher@probe.test',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => User::ROLE_TEACHER,
            ])
            ->assertRedirect(route('admins.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-teacher@probe.test',
            'role' => User::ROLE_TEACHER,
            'school_id' => $this->fixtures['schoolA']->id,
        ]);
    }

    public function test_accountant_cannot_open_hr_home(): void
    {
        ModuleRegistry::enableForSchool(
            (int) $this->fixtures['schoolA']->id,
            Module::SLUG_HR
        );

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('hr.index'))
            ->assertForbidden();
    }
}
