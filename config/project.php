<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Project Creation Settings
    |--------------------------------------------------------------------------
    |
    | Configure how project creation behaves in your application.
    | These settings can be modified to suit your business needs.
    |
    */

    'creation' => [
        /*
         * Auto-generate contract when creating a project
         * Set to false to make contract generation optional
         */
        'auto_generate_contract' => false,

        /*
         * Default contract generation (when auto_generate_contract is false)
         * User can override this during project creation
         */
        'generate_contract_by_default' => true,

        /*
         * Require service package selection
         * Set to false to allow creating projects without packages
         */
        'require_package' => false,

        /*
         * Allow editing package services after selection
         * Set to true to let users remove/modify package services
         */
        'editable_package_services' => true,

        /*
         * Enable multi-step wizard for project creation
         * Set to false to use single-page form
         */
        'use_wizard' => true,

        /*
         * Wizard steps configuration
         */
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
            'details' => [
                'title' => 'Project Details',
                'icon' => 'fa-calendar',
                'fields' => ['budget', 'start_date', 'end_date', 'location', 'project_manager_id']
            ],
            'documents' => [
                'title' => 'Documents',
                'icon' => 'fa-file',
                'optional' => true,
                'fields' => ['documents']
            ],
        ],

        /*
         * Fields that are always required
         */
        'required_fields' => ['name', 'client_id', 'main_service_id', 'status'],

        /*
         * Fields shown in simplified/minimal creation mode
         */
        'minimal_fields' => ['name', 'client_id', 'main_service_id', 'status'],

        /*
         * Enable section-based service selection
         * Allows selecting all services in a stage/section at once
         */
        'enable_section_selection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Settings
    |--------------------------------------------------------------------------
    |
    | Configure document upload requirements for projects
    |
    */

    'documents' => [
        /*
         * Make documents required during project creation
         * Set to false to allow creating projects without documents
         */
        'required' => false,

        /*
         * Load document types dynamically from database
         * Set to false to use hardcoded list
         */
        'dynamic_document_types' => true,

        /*
         * Default required document slugs (if dynamic_document_types is false)
         */
        'default_required_documents' => [
            'project_mulkiya',
            'project_kuroki',
        ],

        /*
         * Maximum file size in kilobytes
         */
        'max_file_size' => 10240, // 10MB

        /*
         * Allowed file types
         */
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Selection Settings
    |--------------------------------------------------------------------------
    |
    | Configure how users select services for projects
    |
    */

    'services' => [
        /*
         * Service selection modes: 'package', 'individual', 'section', 'hybrid'
         * - package: Select via service packages only
         * - individual: Select services one by one
         * - section: Select entire sections/stages at once
         * - hybrid: Allow all methods (recommended)
         */
        'selection_mode' => 'hybrid',

        /*
         * Show service package suggestions based on selected services
         */
        'suggest_packages' => true,

        /*
         * Maximum custom services allowed per project
         * Set to null for unlimited
         */
        'max_custom_services' => null,

        /*
         * Group services by stage in selection UI
         */
        'group_by_stage' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Project Number Generation
    |--------------------------------------------------------------------------
    |
    | Configure how project numbers are generated
    |
    */

    'number_generation' => [
        /*
         * Format: {prefix}-{year}-{sequence}
         * Available placeholders: {prefix}, {year}, {month}, {sequence}
         */
        'format' => 'PRJ-{year}-{sequence}',

        /*
         * Prefix for project numbers
         */
        'prefix' => 'PRJ',

        /*
         * Sequence padding (number of digits)
         */
        'sequence_padding' => 4,

        /*
         * Reset sequence yearly
         */
        'reset_yearly' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Default values for new projects
    |
    */

    'defaults' => [
        'status' => 'planning',
        'currency' => 'OMR',
        'progress' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Customize validation rules for project creation
    |
    */

    'validation' => [
        /*
         * Require budget for specific statuses
         */
        'budget_required_for_statuses' => ['active', 'in_progress'],

        /*
         * Require dates for specific statuses
         */
        'dates_required_for_statuses' => ['active', 'in_progress'],

        /*
         * Minimum budget amount
         */
        'min_budget' => 0,

        /*
         * Maximum budget amount (null for unlimited)
         */
        'max_budget' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Template Settings
    |--------------------------------------------------------------------------
    |
    | Configure contract document generation and templates
    |
    */

    'contract' => [
        /*
         * Use DOCX template for print route instead of HTML
         * Set to false to keep using HTML print view
         */
        'use_docx_for_print' => true,

        /*
         * Output format: 'docx', 'pdf', 'both'
         */
        'output_format' => 'docx',

        /*
         * Path to DOCX template file (relative to storage/app)
         * Template should contain placeholders like ${project_name}, ${client_name}, etc.
         */
        'template_path' => 'templates/contract_template.docx',

        /*
         * Enable PDF generation (requires libreoffice or similar)
         */
        'enable_pdf' => false,

        /*
         * PDF conversion command (if enable_pdf is true)
         * Use {input} and {output} as placeholders
         */
        'pdf_command' => 'libreoffice --headless --convert-to pdf --outdir {output_dir} {input}',

        /*
         * Template variables and their formatting
         */
        'template_variables' => [
            'project_name' => 'name',
            'project_number' => 'project_number',
            'client_name' => 'client.name',
            'client_company' => 'client.company_name',
            'client_email' => 'client.email',
            'client_phone' => 'client.phone',
            'contract_number' => 'contract_number',
            'contract_date' => 'created_at|date:Y-m-d',
            'start_date' => 'start_date|date:Y-m-d',
            'end_date' => 'end_date|date:Y-m-d',
            'budget' => 'value|number',
            'currency' => 'currency',
            'status' => 'status',
            'description' => 'description',
            'terms' => 'terms',
        ],

        /*
         * Company information (for contract header/footer)
         */
        'company_info' => [
            'name' => env('COMPANY_NAME', 'AMTAR Engineering'),
            'address' => env('COMPANY_ADDRESS', 'Muscat, Oman'),
            'phone' => env('COMPANY_PHONE', '+968 XXXXXXXX'),
            'email' => env('COMPANY_EMAIL', 'info@amtar.om'),
            'website' => env('COMPANY_WEBSITE', 'www.amtar.om'),
            'logo_path' => 'templates/logo.png', // Relative to storage/app
        ],

        /*
         * Fonts for Arabic support
         */
        'fonts' => [
            'default' => 'Arial',
            'arabic' => 'Traditional Arabic',
            'header' => 'Calibri',
        ],
    ],
];
