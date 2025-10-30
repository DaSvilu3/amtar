<?php

namespace App\Services;

use App\Models\Contract;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractTemplateService
{
    /**
     * Generate contract document from template
     *
     * @param Contract $contract
     * @param string|null $format 'docx', 'pdf', or 'both'
     * @return string Path to generated file
     */
    public function generateContract(Contract $contract, ?string $format = null): string
    {
        $format = $format ?? config('project.contract.output_format', 'docx');

        // Load contract with relationships
        $contract->load(['client', 'project.mainService', 'project.subService', 'project.servicePackage', 'projectServices.service', 'projectServices.serviceStage']);

        // Generate DOCX first
        $docxPath = $this->generateDocx($contract);

        // Generate PDF if requested
        if ($format === 'pdf' && config('project.contract.enable_pdf', false)) {
            return $this->convertToPdf($docxPath);
        }

        if ($format === 'both' && config('project.contract.enable_pdf', false)) {
            $pdfPath = $this->convertToPdf($docxPath);
            return ['docx' => $docxPath, 'pdf' => $pdfPath];
        }

        return $docxPath;
    }

    /**
     * Generate DOCX file from template
     *
     * @param Contract $contract
     * @return string Path to generated DOCX file
     */
    protected function generateDocx(Contract $contract): string
    {
        $templatePath = config('project.contract.template_path', 'templates/contract_template.docx');
        $fullTemplatePath = storage_path('app/' . $templatePath);

        // Check if template exists
        if (!file_exists($fullTemplatePath)) {
            // Create a basic template if it doesn't exist
            $this->createBasicTemplate($fullTemplatePath);
        }

        // Load template
        $templateProcessor = new TemplateProcessor($fullTemplatePath);

        // Set template values
        $this->setTemplateValues($templateProcessor, $contract);

        // Set company information
        $this->setCompanyInfo($templateProcessor);

        // Set services table
        $this->setServicesTable($templateProcessor, $contract);

        // Generate unique filename
        $filename = 'contract_' . $contract->contract_number . '_' . time() . '.docx';
        $outputPath = 'contracts/' . $filename;
        $fullOutputPath = storage_path('app/' . $outputPath);

        // Ensure contracts directory exists
        if (!file_exists(storage_path('app/contracts'))) {
            mkdir(storage_path('app/contracts'), 0755, true);
        }

        // Save the generated document
        $templateProcessor->saveAs($fullOutputPath);

        return $outputPath;
    }

    /**
     * Set template values from contract data
     *
     * @param TemplateProcessor $templateProcessor
     * @param Contract $contract
     * @return void
     */
    protected function setTemplateValues(TemplateProcessor $templateProcessor, Contract $contract): void
    {
        $variables = config('project.contract.template_variables', []);

        foreach ($variables as $placeholder => $path) {
            $value = $this->getNestedValue($contract, $path);
            $templateProcessor->setValue($placeholder, $value ?? '');
        }

        // Additional calculated values
        $templateProcessor->setValue('today_date', now()->format('Y-m-d'));
        $templateProcessor->setValue('today_date_formatted', now()->format('d F Y'));

        // Service information
        if ($contract->project) {
            $templateProcessor->setValue('main_service', $contract->project->mainService->name ?? '');
            $templateProcessor->setValue('sub_service', $contract->project->subService->name ?? '');
            $templateProcessor->setValue('service_package', $contract->project->servicePackage->name ?? '');
            $templateProcessor->setValue('project_location', $contract->project->location ?? '');
            $templateProcessor->setValue('project_status', ucfirst($contract->project->status) ?? '');
        }

        // Contract status
        $templateProcessor->setValue('contract_status', ucfirst($contract->status));

        // Formatted amounts
        if ($contract->value) {
            $templateProcessor->setValue('budget_formatted', number_format($contract->value, 2));
            $templateProcessor->setValue('budget_words', $this->numberToWords($contract->value));
        }
    }

    /**
     * Set company information
     *
     * @param TemplateProcessor $templateProcessor
     * @return void
     */
    protected function setCompanyInfo(TemplateProcessor $templateProcessor): void
    {
        $companyInfo = config('project.contract.company_info', []);

        foreach ($companyInfo as $key => $value) {
            if ($key !== 'logo_path') {
                $templateProcessor->setValue('company_' . $key, $value ?? '');
            }
        }

        // Set logo if exists
        $logoPath = config('project.contract.company_info.logo_path');
        if ($logoPath && Storage::exists($logoPath)) {
            try {
                $templateProcessor->setImageValue('company_logo', [
                    'path' => storage_path('app/' . $logoPath),
                    'width' => 200,
                    'height' => 80,
                    'ratio' => true
                ]);
            } catch (\Exception $e) {
                // Logo placeholder not found in template, skip
            }
        }
    }

    /**
     * Set services table in template
     *
     * @param TemplateProcessor $templateProcessor
     * @param Contract $contract
     * @return void
     */
    protected function setServicesTable(TemplateProcessor $templateProcessor, Contract $contract): void
    {
        // Get services grouped by stage
        $servicesByStage = $contract->projectServices
            ->groupBy('serviceStage.name')
            ->map(function ($services, $stageName) {
                return [
                    'stage' => $stageName,
                    'services' => $services->pluck('service.name')->toArray(),
                    'count' => $services->count(),
                ];
            })
            ->values();

        // Try to clone table rows if template supports it
        try {
            $templateProcessor->cloneRow('service_stage', $servicesByStage->count());

            foreach ($servicesByStage as $index => $stageData) {
                $rowIndex = $index + 1;
                $templateProcessor->setValue('service_stage#' . $rowIndex, $stageData['stage']);
                $templateProcessor->setValue('service_list#' . $rowIndex, implode(', ', $stageData['services']));
                $templateProcessor->setValue('service_count#' . $rowIndex, $stageData['count']);
            }
        } catch (\Exception $e) {
            // Table cloning not supported in template, set as simple list
            $allServices = $contract->projectServices
                ->pluck('service.name')
                ->implode("\n");

            try {
                $templateProcessor->setValue('services_list', $allServices);
            } catch (\Exception $e) {
                // Services placeholder not found, skip
            }
        }

        // Set total service count
        try {
            $templateProcessor->setValue('total_services', $contract->projectServices->count());
        } catch (\Exception $e) {
            // Placeholder not found, skip
        }
    }

    /**
     * Get nested value from object using dot notation
     *
     * @param mixed $object
     * @param string $path
     * @return mixed
     */
    protected function getNestedValue($object, string $path)
    {
        // Check for formatting options (e.g., "created_at|date:Y-m-d")
        if (Str::contains($path, '|')) {
            [$path, $format] = explode('|', $path, 2);
            $value = data_get($object, $path);
            return $this->formatValue($value, $format);
        }

        return data_get($object, $path);
    }

    /**
     * Format value based on format string
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    protected function formatValue($value, string $format)
    {
        if (Str::startsWith($format, 'date:')) {
            $dateFormat = Str::after($format, 'date:');
            return $value ? date($dateFormat, strtotime($value)) : '';
        }

        if ($format === 'number') {
            return number_format((float)$value, 2);
        }

        if ($format === 'uppercase') {
            return strtoupper($value);
        }

        if ($format === 'lowercase') {
            return strtolower($value);
        }

        return $value;
    }

    /**
     * Convert number to words (basic implementation for Arabic/English)
     *
     * @param float $number
     * @return string
     */
    protected function numberToWords(float $number): string
    {
        // Simple English conversion (you can enhance this for Arabic)
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $words = $formatter->format($number);

        // Add currency
        $currency = config('project.contract.company_info.currency', 'Omani Rials');
        return ucfirst($words) . ' ' . $currency;
    }

    /**
     * Convert DOCX to PDF
     *
     * @param string $docxPath
     * @return string Path to PDF file
     */
    protected function convertToPdf(string $docxPath): string
    {
        $fullDocxPath = storage_path('app/' . $docxPath);
        $pdfPath = str_replace('.docx', '.pdf', $docxPath);
        $fullPdfPath = storage_path('app/' . $pdfPath);
        $outputDir = dirname($fullPdfPath);

        // Get PDF conversion command from config
        $command = config('project.contract.pdf_command');
        $command = str_replace('{input}', escapeshellarg($fullDocxPath), $command);
        $command = str_replace('{output_dir}', escapeshellarg($outputDir), $command);

        // Execute conversion
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($fullPdfPath)) {
            throw new \Exception('Failed to convert DOCX to PDF: ' . implode("\n", $output));
        }

        return $pdfPath;
    }

    /**
     * Create a basic template if none exists
     *
     * @param string $templatePath
     * @return void
     */
    protected function createBasicTemplate(string $templatePath): void
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Set default font
        $phpWord->setDefaultFontName(config('project.contract.fonts.default', 'Arial'));
        $phpWord->setDefaultFontSize(12);

        // Add section
        $section = $phpWord->addSection([
            'marginLeft' => 1000,
            'marginRight' => 1000,
            'marginTop' => 1000,
            'marginBottom' => 1000,
        ]);

        // Add header
        $header = $section->addHeader();
        $header->addText('${company_name}', ['bold' => true, 'size' => 16]);
        $header->addText('${company_address}');
        $header->addText('Tel: ${company_phone} | Email: ${company_email}');

        // Add title
        $section->addText('CONTRACT', ['bold' => true, 'size' => 18], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Contract details
        $section->addText('Contract Number: ${contract_number}', ['bold' => true]);
        $section->addText('Date: ${today_date_formatted}');
        $section->addTextBreak(1);

        // Project information
        $section->addText('PROJECT INFORMATION', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addText('Project Name: ${project_name}');
        $section->addText('Project Number: ${project_number}');
        $section->addText('Location: ${project_location}');
        $section->addTextBreak(1);

        // Client information
        $section->addText('CLIENT INFORMATION', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addText('Name: ${client_name}');
        $section->addText('Company: ${client_company}');
        $section->addText('Email: ${client_email}');
        $section->addText('Phone: ${client_phone}');
        $section->addTextBreak(1);

        // Services
        $section->addText('SERVICES', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addText('Main Service: ${main_service}');
        $section->addText('Sub Service: ${sub_service}');
        $section->addText('Package: ${service_package}');
        $section->addTextBreak(1);

        // Financial details
        $section->addText('FINANCIAL DETAILS', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addText('Total Value: ${budget_formatted} ${currency}');
        $section->addText('In Words: ${budget_words}');
        $section->addText('Start Date: ${start_date}');
        $section->addText('End Date: ${end_date}');
        $section->addTextBreak(1);

        // Terms
        $section->addText('TERMS AND CONDITIONS', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addText('${terms}');
        $section->addTextBreak(2);

        // Signatures
        $section->addText('SIGNATURES', ['bold' => true, 'size' => 14, 'underline' => 'single']);
        $section->addTextBreak(2);

        $textrun = $section->addTextRun();
        $textrun->addText('Company Representative: _______________', null, ['alignment' => 'left']);
        $section->addTextBreak(2);

        $textrun2 = $section->addTextRun();
        $textrun2->addText('Client Signature: _______________', null, ['alignment' => 'left']);

        // Add footer
        $footer = $section->addFooter();
        $footer->addPreserveText('Page {PAGE} of {NUMPAGES}', null, ['alignment' => 'center']);

        // Ensure directory exists
        $dir = dirname($templatePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save template
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($templatePath);
    }

    /**
     * Download contract file
     *
     * @param string $filePath
     * @param string|null $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadContract(string $filePath, ?string $filename = null)
    {
        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'Contract file not found');
        }

        $filename = $filename ?? basename($filePath);

        return response()->download($fullPath, $filename, [
            'Content-Type' => $this->getMimeType($filePath),
        ]);
    }

    /**
     * Get mime type based on file extension
     *
     * @param string $filePath
     * @return string
     */
    protected function getMimeType(string $filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return match($extension) {
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
