# Contract Template System - DOCX Generation Guide

## Overview

The contract printing system now generates **professional DOCX documents** using customizable templates. Contracts can be downloaded in Microsoft Word format with full support for:

- Custom branding and logos
- Dynamic data population
- Service listings with grouping
- Arabic/English bilingual support
- Professional formatting
- Optional PDF conversion

---

## Quick Start

### Printing/Downloading a Contract

1. Navigate to a contract in the admin panel
2. Click one of the options:
   - **Print** - Downloads DOCX automatically (NEW!)
   - **Download DOCX** - Explicit DOCX download
   - **Download PDF** - PDF format (if LibreOffice enabled)
   - **Preview** - View inline before downloading

### Routes Available

```php
// Print contract (downloads DOCX by default, configurable)
GET /admin/contracts/{contract}/print

// Explicit DOCX download
GET /admin/contracts/{contract}/download-docx

// Download contract as PDF (requires LibreOffice)
GET /admin/contracts/{contract}/download-pdf

// Preview contract inline
GET /admin/contracts/{contract}/preview
```

### Important Change

**The Print button now downloads DOCX instead of showing HTML!**

- **Before:** Print → HTML page → Manual print to PDF
- **Now:** Print → Professional DOCX downloads automatically

To revert to HTML print view:
```php
// config/project.php
'use_docx_for_print' => false,
```

---

## Configuration

All settings are in [config/project.php](config/project.php) under the `contract` section:

### Basic Settings

```php
'contract' => [
    // Output format: 'docx', 'pdf', or 'both'
    'output_format' => 'docx',

    // Path to template file
    'template_path' => 'templates/contract_template.docx',

    // Enable PDF generation (requires LibreOffice)
    'enable_pdf' => false,
],
```

### Company Information

Update your company details shown on contracts:

```php
'company_info' => [
    'name' => env('COMPANY_NAME', 'AMTAR Engineering'),
    'address' => env('COMPANY_ADDRESS', 'Muscat, Oman'),
    'phone' => env('COMPANY_PHONE', '+968 XXXXXXXX'),
    'email' => env('COMPANY_EMAIL', 'info@amtar.om'),
    'website' => env('COMPANY_WEBSITE', 'www.amtar.om'),
    'logo_path' => 'templates/logo.png',
],
```

### Template Variables

Map contract data to template placeholders:

```php
'template_variables' => [
    'project_name' => 'name',                    // Direct property
    'client_name' => 'client.name',              // Nested property
    'contract_date' => 'created_at|date:Y-m-d',  // With formatting
    'budget' => 'value|number',                  // Number formatting
],
```

---

## Creating a Custom Template

### Method 1: Use the Auto-Generated Template

The system creates a basic template automatically on first use at:
```
storage/app/templates/contract_template.docx
```

You can:
1. Download this template
2. Edit it in Microsoft Word
3. Upload it back to replace the default

### Method 2: Create Your Own Template

**Step 1: Create a Word Document**

Open Microsoft Word and create your contract layout with:
- Company header with logo
- Contract title and number
- Project and client information sections
- Services table
- Terms and conditions
- Signature blocks

**Step 2: Add Placeholders**

Use placeholders in this format: `${variable_name}`

Example:
```
Contract Number: ${contract_number}
Date: ${today_date_formatted}

PROJECT INFORMATION
Project Name: ${project_name}
Project Number: ${project_number}
Location: ${project_location}

CLIENT INFORMATION
Name: ${client_name}
Company: ${client_company}
Email: ${client_email}
Phone: ${client_phone}
```

**Step 3: Add Service Table (Optional)**

For a dynamic service list, create a table with these placeholders:

| Stage | Services | Count |
|-------|----------|-------|
| ${service_stage} | ${service_list} | ${service_count} |

The system will clone this row for each service stage.

**Step 4: Save and Upload**

1. Save the document as `.docx`
2. Upload to `storage/app/templates/contract_template.docx`
3. Test by downloading a contract

---

## Available Template Variables

### Contract Information
- `${contract_number}` - Contract number
- `${contract_status}` - Contract status
- `${contract_date}` - Contract creation date
- `${today_date}` - Current date (Y-m-d)
- `${today_date_formatted}` - Current date (d F Y)

