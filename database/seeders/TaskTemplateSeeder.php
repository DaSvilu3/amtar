<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\TaskTemplate;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TaskTemplateSeeder - Creates task templates for each service
 *
 * Task templates are used to auto-generate tasks when a project is created.
 * Each template is linked to a service and defines:
 * - Title and description
 * - Priority (low, medium, high)
 * - Estimated hours
 * - Whether it requires review
 * - Required skills (optional)
 */
class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding task templates...');

        // Clear existing templates
        TaskTemplate::query()->delete();

        // Create skills if they don't exist
        $skills = $this->ensureSkillsExist();

        // Get all services
        $services = Service::with('serviceStage')->get()->keyBy('slug');

        if ($services->isEmpty()) {
            $this->command->warn('No services found. Run ServiceSeeder first.');
            return;
        }

        // Define templates for each service category
        $templates = $this->getTemplateDefinitions();

        $totalCreated = 0;
        foreach ($templates as $serviceSlug => $templateData) {
            if (!isset($services[$serviceSlug])) {
                continue;
            }

            $service = $services[$serviceSlug];

            // Convert skill slugs to skill IDs for JSON storage
            $skillIds = [];
            if (!empty($templateData['skills']) && !empty($skills)) {
                foreach ($templateData['skills'] as $skillSlug) {
                    if (isset($skills[$skillSlug])) {
                        $skillIds[] = $skills[$skillSlug]->id;
                    }
                }
            }

            TaskTemplate::create([
                'service_id' => $service->id,
                'title' => $templateData['title'],
                'description' => $templateData['description'] ?? null,
                'priority' => $templateData['priority'] ?? 'medium',
                'estimated_hours' => $templateData['hours'] ?? 4,
                'requires_review' => $templateData['requires_review'] ?? true,
                'required_skills' => !empty($skillIds) ? $skillIds : null,
                'is_active' => true,
                'sort_order' => $templateData['sort_order'] ?? 1,
            ]);

            $totalCreated++;
        }

        $this->command->info("Created {$totalCreated} task templates.");
    }

    /**
     * Ensure required skills exist in the database
     * Skills are tailored for an interior design and engineering consultancy
     */
    private function ensureSkillsExist(): array
    {
        $skillDefinitions = [
            // Design Skills
            ['slug' => 'interior-design', 'name' => 'Interior Design', 'description' => 'Residential and commercial interior design expertise'],
            ['slug' => 'space-planning', 'name' => 'Space Planning', 'description' => 'Functional space layout and planning'],
            ['slug' => 'furniture-design', 'name' => 'Furniture Design', 'description' => 'Custom furniture design and selection'],
            ['slug' => 'color-material', 'name' => 'Color & Material Selection', 'description' => 'Color schemes and material palette expertise'],
            ['slug' => 'lighting-design', 'name' => 'Lighting Design', 'description' => 'Interior and exterior lighting design'],
            ['slug' => 'landscape', 'name' => 'Landscape Design', 'description' => 'Outdoor space and landscape architecture'],
            ['slug' => 'fit-out', 'name' => 'Fit-Out Design', 'description' => 'Commercial and residential fit-out expertise'],

            // Visualization Skills
            ['slug' => '3d-modeling', 'name' => '3D Modeling', 'description' => 'SketchUp, 3ds Max, Revit modeling'],
            ['slug' => 'rendering', 'name' => 'Rendering & Visualization', 'description' => 'Photorealistic rendering (V-Ray, Corona, Lumion)'],
            ['slug' => 'autocad', 'name' => 'AutoCAD Drafting', 'description' => 'Technical drawing and CAD documentation'],
            ['slug' => 'revit', 'name' => 'Revit BIM', 'description' => 'Building Information Modeling expertise'],
            ['slug' => 'presentation', 'name' => 'Presentation Design', 'description' => 'Client presentations and mood boards'],

            // Engineering Skills
            ['slug' => 'architecture', 'name' => 'Architecture', 'description' => 'Architectural design and planning'],
            ['slug' => 'structural', 'name' => 'Structural Engineering', 'description' => 'Structural design and analysis'],
            ['slug' => 'mep', 'name' => 'MEP Engineering', 'description' => 'Mechanical, Electrical, Plumbing systems'],
            ['slug' => 'hvac', 'name' => 'HVAC Design', 'description' => 'Heating, ventilation and air conditioning'],
            ['slug' => 'electrical', 'name' => 'Electrical Design', 'description' => 'Electrical systems and lighting layouts'],
            ['slug' => 'plumbing', 'name' => 'Plumbing Design', 'description' => 'Plumbing and sanitary systems'],

            // Project Management Skills
            ['slug' => 'project-management', 'name' => 'Project Management', 'description' => 'Project planning and coordination'],
            ['slug' => 'client-liaison', 'name' => 'Client Liaison', 'description' => 'Client communication and relationship management'],
            ['slug' => 'vendor-coordination', 'name' => 'Vendor Coordination', 'description' => 'Supplier and contractor coordination'],
            ['slug' => 'procurement', 'name' => 'Procurement', 'description' => 'Material sourcing and purchasing'],
            ['slug' => 'cost-estimation', 'name' => 'Cost Estimation', 'description' => 'BOQ preparation and cost analysis'],
            ['slug' => 'scheduling', 'name' => 'Project Scheduling', 'description' => 'Timeline planning and tracking'],

            // Site & Quality Skills
            ['slug' => 'site-supervision', 'name' => 'Site Supervision', 'description' => 'On-site monitoring and supervision'],
            ['slug' => 'quality-control', 'name' => 'Quality Control', 'description' => 'Quality assurance and inspection'],
            ['slug' => 'snagging', 'name' => 'Snagging & Defects', 'description' => 'Defect identification and resolution'],
            ['slug' => 'handover', 'name' => 'Project Handover', 'description' => 'Final handover and documentation'],

            // Documentation Skills
            ['slug' => 'technical-docs', 'name' => 'Technical Documentation', 'description' => 'Specifications and technical writing'],
            ['slug' => 'permit-coordination', 'name' => 'Permit Coordination', 'description' => 'Municipality and authority approvals'],
        ];

        $skills = [];
        foreach ($skillDefinitions as $skillData) {
            $skill = Skill::firstOrCreate(
                ['slug' => $skillData['slug']],
                [
                    'name' => $skillData['name'],
                    'description' => $skillData['description'] ?? null
                ]
            );
            $skills[$skillData['slug']] = $skill;
        }

        return $skills;
    }

    /**
     * Get template definitions for all services
     */
    private function getTemplateDefinitions(): array
    {
        return [
            // Pre-Design / Programming
            'site_data_collection' => [
                'title' => 'Collect Site Data',
                'description' => 'Visit site to collect survey data, photographs, and measurements',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['architecture', 'site-supervision'],
                'sort_order' => 1,
            ],
            'client_requirements_study' => [
                'title' => 'Study Client Requirements',
                'description' => 'Review and document client requirements, preferences, and constraints',
                'priority' => 'high',
                'hours' => 6,
                'requires_review' => true,
                'skills' => ['architecture', 'project-management'],
                'sort_order' => 2,
            ],
            'preliminary_budget_schedule' => [
                'title' => 'Prepare Preliminary Budget & Schedule',
                'description' => 'Develop initial project budget and timeline estimates',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['cost-estimation', 'project-management'],
                'sort_order' => 3,
            ],

            // Conceptual Design
            'concept_idea' => [
                'title' => 'Develop Concept Idea',
                'description' => 'Create initial design concept based on client requirements',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 4,
            ],
            'bubble_diagrams_zoning' => [
                'title' => 'Create Bubble Diagrams & Zoning',
                'description' => 'Develop spatial relationships and functional zoning diagrams',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 5,
            ],
            'design_narrative' => [
                'title' => 'Write Design Narrative',
                'description' => 'Document design philosophy, approach, and rationale',
                'priority' => 'medium',
                'hours' => 4,
                'requires_review' => true,
                'skills' => ['architecture', 'technical-docs'],
                'sort_order' => 6,
            ],
            'initial_structural_mep_studies' => [
                'title' => 'Perform Initial Structural & MEP Studies',
                'description' => 'Conduct preliminary structural and MEP feasibility analysis',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['structural', 'mep'],
                'sort_order' => 7,
            ],

            // Schematic Design
            'floor_plan_development' => [
                'title' => 'Develop Floor Plans',
                'description' => 'Create detailed floor plan layouts',
                'priority' => 'high',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 8,
            ],
            'circulation_functional_relationships' => [
                'title' => 'Design Circulation & Functional Relationships',
                'description' => 'Plan circulation paths and functional area relationships',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 9,
            ],
            'preliminary_3d_perspectives' => [
                'title' => 'Create Preliminary 3D Perspectives',
                'description' => 'Develop initial 3D visualizations of the design',
                'priority' => 'medium',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['3d-modeling', 'rendering'],
                'sort_order' => 10,
            ],
            'preliminary_elevations' => [
                'title' => 'Prepare Preliminary Elevations',
                'description' => 'Create initial building elevation drawings',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 11,
            ],
            'column_locations' => [
                'title' => 'Determine Column Locations',
                'description' => 'Coordinate and finalize structural column positions',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['structural'],
                'sort_order' => 12,
            ],
            'initial_mep_layouts' => [
                'title' => 'Create Initial MEP Layouts',
                'description' => 'Develop preliminary mechanical, electrical, and plumbing layouts',
                'priority' => 'medium',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['mep'],
                'sort_order' => 13,
            ],

            // Design Development
            'refined_drawings' => [
                'title' => 'Prepare Refined Drawings',
                'description' => 'Develop detailed and refined design drawings',
                'priority' => 'high',
                'hours' => 32,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 14,
            ],
            'service_space_allocation' => [
                'title' => 'Allocate Service Spaces',
                'description' => 'Plan and allocate spaces for building services',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['architecture', 'mep'],
                'sort_order' => 15,
            ],
            'architectural_elevations' => [
                'title' => 'Finalize Architectural Elevations',
                'description' => 'Complete detailed architectural elevation drawings',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['architecture'],
                'sort_order' => 16,
            ],

            // 3D Visualization
            'realistic_3d_renderings' => [
                'title' => 'Create Realistic 3D Renderings',
                'description' => 'Produce high-quality photorealistic renderings',
                'priority' => 'medium',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['3d-modeling', 'rendering'],
                'sort_order' => 17,
            ],
            'day_night_views' => [
                'title' => 'Create Day & Night Views',
                'description' => 'Render daytime and nighttime visualization views',
                'priority' => 'low',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['3d-modeling', 'rendering'],
                'sort_order' => 18,
            ],
            'walkthrough_animation' => [
                'title' => 'Produce Walkthrough Animation',
                'description' => 'Create animated walkthrough of the design',
                'priority' => 'low',
                'hours' => 40,
                'requires_review' => true,
                'skills' => ['3d-modeling', 'rendering'],
                'sort_order' => 19,
            ],

            // Construction Documents
            'detailed_working_drawings' => [
                'title' => 'Prepare Detailed Working Drawings',
                'description' => 'Develop comprehensive construction drawings',
                'priority' => 'high',
                'hours' => 80,
                'requires_review' => true,
                'skills' => ['architecture', 'technical-docs'],
                'sort_order' => 20,
            ],
            'bill_of_quantities' => [
                'title' => 'Prepare Bill of Quantities',
                'description' => 'Create detailed BOQ for tendering',
                'priority' => 'high',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['cost-estimation'],
                'sort_order' => 21,
            ],
            'technical_specifications' => [
                'title' => 'Write Technical Specifications',
                'description' => 'Document technical specifications for all works',
                'priority' => 'high',
                'hours' => 32,
                'requires_review' => true,
                'skills' => ['technical-docs'],
                'sort_order' => 22,
            ],

            // Tendering & Bidding
            'tender_documents' => [
                'title' => 'Prepare Tender Documents',
                'description' => 'Compile complete tender package for bidding',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['procurement', 'technical-docs'],
                'sort_order' => 23,
            ],
            'bid_evaluation' => [
                'title' => 'Evaluate Bids',
                'description' => 'Review and evaluate contractor bids',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['procurement', 'cost-estimation'],
                'sort_order' => 24,
            ],
            'contract_award' => [
                'title' => 'Process Contract Award',
                'description' => 'Finalize contractor selection and contract award',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['procurement', 'project-management'],
                'sort_order' => 25,
            ],

            // Contract Administration
            'contract_administration' => [
                'title' => 'Administer Contract',
                'description' => 'Ongoing contract administration and management',
                'priority' => 'high',
                'hours' => 40,
                'requires_review' => false,
                'skills' => ['project-management'],
                'sort_order' => 26,
            ],
            'site_supervision' => [
                'title' => 'Conduct Site Supervision',
                'description' => 'Supervise construction activities on site',
                'priority' => 'high',
                'hours' => 80,
                'requires_review' => false,
                'skills' => ['site-supervision'],
                'sort_order' => 27,
            ],
            'progress_reports' => [
                'title' => 'Prepare Progress Reports',
                'description' => 'Document and report construction progress',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['technical-docs', 'site-supervision'],
                'sort_order' => 28,
            ],

            // Quality & Handover
            'snagging' => [
                'title' => 'Conduct Snagging',
                'description' => 'Identify and document construction defects',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['quality-control', 'site-supervision'],
                'sort_order' => 29,
            ],
            'as_built_drawings' => [
                'title' => 'Prepare As-Built Drawings',
                'description' => 'Create drawings reflecting actual constructed conditions',
                'priority' => 'high',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['architecture', 'technical-docs'],
                'sort_order' => 30,
            ],
            'vendor_warranties' => [
                'title' => 'Compile Vendor Warranties',
                'description' => 'Collect and organize all vendor warranty documents',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['technical-docs'],
                'sort_order' => 31,
            ],

            // Interior Design Services
            'client_interview' => [
                'title' => 'Conduct Client Interview',
                'description' => 'Meet with client to understand design preferences and requirements',
                'priority' => 'high',
                'hours' => 4,
                'requires_review' => false,
                'skills' => ['interior-design', 'client-liaison'],
                'sort_order' => 32,
            ],
            'mood_boards' => [
                'title' => 'Create Mood Boards',
                'description' => 'Develop visual mood boards showing design direction',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['interior-design', 'presentation'],
                'sort_order' => 33,
            ],
            'color_schemes' => [
                'title' => 'Develop Color Schemes',
                'description' => 'Create color palette options for the project',
                'priority' => 'medium',
                'hours' => 4,
                'requires_review' => true,
                'skills' => ['interior-design', 'color-material'],
                'sort_order' => 34,
            ],
            'material_palettes' => [
                'title' => 'Select Material Palettes',
                'description' => 'Choose materials, finishes, and textures',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['interior-design', 'color-material'],
                'sort_order' => 35,
            ],
            'space_planning' => [
                'title' => 'Develop Space Planning',
                'description' => 'Create optimal space planning layouts',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['interior-design', 'space-planning'],
                'sort_order' => 36,
            ],
            'furniture_layout' => [
                'title' => 'Design Furniture Layout',
                'description' => 'Plan furniture placement and selection',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['interior-design', 'furniture-design'],
                'sort_order' => 37,
            ],
            'lighting_design' => [
                'title' => 'Design Lighting Scheme',
                'description' => 'Develop lighting design and fixture selection',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['lighting-design', 'electrical'],
                'sort_order' => 38,
            ],
            '3d_renderings' => [
                'title' => 'Create Interior 3D Renderings',
                'description' => 'Produce photorealistic interior visualizations',
                'priority' => 'medium',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['3d-modeling', 'rendering'],
                'sort_order' => 39,
            ],

            // Landscape Design Services
            'site_visit' => [
                'title' => 'Conduct Site Visit',
                'description' => 'Visit and document existing site conditions',
                'priority' => 'high',
                'hours' => 4,
                'requires_review' => false,
                'skills' => ['landscape'],
                'sort_order' => 40,
            ],
            'climate_analysis' => [
                'title' => 'Perform Climate Analysis',
                'description' => 'Analyze local climate conditions for landscape design',
                'priority' => 'medium',
                'hours' => 6,
                'requires_review' => true,
                'skills' => ['landscape'],
                'sort_order' => 41,
            ],
            'master_plan_concept' => [
                'title' => 'Develop Master Plan Concept',
                'description' => 'Create overall landscape master plan concept',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['landscape'],
                'sort_order' => 42,
            ],
            'plant_palette' => [
                'title' => 'Select Plant Palette',
                'description' => 'Choose appropriate plant species for the design',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['landscape'],
                'sort_order' => 43,
            ],
            'planting_plan' => [
                'title' => 'Prepare Planting Plan',
                'description' => 'Create detailed planting layout and specifications',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['landscape'],
                'sort_order' => 44,
            ],
            'irrigation_design' => [
                'title' => 'Design Irrigation System',
                'description' => 'Develop irrigation layout and specifications',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['landscape', 'mep'],
                'sort_order' => 45,
            ],
            'hardscape_layout' => [
                'title' => 'Design Hardscape Layout',
                'description' => 'Plan paving, walls, and other hardscape elements',
                'priority' => 'medium',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['landscape'],
                'sort_order' => 46,
            ],

            // Fit-Out Design Services
            'space_assessment' => [
                'title' => 'Assess Existing Space',
                'description' => 'Evaluate current space conditions and constraints',
                'priority' => 'high',
                'hours' => 6,
                'requires_review' => true,
                'skills' => ['fit-out', 'space-planning'],
                'sort_order' => 47,
            ],
            'functional_requirements' => [
                'title' => 'Document Functional Requirements',
                'description' => 'Capture all functional and operational requirements',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['fit-out', 'client-liaison'],
                'sort_order' => 48,
            ],
            'space_layout' => [
                'title' => 'Design Space Layout',
                'description' => 'Create optimal space layout for fit-out',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['fit-out', 'space-planning', 'autocad'],
                'sort_order' => 49,
            ],
            'finishes_selection' => [
                'title' => 'Select Finishes',
                'description' => 'Choose materials and finishes for fit-out',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['fit-out', 'color-material'],
                'sort_order' => 50,
            ],
            'mep_coordination' => [
                'title' => 'Coordinate MEP Systems',
                'description' => 'Coordinate fit-out with MEP requirements',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['mep', 'hvac', 'electrical'],
                'sort_order' => 51,
            ],
            'partition_details' => [
                'title' => 'Prepare Partition Details',
                'description' => 'Design partition wall details and specifications',
                'priority' => 'medium',
                'hours' => 12,
                'requires_review' => true,
                'skills' => ['fit-out', 'autocad', 'technical-docs'],
                'sort_order' => 52,
            ],
            'ceiling_details' => [
                'title' => 'Design Ceiling Details',
                'description' => 'Develop ceiling design and specifications',
                'priority' => 'medium',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['fit-out', 'autocad'],
                'sort_order' => 53,
            ],

            // Engineering Supervision
            'review_construction_drawings' => [
                'title' => 'Review Construction Drawings',
                'description' => 'Review and approve construction drawings before work begins',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['architecture', 'revit', 'autocad'],
                'sort_order' => 54,
            ],
            'contractor_coordination' => [
                'title' => 'Coordinate with Contractors',
                'description' => 'Manage contractor communications and coordination',
                'priority' => 'high',
                'hours' => 24,
                'requires_review' => false,
                'skills' => ['vendor-coordination', 'site-supervision'],
                'sort_order' => 55,
            ],
            'periodic_site_visits' => [
                'title' => 'Conduct Periodic Site Visits',
                'description' => 'Regular site inspections to monitor progress',
                'priority' => 'high',
                'hours' => 40,
                'requires_review' => false,
                'skills' => ['site-supervision', 'quality-control'],
                'sort_order' => 56,
            ],
            'workmanship_inspection' => [
                'title' => 'Inspect Workmanship Quality',
                'description' => 'Quality inspection of completed works',
                'priority' => 'high',
                'hours' => 24,
                'requires_review' => true,
                'skills' => ['quality-control', 'snagging'],
                'sort_order' => 57,
            ],
            'compliance_verification' => [
                'title' => 'Verify Compliance',
                'description' => 'Verify work complies with drawings and specifications',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['quality-control', 'technical-docs'],
                'sort_order' => 58,
            ],
            'payment_certification' => [
                'title' => 'Certify Payments',
                'description' => 'Review and certify contractor payment applications',
                'priority' => 'high',
                'hours' => 8,
                'requires_review' => true,
                'skills' => ['cost-estimation', 'scheduling'],
                'sort_order' => 59,
            ],
            'final_inspection' => [
                'title' => 'Conduct Final Inspection',
                'description' => 'Comprehensive final inspection before handover',
                'priority' => 'high',
                'hours' => 16,
                'requires_review' => true,
                'skills' => ['snagging', 'handover'],
                'sort_order' => 60,
            ],
        ];
    }
}
