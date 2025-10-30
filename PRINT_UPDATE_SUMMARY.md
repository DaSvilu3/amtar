# Contract Print Update - Now Uses DOCX Template

## What Changed

The **Print** button for contracts now generates and downloads a **professional DOCX document** instead of showing an HTML page.

---

## Before vs After

### Before ❌
- Click "Print" → Opens HTML page in browser
- User must manually print to PDF
- Basic formatting
- Hard to customize

### After ✅
- Click "Print" → Downloads professional DOCX file
- User gets editable Word document
- Professional formatting with branding
- Easy to customize template
- Fallback to HTML if DOCX fails

---

## How It Works

When a user clicks "Print" on a contract:

```
User clicks "Print" button
        ↓
Route: /admin/contracts/{contract}/print
        ↓
Controller checks config: use_docx_for_print
        ↓
If TRUE (default):
  → Generate DOCX template
  → Download file
        ↓
If FALSE or error:
  → Show HTML view (old behavior)
```

---

## Configuration

In [config/project.php](config/project.php):

```php
'contract' => [
    // Set to true to use DOCX for print (recommended)
    // Set to false to use old HTML print view
    'use_docx_for_print' => true,
],
```

### To Revert to HTML Print

If you want to keep the old HTML print behavior:

```php
'use_docx_for_print' => false,
```

Then clear config cache:
```bash
php artisan config:clear
```

---

## Updated Files

1. **[app/Http/Controllers/Admin/ContractController.php](app/Http/Controllers/Admin/ContractController.php)**
   - Modified `print()` method
   - Now uses `ContractTemplateService`
   - Automatic fallback to HTML on error

2. **[app/Models/Contract.php](app/Models/Contract.php)**
   - Added `projectServices()` relationship
   - Required for service table in template

3. **[config/project.php](config/project.php)**
   - Added `use_docx_for_print` setting
   - Controls print behavior

---

## Routes (Unchanged)

The print route remains the same:

```php
GET /admin/contracts/{contract}/print
```

But now it downloads DOCX instead of showing HTML!

### Additional Routes (Still Available)

```php
// Explicit DOCX download
GET /admin/contracts/{contract}/download-docx

// PDF download (if LibreOffice installed)
GET /admin/contracts/{contract}/download-pdf

// Preview inline
GET /admin/contracts/{contract}/preview
```

---

## Error Handling

### Automatic Fallback

If DOCX generation fails for any reason:
- System automatically falls back to HTML view
- Error is logged for debugging
- User sees warning message
- No disruption to workflow

### Error Log

Check logs at: `storage/logs/laravel.log`

Example error entry:
```
Contract DOCX generation failed, falling back to HTML
Contract ID: 123
Error: Template file not found
```

---

## User Experience

### What Users See

**Before:**
1. Click "Print" → HTML page opens
2. Browser print dialog
3. Save as PDF manually
4. Basic formatting

**Now:**
1. Click "Print" → DOCX downloads automatically
2. Open in Microsoft Word
3. Professional document with branding
4. Edit if needed
5. Save or export as PDF from Word

---

## Template Customization

Users can customize the contract template:

1. **Download Template**
   - Generate any contract (creates template automatically)
   - Template saved at: `storage/app/templates/contract_template.docx`

2. **Edit Template**
   - Download and open in Microsoft Word
   - Customize layout, fonts, colors
   - Add/modify company branding
   - Rearrange sections

3. **Upload Back**
   - Save edited template
   - Upload to `storage/app/templates/contract_template.docx`
   - All future contracts use new template!

---

## Testing

### Test the New Print Behavior

```bash
# Start server
php artisan serve

# Visit any contract
http://localhost:8000/admin/contracts/1

# Click "Print" button
# → Should download DOCX file automatically
```

### Test Fallback to HTML

```bash
# Temporarily rename template
mv storage/app/templates/contract_template.docx storage/app/templates/contract_template.docx.bak

# Click "Print"
# → Should show HTML view with warning message

# Restore template
mv storage/app/templates/contract_template.docx.bak storage/app/templates/contract_template.docx
```

---

## For Developers

### Programmatic Usage

```php
use App\Services\ContractTemplateService;

// In your controller
public function customPrint(Contract $contract)
{
    $service = app(ContractTemplateService::class);

    // Generate DOCX
    $path = $service->generateContract($contract, 'docx');

    // Download
    return $service->downloadContract($path, 'my_contract.docx');

    // Or return inline for preview
    return response()->file(storage_path('app/' . $path));
}
```

### Customizing Print Behavior

You can extend the print logic:

```php
public function print(Contract $contract, ContractTemplateService $templateService)
{
    // Custom logic before printing
    if ($contract->status === 'draft') {
        return redirect()->back()->with('error', 'Cannot print draft contracts');
    }

    // Check user preference
    $format = auth()->user()->preferred_contract_format ?? 'docx';

    if ($format === 'pdf') {
        $filePath = $templateService->generateContract($contract, 'pdf');
    } else {
        $filePath = $templateService->generateContract($contract, 'docx');
    }

    return $templateService->downloadContract($filePath);
}
```