### Project Information
- `${project_name}` - Project name
- `${project_number}` - Project number
- `${project_location}` - Project location
- `${project_status}` - Project status
- `${main_service}` - Main service name
- `${sub_service}` - Sub service name
- `${service_package}` - Service package name

### Client Information
- `${client_name}` - Client full name
- `${client_company}` - Company name
- `${client_email}` - Email address
- `${client_phone}` - Phone number

### Financial Information
- `${budget}` - Contract value (number)
- `${budget_formatted}` - Formatted with decimals
- `${budget_words}` - Amount in words (e.g., "Five Thousand Omani Rials")
- `${currency}` - Currency code (OMR)

### Dates
- `${start_date}` - Contract start date
- `${end_date}` - Contract end date

### Contract Content
- `${description}` - Contract description
- `${terms}` - Terms and conditions
- `${status}` - Contract status

### Company Information
- `${company_name}` - Your company name
- `${company_address}` - Your address
- `${company_phone}` - Your phone
- `${company_email}` - Your email
- `${company_website}` - Your website
- `${company_logo}` - Logo image (special placeholder)

### Services (Table Variables)
- `${service_stage}` - Service stage name
- `${service_list}` - Comma-separated services in stage
- `${service_count}` - Number of services in stage
- `${total_services}` - Total number of all services
- `${services_list}` - All services as plain list

---

## Adding Your Logo

**Step 1: Prepare Logo**

1. Use PNG or JPG format
2. Recommended size: 200x80 pixels
3. Save as `logo.png`

**Step 2: Upload Logo**

Upload to:
```
storage/app/templates/logo.png
```

**Step 3: Add to Template**

In your DOCX template:
1. Insert > Picture > Placeholder
2. Right-click > Format Picture
3. Alt Text: `${company_logo}`

Or simply use the placeholder text `${company_logo}` where you want the logo.

---

## PDF Generation (Optional)

### Requirements

To enable PDF generation, you need **LibreOffice** installed:

**On macOS:**
```bash
brew install --cask libreoffice
```

**On Ubuntu/Debian:**
```bash
sudo apt-get install libreoffice
```

**On CentOS/RHEL:**
```bash
sudo yum install libreoffice
```

### Enable PDF

In [config/project.php](config/project.php):

```php
'contract' => [
    'enable_pdf' => true,

    'pdf_command' => 'libreoffice --headless --convert-to pdf --outdir {output_dir} {input}',
],
```

### Custom PDF Command

If LibreOffice is not in PATH, specify full path:

```php
'pdf_command' => '/Applications/LibreOffice.app/Contents/MacOS/soffice --headless --convert-to pdf --outdir {output_dir} {input}',
```

Or use alternative converters:

```php
// Using Pandoc
'pdf_command' => 'pandoc {input} -o {output_dir}/output.pdf',

// Using unoconv
'pdf_command' => 'unoconv -f pdf -o {output_dir} {input}',
```

---

## Arabic Language Support

### Fonts

The template service includes Arabic font support:

```php
'fonts' => [
    'default' => 'Arial',
    'arabic' => 'Traditional Arabic',
    'header' => 'Calibri',
],
```

### Creating Bilingual Templates

**Method 1: Side-by-Side**

```
Contract Number | رقم العقد
${contract_number}

Project Name | اسم المشروع
${project_name}
```

**Method 2: Separate Sections**

```
ENGLISH SECTION
===============
Project Name: ${project_name}
Client Name: ${client_name}

القسم العربي
===============
اسم المشروع: ${project_name}
اسم العميل: ${client_name}
```

**Method 3: RTL Support**

Set paragraph direction to Right-to-Left in Word:
- Select text
- Layout > Text Direction > Right-to-Left

---

## Advanced Features

### Custom Formatting

You can apply formatting modifiers to variables:

```php
'template_variables' => [
    'created_at|date:d/m/Y' => 'created_at',      // Custom date format
    'value|number' => 'value',                     // Number with decimals
    'name|uppercase' => 'name',                    // UPPERCASE
    'description|lowercase' => 'description',      // lowercase
],
```

