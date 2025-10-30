# Simplified Project Creation Guide

## Overview

The project creation system has been completely redesigned to be **simpler**, **more flexible**, and **highly configurable**. This guide explains all the new features and how to customize them.

---

## What's New?

### 1. Configuration File
All project creation settings are now centralized in `config/project.php`. You can easily customize behavior without touching code.

### 2. Multi-Step Wizard Interface
Instead of one overwhelming form with 16+ fields, projects are now created through a user-friendly 4-step wizard:

- **Step 1: Basic Information** - Essential project details (name, client, status)
- **Step 2: Services** - Flexible service selection with multiple methods
- **Step 3: Details** - Optional fields (budget, dates, manager, contract generation)
- **Step 4: Documents** - Optional document uploads

### 3. Optional Contract Generation
Contracts are no longer auto-generated. Users can choose whether to generate a contract during project creation.

### 4. Flexible Service Selection
Three powerful ways to select services:

#### A. Package-Based (Original Method)
- Select a pre-configured service package
- All package services are included
- **NEW:** Can now remove individual services from packages!

#### B. Section/Stage Selection (NEW!)
- Select entire service stages at once
- Click "Select All" to add all services in a section
- Much faster than individual selection

#### C. Individual Service Selection
- Pick specific services one by one
- Grouped by stage for easy navigation
- Search and filter available services

### 5. Editable Package Services
Package services are no longer locked! Users can:
- Remove unwanted services from a package
- Add additional custom services
- Mix package and custom services freely

---

## Configuration Options

All settings are in [config/project.php](config/project.php). Here are the key options:

### Quick Wins Settings

```php
'creation' => [
    // Make contract generation optional (default: false - user chooses)
    'auto_generate_contract' => false,

    // Default checkbox state for contract generation (when optional)
    'generate_contract_by_default' => true,

    // Force users to select a package (default: false - packages optional)
    'require_package' => false,

    // Allow editing services within a package (default: true)
    'editable_package_services' => true,

    // Use wizard interface instead of single page form (default: true)
    'use_wizard' => true,

    // Enable section-based service selection (default: true)
    'enable_section_selection' => true,
],
```

### Service Selection Modes

```php
'services' => [
    // Options: 'package', 'individual', 'section', 'hybrid'
    // 'hybrid' = all methods available (recommended)
    'selection_mode' => 'hybrid',

    // Show package suggestions based on selected services
    'suggest_packages' => true,

    // Maximum custom services (null = unlimited)
    'max_custom_services' => null,

    // Group services by stage in UI
    'group_by_stage' => true,
],
```

### Document Settings

```php
'documents' => [
    // Make documents required during creation (default: false)
    'required' => false,

    // Load document types dynamically from database (default: true)
    'dynamic_document_types' => true,

    // Maximum file size in KB (default: 10MB)
    'max_file_size' => 10240,

    // Allowed file types
    'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
],
```

### Wizard Steps Configuration

Customize which fields appear in each wizard step:

```php
'wizard_steps' => [
    'basic_info' => [
        'title' => 'Basic Information',
        'icon' => 'fa-info-circle',
        'fields' => ['name', 'project_number', 'client_id', 'description', 'status']
    ],
    'services' => [
        'title' => 'Services & Packages',
        'icon' => 'fa-cogs',
        'fields' => ['main_service_id', 'sub_service_id', 'service_package_id', 'services']
    ],
    // ... more steps
],
```

---

## How to Use the New System

### For End Users

#### Creating a Project (Wizard Mode)

**Step 1: Basic Information**
1. Enter project name (required)
2. Project number is auto-generated (can be edited)
3. Select client (required)
4. Choose project status (required)
5. Add optional description
6. Click "Next"

**Step 2: Services**
1. Select main service (required)
2. Select sub-service if available (optional)
3. Choose one of three methods:

   **Method A: Use a Package**
   - Select a package from dropdown
   - Review included services
   - Remove unwanted services (if enabled)

   **Method B: Select by Section** (NEW!)
   - Switch to "Select by Section" tab
   - Click "Select All" on desired sections
   - Or pick individual services within sections

   **Method C: Individual Selection**
   - Switch to "Select Individual Services" tab
   - Check specific services you need
   - Services are grouped by stage

4. Click "Next"

**Step 3: Details**
1. Enter budget (optional)
2. Set start/end dates (optional)
3. Assign project manager (optional)
4. **Check/uncheck "Generate Contract"** (if option is enabled)
5. Click "Next"

**Step 4: Documents**
1. Upload documents (all optional):
   - Project Mulkiya (Title Deed)
   - Project Kuroki (Sketch/Plan)
   - Location Map
   - NOC (No Objection Certificate)
2. Or skip and add documents later
3. Click "Create Project"

---

### For Administrators

#### Switching Between Wizard and Classic Mode

Edit `config/project.php`:

```php
// Enable wizard (recommended)
'use_wizard' => true,

// Use classic single-page form
'use_wizard' => false,
```

#### Making Contract Generation Mandatory

```php
// Always generate contracts (no user choice)
'auto_generate_contract' => true,

// Let users decide (checkbox shown)
'auto_generate_contract' => false,
```

#### Disabling Section Selection

```php
// Disable section selection feature
'enable_section_selection' => false,
```

#### Making Packages Required

```php
// Force users to select a package
'require_package' => true,
```

#### Locking Package Services (Old Behavior)

