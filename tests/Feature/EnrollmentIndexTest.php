<?php

namespace Tests\Feature;

use App\Models\Schools;
use App\Models\User;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class EnrollmentIndexTest extends TestCase
{
    public function test_enrollment_index_loads_for_a_school_with_terms(): void
    {
        $fixtures = TenantFixtureBuilder::createPair();

        $this->actingAs($fixtures['adminA'])
            ->get(route('enrollment.index'))
            ->assertOk();
    }

    public function test_enrollment_index_loads_when_school_has_no_term(): void
    {
        $school = Schools::factory()->create();
        $admin = User::factory()
            ->admin()
            ->forSchool($school)
            ->create([
                'two_factor_enabled' => false,
            ]);

        $this->actingAs($admin)
            ->get(route('enrollment.index'))
            ->assertOk()
            ->assertSee('No active term')
            ->assertSee('Create an academic year and a term');
    }

    public function test_student_list_resolves_controller(): void
    {
        $fixtures = TenantFixtureBuilder::createPair();

        $this->actingAs($fixtures['adminA'])
            ->get(route('listStudents'))
            ->assertOk();
    }
}