### Conditional Sections

While PHPWord doesn't support conditionals in templates directly, you can:

1. Create multiple templates for different scenarios
2. Switch templates based on conditions in code
3. Use empty values for optional fields

### Dynamic Tables

The service automatically handles service tables:

```php
// In template, create a table row with:
${service_stage} | ${service_list} | ${service_count}

// System will clone this row for each service stage
```

### Custom Variables

Add your own variables by extending the service:

```php
// In ContractTemplateService, add to setTemplateValues():
$templateProcessor->setValue('custom_field', $contract->custom_field);
```

---

## File Storage

Generated contracts are stored in:
```
storage/app/contracts/
```

Files are named:
```
contract_{CONTRACT_NUMBER}_{TIMESTAMP}.docx
```

Example:
```
contract_CNT-2025-0001_1730304000.docx
```

---

## Troubleshooting

### "Template not found" Error

**Solution:**
1. Check `storage/app/templates/contract_template.docx` exists
2. Or let the system generate a basic template automatically
3. Verify path in `config/project.php`

### Variables Not Replaced

**Causes:**
1. Incorrect placeholder format (use `${variable}` not `{variable}`)
2. Variable name typo
3. Data not loaded (check contract has client/project loaded)

**Solution:**
```php
// Ensure relationships are loaded
$contract->load('client', 'project', 'projectServices');
```

### Logo Not Showing

**Causes:**
1. Logo file doesn't exist at specified path
2. Wrong placeholder format in template

**Solution:**
1. Verify file exists: `storage/app/templates/logo.png`
2. Use image placeholder in Word, not text
3. Check config path is correct

### PDF Generation Fails

**Causes:**
1. LibreOffice not installed
2. LibreOffice not in PATH
3. Permission issues

**Solution:**
```bash
# Test LibreOffice command manually
libreoffice --headless --convert-to pdf --outdir /tmp /path/to/test.docx

# Check if file was created
ls /tmp/*.pdf

# Fix permissions
chmod +x /usr/bin/libreoffice
```

### Arabic Text Shows as Boxes

**Solution:**
1. Install Arabic fonts on server
2. Update font config:
   ```php
   'fonts' => [
       'arabic' => 'Arial Unicode MS', // or 'Tahoma'
   ],
   ```

### Large File Size

**Solution:**
1. Compress images in template
2. Remove unnecessary formatting
3. Use simpler fonts

---

## Usage in Code

### Generate Contract Programmatically

```php
use App\Services\ContractTemplateService;

$templateService = app(ContractTemplateService::class);

// Generate DOCX
$docxPath = $templateService->generateContract($contract, 'docx');

// Generate PDF
$pdfPath = $templateService->generateContract($contract, 'pdf');

// Generate both
$paths = $templateService->generateContract($contract, 'both');
// Returns: ['docx' => 'path/to/file.docx', 'pdf' => 'path/to/file.pdf']
```

### Download Contract

```php
use App\Services\ContractTemplateService;

$templateService = app(ContractTemplateService::class);
$filePath = $templateService->generateContract($contract);

return $templateService->downloadContract($filePath, 'my_contract.docx');
```

### In Blade Templates

```blade
{{-- Download buttons --}}
<a href="{{ route('admin.contracts.download-docx', $contract) }}"
   class="btn btn-primary">
    <i class="fas fa-file-word"></i> Download DOCX
</a>

<a href="{{ route('admin.contracts.download-pdf', $contract) }}"
   class="btn btn-danger">
    <i class="fas fa-file-pdf"></i> Download PDF
</a>

<a href="{{ route('admin.contracts.preview', $contract) }}"
   class="btn btn-info" target="_blank">
    <i class="fas fa-eye"></i> Preview
</a>
```

---

## Environment Variables

Add to your `.env` file:

```env
# Company Information
COMPANY_NAME="AMTAR Engineering"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 12345678"
COMPANY_EMAIL="info@amtar.om"
COMPANY_WEBSITE="www.amtar.om"
```

---

## Best Practices

### Template Design