```php
// Don't allow removing services from packages
'editable_package_services' => false,
```

---

## API Endpoints

### New Endpoints Added

**Get Service Stages (for section selection)**
```
GET /admin/api/services/stages
```
Returns all service stages with their services, ordered by sort_order.

**Existing Endpoints**
```
GET /admin/api/services/sub-services/{mainServiceId}
GET /admin/api/services/packages?main_service_id={id}&sub_service_id={id}
GET /admin/api/services/package-services/{packageId}
GET /admin/api/services/all
```

---

## Technical Details

### Controller Changes

**ProjectController** now includes:

1. **Dynamic Validation** - Rules adapt based on config settings
2. **Optional Contract Generation** - `shouldGenerateContract()` method
3. **Section Selection Support** - Handles `selected_sections[]` parameter
4. **Editable Package Services** - Handles `removed_package_services[]` parameter
5. **Config-Driven View Selection** - Automatically uses wizard or classic view

### New Form Parameters

The wizard form now supports these additional parameters:

```php
// Standard parameters
'name', 'client_id', 'main_service_id', etc.

// NEW: Section selection
'selected_sections' => [1, 3, 5] // Array of service_stage_id

// NEW: Removed package services (if editable)
'removed_package_services' => [12, 45] // Array of service_id

// NEW: Contract generation choice
'generate_contract' => true/false

// Existing: Custom services
'custom_services' => [7, 23, 89] // Array of service_id
```

### Database Schema

No migration changes required! The new system works with existing tables:
- `projects`
- `project_services`
- `services`
- `service_stages`
- `service_packages`
- `contracts`

---

## Comparison: Old vs New

| Feature | Old System | New System |
|---------|-----------|------------|
| **Form Type** | Single overwhelming page | 4-step wizard |
| **Required Fields** | 16 fields shown at once | Only essentials per step |
| **Contract Creation** | Always auto-generated | Optional (user chooses) |
| **Package Selection** | Mandatory workflow | Truly optional |
| **Package Services** | Locked after selection | Can remove/edit services |
| **Service Selection** | Package or individual only | Package + Section + Individual |
| **Section Selection** | Not available | NEW! Select entire stages |
| **Documents** | Required upfront | Optional at creation |
| **Configuration** | Hardcoded in views | Centralized config file |
| **Flexibility** | Rigid workflow | Highly customizable |

---

## Performance Improvements

1. **Lazy Loading** - Services loaded only when needed via AJAX
2. **Grouped Rendering** - Services organized by stage reduce DOM complexity
3. **Progressive Enhancement** - Wizard steps load content on-demand
4. **Reduced Initial Payload** - Smaller initial page load

---

## Best Practices

### Recommended Settings for Different Use Cases

**Small Projects (Simple Workflow)**
```php
'use_wizard' => false, // Single page is fine
'require_package' => false,
'enable_section_selection' => true,
'auto_generate_contract' => false,
```

**Large Enterprise (Complex Projects)**
```php
'use_wizard' => true, // Wizard reduces cognitive load
'require_package' => false, // Allow flexible service selection
'enable_section_selection' => true, // Faster bulk selection
'editable_package_services' => true, // Full flexibility
'auto_generate_contract' => false, // User decides
```

**Strict Workflow (Compliance-Heavy)**
```php
'use_wizard' => true,
'require_package' => true, // Force package selection
'editable_package_services' => false, // Lock package services
'auto_generate_contract' => true, // Always generate contracts
'documents' => ['required' => true], // Require documents
```

---

## Troubleshooting

### Wizard not showing
- Check `config/project.php` - ensure `'use_wizard' => true`
- Clear config cache: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`

### Section selection not working
- Ensure `'enable_section_selection' => true` in config
- Check route exists: `php artisan route:list | grep stages`
- Verify ServiceStage model has `services` relationship

### Contract not generating
- Check `auto_generate_contract` and `generate_contract_by_default` settings
- Ensure checkbox is checked in Step 3 (if optional)
- Verify Contract model and migration exist

### Services not loading
- Check browser console for JavaScript errors
- Verify API routes are registered
- Test endpoints directly: `/admin/api/services/stages`

---

## Future Enhancements

Possible future improvements:
- Project templates/presets for common project types
- Service recommendations based on project type
- Bulk project creation
- Save draft projects (incomplete submissions)
- Project cloning with service inheritance

---

## Files Changed

### New Files
- `config/project.php` - Configuration file
- `resources/views/admin/projects/create-wizard.blade.php` - Wizard view
- `PROJECT_CREATION_GUIDE.md` - This documentation

### Modified Files
- `app/Http/Controllers/Admin/ProjectController.php`
  - Added `shouldGenerateContract()` method
  - Added `getValidationRules()` method
  - Added `getServiceStages()` API endpoint
  - Updated `store()` method
  - Updated `handleProjectServices()` method
  - Updated `create()` method to support wizard
- `routes/web.php`
  - Added `/admin/api/services/stages` route

### Unchanged Files
- All models remain unchanged
- All migrations remain unchanged
- Classic `create.blade.php` view still available

---

## Support

For questions or issues:
1. Check this documentation
2. Review `config/project.php` comments
3. Inspect browser console for JavaScript errors
4. Check Laravel logs in `storage/logs/laravel.log`

---

**Last Updated:** 2025-10-30
**Version:** 1.0.0
