<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectService;
use App\Models\ServiceStage;
use App\Services\ContractTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ContractTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContractTemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ContractTemplateService();
        Storage::fake('local');
    }

    public function test_generates_docx_with_correct_placeholders(): void
    {
        $contract = Contract::factory()->create([
            'contract_number' => 'CNT-2026-1234',
            'contract_value' => 100000,
            'title' => 'Engineering Services Contract'
        ]);

        $filePath = $this->service->generateDocx($contract);

        $this->assertNotNull($filePath);
        $this->assertStringContainsString('.docx', $filePath);
        Storage::disk('local')->assertExists($filePath);
    }

    public function test_generates_pdf_with_dompdf(): void
    {
        $contract = Contract::factory()->create();

        $filePath = $this->service->generatePdfWithDompdf($contract);

        $this->assertNotNull($filePath);
        $this->assertStringContainsString('.pdf', $filePath);
        Storage::disk('local')->assertExists($filePath);
    }

    public function test_replaces_all_template_variables(): void
    {
        $contract = Contract::factory()->create([
            'contract_number' => 'CNT-2026-5678',
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertArrayHasKey('contract_number', $data);
        $this->assertArrayHasKey('client_name', $data);
        $this->assertArrayHasKey('contract_date', $data);
        $this->assertArrayHasKey('contract_value', $data);
        $this->assertEquals('CNT-2026-5678', $data['contract_number']);
    }

    public function test_formats_dates_correctly(): void
    {
        $contract = Contract::factory()->create([
            'contract_date' => now()->parse('2026-01-24')
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertStringContainsString('2026', $data['contract_date']);
        $this->assertStringContainsString('01', $data['contract_date']);
    }

    public function test_formats_currency_correctly(): void
    {
        $contract = Contract::factory()->create([
            'contract_value' => 123456.78
        ]);

        $data = $this->service->prepareContractData($contract);

        // Should format with thousands separator
        $this->assertMatchesRegularExpression('/123[,\s]?456/', $data['contract_value']);
    }

    public function test_handles_missing_template_gracefully(): void
    {
        $contract = Contract::factory()->create();

        // Even if template doesn't exist, should create basic template
        $filePath = $this->service->generateContract($contract, 'docx');

        $this->assertNotNull($filePath);
    }

    public function test_groups_services_by_stage(): void
    {
        $project = Project::factory()->create();
        $stage1 = ServiceStage::factory()->create(['name' => 'Design']);
        $stage2 = ServiceStage::factory()->create(['name' => 'Execution']);

        ProjectService::factory()->create([
            'project_id' => $project->id,
            'service_stage_id' => $stage1->id,
            'total_price' => 10000
        ]);
        ProjectService::factory()->create([
            'project_id' => $project->id,
            'service_stage_id' => $stage1->id,
            'total_price' => 15000
        ]);
        ProjectService::factory()->create([
            'project_id' => $project->id,
            'service_stage_id' => $stage2->id,
            'total_price' => 20000
        ]);

        $contract = Contract::factory()->create([
            'project_id' => $project->id
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertArrayHasKey('services_grouped', $data);
        $this->assertCount(2, $data['services_grouped']); // 2 stages
    }

    public function test_converts_numbers_to_words(): void
    {
        $result = $this->service->numberToWords(12345);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        // Should contain text representation
        $this->assertMatchesRegularExpression('/twelve|thousand|اثنا|ألف/i', $result);
    }

    public function test_handles_multiple_output_formats(): void
    {
        $contract = Contract::factory()->create();

        $docxPath = $this->service->generateContract($contract, 'docx');
        $pdfPath = $this->service->generateContract($contract, 'pdf');

        $this->assertStringContainsString('.docx', $docxPath);
        $this->assertStringContainsString('.pdf', $pdfPath);
        Storage::disk('local')->assertExists($docxPath);
        Storage::disk('local')->assertExists($pdfPath);
    }

    public function test_handles_arabic_text_rendering(): void
    {
        $contract = Contract::factory()->create([
            'title' => 'عقد خدمات هندسية'
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertEquals('عقد خدمات هندسية', $data['contract_title']);
    }

    public function test_calculates_total_contract_value_correctly(): void
    {
        $project = Project::factory()->create();

        ProjectService::factory()->create([
            'project_id' => $project->id,
            'total_price' => 25000
        ]);
        ProjectService::factory()->create([
            'project_id' => $project->id,
            'total_price' => 35000
        ]);

        $contract = Contract::factory()->create([
            'project_id' => $project->id,
            'contract_value' => 60000
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertEquals(60000, $data['total_value']);
    }

    public function test_includes_client_information(): void
    {
        $client = Client::factory()->create([
            'name' => 'Test Client Ltd',
            'email' => 'client@test.com',
            'phone' => '+968 1234 5678'
        ]);

        $contract = Contract::factory()->create([
            'client_id' => $client->id
        ]);

        $data = $this->service->prepareContractData($contract);

        $this->assertEquals('Test Client Ltd', $data['client_name']);
        $this->assertEquals('client@test.com', $data['client_email']);
        $this->assertEquals('+968 1234 5678', $data['client_phone']);
    }
}