1. **Keep it Simple** - Complex formatting may not convert well
2. **Test Early** - Generate a contract early in design process
3. **Use Tables** - For structured data like services
4. **Standard Fonts** - Stick to Arial, Calibri, Times New Roman
5. **Avoid Macros** - PHPWord doesn't support VBA

### Performance

1. **Cache Templates** - Don't regenerate unnecessarily
2. **Cleanup Old Files** - Delete generated contracts periodically
3. **Optimize Images** - Compress logos before adding to template

### Security

1. **Validate Input** - Always validate contract data
2. **Sanitize Filenames** - Avoid special characters in filenames
3. **Storage Permissions** - Ensure proper file permissions

### Maintenance

1. **Version Templates** - Keep backups of template changes
2. **Test After Updates** - Test contract generation after PHPWord updates
3. **Document Changes** - Note any customizations made

---

## Example Template Structure

```
┌─────────────────────────────────────────────┐
│  [Company Logo]                             │
│  ${company_name}                            │
│  ${company_address}                         │
│  Tel: ${company_phone} | ${company_email}   │
├─────────────────────────────────────────────┤
│                                             │
│           CONTRACT AGREEMENT                │
│                                             │
├─────────────────────────────────────────────┤
│  Contract No: ${contract_number}            │
│  Date: ${today_date_formatted}              │
├─────────────────────────────────────────────┤
│  PROJECT DETAILS                            │
│  ──────────────                             │
│  Name: ${project_name}                      │
│  Number: ${project_number}                  │
│  Location: ${project_location}              │
├─────────────────────────────────────────────┤
│  CLIENT DETAILS                             │
│  ──────────────                             │
│  Name: ${client_name}                       │
│  Company: ${client_company}                 │
│  Email: ${client_email}                     │
│  Phone: ${client_phone}                     │
├─────────────────────────────────────────────┤
│  SERVICES                                   │
│  ────────                                   │
│  ┌────────┬─────────────┬───────┐           │
│  │ Stage  │ Services    │ Count │           │
│  ├────────┼─────────────┼───────┤           │
│  │ ${service_stage}               │           │
│  │        │ ${service_list}       │           │
│  │        │             │ ${service_count} │  │
│  └────────┴─────────────┴───────┘           │
├─────────────────────────────────────────────┤
│  FINANCIAL TERMS                            │
│  ───────────────                            │
│  Total Value: ${budget_formatted} ${currency}│
│  In Words: ${budget_words}                  │
│  Start Date: ${start_date}                  │
│  End Date: ${end_date}                      │
├─────────────────────────────────────────────┤
│  TERMS & CONDITIONS                         │
│  ──────────────────                         │
│  ${terms}                                   │
├─────────────────────────────────────────────┤
│  SIGNATURES                                 │
│  ──────────                                 │
│  Company: _______________                   │
│  Client:  _______________                   │
└─────────────────────────────────────────────┘
```

---

## Migration from Old System

If you were using HTML/PDF printing before:

1. **Keep Existing Routes** - HTML print route still works
2. **Add Download Buttons** - Add DOCX/PDF download links
3. **Test Both** - Ensure both systems work during transition
4. **Deprecate Gradually** - Remove HTML printing when ready

---

## Support & Debugging

### Enable Debug Mode

```php
// In ContractTemplateService
protected function generateDocx(Contract $contract): string
{
    \Log::info('Generating contract', [
        'contract_id' => $contract->id,
        'contract_number' => $contract->number,
    ]);

    // ... rest of code
}
```

### Check Generated File

```bash
# View generated contracts
ls -lah storage/app/contracts/

# Check file content
file storage/app/contracts/contract_*.docx

# Check file size
du -h storage/app/contracts/contract_*.docx
```

### Clear Generated Files

```bash
# Remove old contracts (older than 30 days)
find storage/app/contracts -name "*.docx" -mtime +30 -delete

# Remove all generated contracts
rm storage/app/contracts/*.docx
```

---

## Changelog

**v1.0.0** - Initial release
- DOCX template generation
- Company branding support
- Dynamic service tables
- Arabic font support
- Optional PDF conversion
- Configurable templates

---

**Last Updated:** 2025-10-30
**PHP Version:** 8.1+
**Laravel Version:** 11.x
**PHPWord Version:** 1.4.0
