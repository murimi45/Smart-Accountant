<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Support\CsvStream;
use Illuminate\Http\UploadedFile;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class BulkImportExportTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_admin_can_export_students_csv(): void
    {
        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('bulk.export', 'students'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_import_students_from_csv(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $termLabel = 'Term 1 - 2026';
        $className = $tenant['class']->name;

        $content = "admission,full_name,phone,guardian_name,gender,class,term,stream\n";
        $content .= "BULK001,Bulk Student,0700000000,Guardian,male,{$className},{$termLabel},\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $content);

        $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'students'), ['file' => $file])
            ->assertRedirect();

        $this->assertNotNull(
            Student::withoutGlobalScopes()
                ->where('school_id', $this->fixtures['schoolA']->id)
                ->where('admission', 'BULK001')
                ->first()
        );
    }

    public function test_accountant_can_access_bulk_page(): void
    {
        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('bulk.index'))
            ->assertOk()
            ->assertSee('Bulk Import / Export');
    }
}
