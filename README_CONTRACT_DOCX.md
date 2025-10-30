# Contract DOCX Template System - Summary

## What Was Implemented

Your contract printing system now generates **professional Microsoft Word documents** (DOCX) instead of just HTML/PDF. This gives you:

✅ **Professional DOCX Templates** - Fully customizable Word documents
✅ **Company Branding** - Add your logo and company information
✅ **Dynamic Data** - Auto-populate contract details
✅ **Arabic Support** - RTL text and Arabic fonts
✅ **Service Tables** - Automatically formatted service listings
✅ **PDF Export** - Optional PDF conversion (requires LibreOffice)
✅ **Download & Preview** - Multiple viewing options

---

## Quick Start

### 1. Download a Contract as DOCX

```php
// In your blade view (e.g., contracts/show.blade.php)
<a href="{{ route('admin.contracts.download-docx', $contract) }}" class="btn btn-primary">
    <i class="fas fa-file-word"></i> Download DOCX
</a>
```

Visit: `/admin/contracts/{contract}/download-docx`

### 2. Update Company Information

Edit `.env`:
```env
COMPANY_NAME="AMTAR Engineering"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 12345678"
COMPANY_EMAIL="info@amtar.om"
COMPANY_WEBSITE="www.amtar.om"
```

### 3. Add Your Logo

1. Save your logo as `logo.png` (200x80px recommended)
2. Upload to: `storage/app/templates/logo.png`
3. Done! Logo will appear on all contracts

---

## Files Created

### Core Files

1. **[app/Services/ContractTemplateService.php](app/Services/ContractTemplateService.php)**
   - Main service for generating DOCX documents
   - Handles template processing
   - Manages variable replacement
   - Creates PDF conversions

2. **[config/project.php](config/project.php)** (Updated)
   - Added `contract` configuration section
   - Template paths
   - Company information
   - Font settings
   - Variable mappings

3. **[app/Http/Controllers/Admin/ContractController.php](app/Http/Controllers/Admin/ContractController.php)** (Updated)
   - Added `downloadDocx()` method
   - Added `downloadPdf()` method
   - Added `preview()` method

### Documentation

4. **[CONTRACT_TEMPLATE_GUIDE.md](CONTRACT_TEMPLATE_GUIDE.md)**
   - Complete user guide
   - Template creation instructions
   - Variable reference
   - Troubleshooting

5. **[README_CONTRACT_DOCX.md](README_CONTRACT_DOCX.md)** (This file)
   - Quick reference
   - Summary of features

### Routes Added

```php
GET /admin/contracts/{contract}/download-docx  // Download as Word
GET /admin/contracts/{contract}/download-pdf   // Download as PDF
GET /admin/contracts/{contract}/preview        // Preview inline
```

### Directories Created

```
storage/app/
├── templates/              # Template files
│   ├── contract_template.docx  (auto-generated)
│   └── logo.png           # Your company logo
└── contracts/             # Generated contracts
    └── contract_CNT-2025-0001_timestamp.docx
```

---

## Configuration Overview

All settings in [config/project.php](config/project.php):

```php
'contract' => [
    // Format: 'docx', 'pdf', or 'both'
    'output_format' => 'docx',

    // Template file location
    'template_path' => 'templates/contract_template.docx',

    // PDF generation (requires LibreOffice)
    'enable_pdf' => false,

    // Company info (from .env)
    'company_info' => [
        'name' => env('COMPANY_NAME', 'AMTAR Engineering'),
        'address' => env('COMPANY_ADDRESS', 'Muscat, Oman'),
        // ... more fields
    ],

    // Available template variables
    'template_variables' => [
        'project_name' => 'name',
        'client_name' => 'client.name',
        'contract_date' => 'created_at|date:Y-m-d',
        // ... 20+ variables
    ],

    // Font support for Arabic
    'fonts' => [
        'default' => 'Arial',
        'arabic' => 'Traditional Arabic',
    ],
],
```

---