---

## Blade View Updates

### Adding Print Button

If you're adding print buttons to views:

```blade
{{-- Single Print button (uses config setting) --}}
<a href="{{ route('admin.contracts.print', $contract) }}"
   class="btn btn-primary">
    <i class="fas fa-print"></i> Print Contract
</a>

{{-- Multiple download options --}}
<div class="btn-group">
    <a href="{{ route('admin.contracts.print', $contract) }}"
       class="btn btn-primary">
        <i class="fas fa-print"></i> Print
    </a>

    <a href="{{ route('admin.contracts.download-docx', $contract) }}"
       class="btn btn-success">
        <i class="fas fa-file-word"></i> DOCX
    </a>

    @if(config('project.contract.enable_pdf'))
    <a href="{{ route('admin.contracts.download-pdf', $contract) }}"
       class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @endif
</div>
```

---

## Troubleshooting

### Issue: Print Downloads Instead of Opening

**This is expected behavior!**
- DOCX files download automatically
- User opens in Microsoft Word
- More reliable than browser preview

### Issue: Print Still Shows HTML

**Causes:**
1. Config cache not cleared
2. `use_docx_for_print` set to false

**Solution:**
```bash
# Clear cache
php artisan config:clear

# Check config value
php artisan tinker
>>> config('project.contract.use_docx_for_print')
=> true  // Should be true for DOCX
```

### Issue: Error - Template Not Found

**Cause:** Template doesn't exist yet

**Solution:**
Template is auto-created on first use. Just click print again!

Or manually create:
```bash
# System will auto-generate when you first use it
# No action needed!
```

### Issue: Blank DOCX File

**Causes:**
1. Contract missing data (no client, project, etc.)
2. Relationships not loaded

**Solution:**
```php
// Ensure data is loaded
$contract->load(['client', 'project', 'projectServices']);
```

---

## Performance Impact

### Before (HTML)
- Fast (generates HTML)
- No file storage

### After (DOCX)
- Slightly slower (generates DOCX)
- Files stored in `storage/app/contracts/`
- Automatic cleanup recommended

### Cleanup Old Files

Add to your scheduled tasks:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Delete contracts older than 30 days
    $schedule->exec('find storage/app/contracts -name "*.docx" -mtime +30 -delete')
             ->daily();
}
```

Or manual cleanup:
```bash
find storage/app/contracts -name "*.docx" -mtime +30 -delete
```

---

## Migration Guide

### For End Users

**No action needed!**
- Print button works exactly the same
- Just downloads file instead of opening page

### For Administrators

1. **Add Company Logo** (Optional)
   ```bash
   # Upload to: storage/app/templates/logo.png
   ```

2. **Update Company Info** (Optional)
   ```bash
   # Edit .env
   COMPANY_NAME="Your Company"
   COMPANY_ADDRESS="Your Address"
   # etc.
   ```

3. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

4. **Test**
   - Visit any contract
   - Click "Print"
   - Verify DOCX downloads

### For Developers

1. **Update Views** (If customized)
   - Print button still works with same route
   - Consider adding explicit download buttons

2. **Update Tests**
   ```php
   // Old test
   $response = $this->get(route('admin.contracts.print', $contract));
   $response->assertViewIs('admin.contracts.print');

   // New test
   $response = $this->get(route('admin.contracts.print', $contract));
   $response->assertDownload(); // Downloads file
   ```

---

## Rollback Instructions

If you need to revert to old HTML behavior:

### Option 1: Configuration (Recommended)

```php
// config/project.php
'use_docx_for_print' => false,
```

```bash
php artisan config:clear
```

### Option 2: Controller Update

Edit [ContractController.php](app/Http/Controllers/Admin/ContractController.php):

```php
public function print(Contract $contract)
{
    $contract->load('client', 'project.projectManager', 'creator');
    return view('admin.contracts.print', compact('contract'));
}
```

---

## Benefits

✅ **Professional Output** - Word document instead of HTML
✅ **Editable** - Users can edit in Word if needed
✅ **Consistent Branding** - Company logo and info
✅ **Better Quality** - Proper document formatting
✅ **Easy to Share** - Standard DOCX format
✅ **No Breaking Changes** - Same route, new behavior
✅ **Automatic Fallback** - HTML backup if errors
✅ **Configurable** - Easy to switch back

---

## Summary

- ✅ Print now downloads DOCX instead of showing HTML
- ✅ Configurable via `use_docx_for_print` setting
- ✅ Automatic fallback to HTML on errors
- ✅ No breaking changes to routes or UI
- ✅ Professional templates with branding
- ✅ Easy rollback if needed

---

**Updated:** 2025-10-30
**Version:** 2.0.0
**Status:** ✅ Production Ready
