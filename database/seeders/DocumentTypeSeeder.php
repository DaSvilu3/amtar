<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            // Client Documents
            [
                'name' => 'Client ID / Civil ID',
                'slug' => 'client_civil_id',
                'entity_type' => 'client',
                'is_required' => true,
                'description' => 'Client identification or civil ID document',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Registration',
                'slug' => 'client_commercial_registration',
                'entity_type' => 'client',
                'is_required' => true,
                'description' => 'Commercial registration certificate',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Tax Registration Certificate',
                'slug' => 'client_tax_certificate',
                'entity_type' => 'client',
                'is_required' => false,
                'description' => 'Tax registration certificate',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Authorization Letter',
                'slug' => 'client_authorization_letter',
                'entity_type' => 'client',
                'is_required' => false,
                'description' => 'Authorization letter from the client',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],

            // Project Documents
            [
                'name' => 'Project Mulkiya (Property Title Deed)',
                'slug' => 'project_mulkiya',
                'entity_type' => 'project',
                'is_required' => true,
                'description' => 'Property title deed for the project',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Project Kuroki (Project Sketch/Plan)',
                'slug' => 'project_kuroki',
                'entity_type' => 'project',
                'is_required' => true,
                'description' => 'Project sketch or plan document',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Location Map',
                'slug' => 'project_location_map',
                'entity_type' => 'project',
                'is_required' => false,
                'description' => 'Location map of the project',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'NOC (No Objection Certificate)',
                'slug' => 'project_noc',
                'entity_type' => 'project',
                'is_required' => false,
                'description' => 'No objection certificate for the project',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Municipality Approval',
                'slug' => 'project_municipality_approval',
                'entity_type' => 'project',
                'is_required' => false,
                'description' => 'Municipality approval document',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],
            [
                'name' => 'Building Permit',
                'slug' => 'project_building_permit',
                'entity_type' => 'project',
                'is_required' => false,
                'description' => 'Building permit for the project',
                'file_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'is_active' => true,
            ],

            // Contract Documents
            [
                'name' => 'Signed Contract',
                'slug' => 'contract_signed_document',
                'entity_type' => 'contract',
                'is_required' => true,
                'description' => 'The signed contract document',
                'file_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'is_active' => true,
            ],
            [
                'name' => 'Addendum Documents',
                'slug' => 'contract_addendum',
                'entity_type' => 'contract',
                'is_required' => false,
                'description' => 'Contract addendum or amendment documents',
                'file_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'is_active' => true,
            ],
            [
                'name' => 'Payment Schedule',
                'slug' => 'contract_payment_schedule',
                'entity_type' => 'contract',
                'is_required' => false,
                'description' => 'Payment schedule for the contract',
                'file_types' => ['application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'is_active' => true,
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::updateOrCreate(
                ['slug' => $documentType['slug']],
                $documentType
            );
        }
    }
}