## Available Template Variables

Use these in your DOCX template with `${variable}` syntax:

### Contract Info
- `${contract_number}` - CNT-2025-0001
- `${contract_status}` - draft/active/expired
- `${today_date}` - 2025-10-30
- `${today_date_formatted}` - 30 October 2025

### Project Info
- `${project_name}` - Project name
- `${project_number}` - PRJ-2025-0001
- `${project_location}` - Location
- `${main_service}` - Engineering
- `${sub_service}` - Consultation
- `${service_package}` - Complete Package

### Client Info
- `${client_name}` - Client full name
- `${client_company}` - Company name
- `${client_email}` - Email
- `${client_phone}` - Phone

### Financial
- `${budget}` - 5000.00
- `${budget_formatted}` - 5,000.00
- `${budget_words}` - Five Thousand Omani Rials
- `${currency}` - OMR

### Dates
- `${start_date}` - 2025-01-01
- `${end_date}` - 2025-12-31

### Company
- `${company_name}` - Your company
- `${company_address}` - Your address
- `${company_phone}` - Your phone
- `${company_email}` - Your email
- `${company_logo}` - Logo image

---

## How It Works

### 1. Template Processing

```
User clicks "Download DOCX"
        ↓
ContractController::downloadDocx()
        ↓
ContractTemplateService::generateContract()
        ↓
Load template from storage/app/templates/
        ↓
Replace ${variables} with actual data
        ↓
Process service tables
        ↓
Save to storage/app/contracts/
        ↓
Return file for download
```

### 2. Auto-Template Creation

On first run, if no template exists:
```php
The service automatically creates a basic template with:
- Company header
- Contract details section
- Project information
- Client information
- Services table
- Financial details
- Terms and conditions
- Signature blocks
```

### 3. Service Table Generation

The template service automatically:
1. Groups services by stage
2. Creates table rows for each stage
3. Lists services within each stage
4. Counts services per stage

---

## Customizing the Template

### Method 1: Edit Auto-Generated Template

```bash
# First, generate a contract to create the template
# Visit: /admin/contracts/1/download-docx

# Template is now at:
storage/app/templates/contract_template.docx

# Download, edit in Word, upload back
```

### Method 2: Create From Scratch

1. Open Microsoft Word
2. Design your contract layout
3. Add placeholders like `${project_name}`
4. Save as `contract_template.docx`
5. Upload to `storage/app/templates/`

### Adding Dynamic Tables

Create a table row with:
```
| ${service_stage} | ${service_list} | ${service_count} |
```

The system will automatically duplicate this row for each service stage.

---

## Usage Examples

### In Controller

```php
use App\Services\ContractTemplateService;

public function export(Contract $contract)
{
    $service = app(ContractTemplateService::class);

    // Generate DOCX
    $path = $service->generateContract($contract, 'docx');

    // Download
    return $service->downloadContract($path);
}
```

### In Blade View

```blade
{{-- Add to contracts/show.blade.php --}}

<div class="btn-group">
    <a href="{{ route('admin.contracts.download-docx', $contract) }}"
       class="btn btn-primary">
        <i class="fas fa-file-word"></i> Download Word
    </a>

    @if(config('project.contract.enable_pdf'))
    <a href="{{ route('admin.contracts.download-pdf', $contract) }}"
       class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>
    @endif

    <a href="{{ route('admin.contracts.preview', $contract) }}"
       class="btn btn-info" target="_blank">
        <i class="fas fa-eye"></i> Preview
    </a>
</div>
```

### Programmatic Generation

```php
// Generate contract on creation
public function store(Request $request)
{
    $contract = Contract::create($validated);

    // Auto-generate DOCX
    $templateService = app(ContractTemplateService::class);
    $docxPath = $templateService->generateContract($contract);

    // Save path to contract
    $contract->update(['file_path' => $docxPath]);

    return redirect()->route('admin.contracts.show', $contract);
}
```

---

## PDF Generation (Optional)

### Requirements

