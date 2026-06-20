<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\StudentEnrollment;
use Illuminate\Http\UploadedFile;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class OpeningBalanceImportTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_opening_balance_import_adds_arrears_to_invoice(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $admission = $tenant['student']->admission;
        $termLabel = 'Term 1 - 2026';

        $content = "admission,term,opening_balance,notes\n";
        $content .= "{$admission},{$termLabel},4500,Arrears from 2025\n";

        $file = UploadedFile::fake()->createWithContent('opening.csv', $content);

        $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'opening_balances'), ['file' => $file])
            ->assertRedirect();

        $invoice = Invoice::withoutGlobalScopes()
            ->where('school_id', $this->fixtures['schoolA']->id)
            ->where('student_id', $tenant['student']->id)
            ->first();

        $this->assertNotNull($invoice);
        $this->assertSame(4500.0, (float) $invoice->imported_opening_balance);
        $this->assertTrue(
            $invoice->items()->where('is_opening_balance', true)->where('amount', 4500)->exists()
        );
        $this->assertGreaterThanOrEqual(4500.0, (float) $invoice->total_amount);
    }

    public function test_opening_balance_requires_enrollment(): void
    {
        $tenant = $this->fixtures['tenantB'];
        $admission = $tenant['student']->admission;

        $content = "admission,term,opening_balance,notes\n";
        $content .= "{$admission},Term 1 - 2026,1000,Test\n";

        $file = UploadedFile::fake()->createWithContent('opening.csv', $content);

        $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'opening_balances'), ['file' => $file])
            ->assertRedirect()
            ->assertSessionHas('import_errors');
    }
}