Install LibreOffice:

**macOS:**
```bash
brew install --cask libreoffice
```

**Ubuntu:**
```bash
sudo apt-get install libreoffice
```

### Enable in Config

```php
// config/project.php
'contract' => [
    'enable_pdf' => true,
],
```

### Usage

```php
// Generate PDF
$pdfPath = $service->generateContract($contract, 'pdf');

// Or generate both
$paths = $service->generateContract($contract, 'both');
// Returns: ['docx' => '...', 'pdf' => '...']
```

---

## Arabic/Bilingual Support

### Set Arabic Font

```php
'fonts' => [
    'arabic' => 'Traditional Arabic',  // or 'Arial Unicode MS'
],
```

### Create Bilingual Template

```
Contract Number | رقم العقد
${contract_number}

Project Name | اسم المشروع
${project_name}

Client | العميل
${client_name}
```

### RTL Text Direction

In Microsoft Word:
1. Select Arabic text
2. Layout → Text Direction → Right-to-Left

---

## Troubleshooting

### Issue: Variables Not Replaced

**Cause:** Wrong placeholder format
**Solution:** Use `${variable}` not `{variable}` or `$variable`

### Issue: Template Not Found

**Cause:** Template doesn't exist
**Solution:** Let system create it automatically on first download, or create manually

### Issue: Logo Not Showing

**Cause:** Logo file missing
**Solution:** Upload logo to `storage/app/templates/logo.png`

### Issue: PDF Generation Fails

**Cause:** LibreOffice not installed
**Solution:** Install LibreOffice or disable PDF:
```php
'enable_pdf' => false,
```

### Issue: Arabic Shows Boxes

**Cause:** Missing Arabic fonts
**Solution:** Install Arabic fonts on server:
```bash
sudo apt-get install fonts-arabeyes
```

---

## Performance Tips

1. **Template Caching** - Template is loaded once per request
2. **Cleanup Old Files** - Delete old contracts periodically
3. **Optimize Logo** - Use compressed PNG (under 50KB)
4. **Simple Formatting** - Complex Word formatting may slow generation

### Cleanup Command

```bash
# Delete contracts older than 30 days
find storage/app/contracts -name "*.docx" -mtime +30 -delete
```

### Add to Scheduler

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Clean old contracts weekly
    $schedule->exec('find storage/app/contracts -name "*.docx" -mtime +30 -delete')
             ->weekly();
}
```

---

## Testing

### Test Contract Generation

```bash
# Visit any contract
php artisan serve

# Go to: http://localhost:8000/admin/contracts/1/download-docx
```

### Test Template Variables

```php
// In tinker
php artisan tinker

$contract = \App\Models\Contract::first();
$service = app(\App\Services\ContractTemplateService::class);
$path = $service->generateContract($contract);
echo $path;  // Shows where file was saved
```

### Verify File Created

```bash
ls -lah storage/app/contracts/
```

---

## Next Steps

1. **Add Your Logo**
   - Save logo to `storage/app/templates/logo.png`

2. **Update Company Info**
   - Edit `.env` file with your details

3. **Customize Template**
   - Download auto-generated template
   - Edit in Microsoft Word
   - Add your branding
   - Upload back

4. **Add Download Buttons**
   - Update contract views with download links
   - See examples in Usage section

5. **Test Everything**
   - Download a contract
   - Verify all data is correct
   - Check formatting

---

## Support

- **Full Documentation:** [CONTRACT_TEMPLATE_GUIDE.md](CONTRACT_TEMPLATE_GUIDE.md)
- **Project Config:** [config/project.php](config/project.php)
- **Service Code:** [app/Services/ContractTemplateService.php](app/Services/ContractTemplateService.php)

---

## Package Information

- **PHPWord:** v1.4.0
- **License:** MIT
- **Docs:** https://phpoffice.github.io/PHPWord/

---

**Implemented:** 2025-10-30
**Status:** ✅ Production Ready
**Version:** 1.0.0
