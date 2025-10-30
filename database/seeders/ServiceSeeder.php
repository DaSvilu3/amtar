<?php

namespace Database\Seeders;

use App\Models\MainService;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceStage;
use App\Models\SubService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ServiceSeeder - Comprehensive Service Catalog Seeder
 *
 * This seeder populates the service catalog with all services, stages, and packages.
 *
 * Seeding Order (respects foreign key constraints):
 * 1. Clear existing data (in reverse dependency order)
 * 2. Seed ServiceStages - All unique stages from the service catalog
 * 3. Seed Services - All individual services linked to their stages
 * 4. Seed MainServices - Engineering, Interior Design, Landscape Design, Fit-Out Design
 * 5. Seed SubServices - Engineering Consultation, Engineering Supervision (under Engineering)
 * 6. Seed ServicePackages - 3 generic packages (Basic, Standard, Premium)
 * 7. Link Services to Packages - NOT USED (services selected independently during project creation)
 *
 * Data Structure:
 * - MainServices (4): Engineering, Interior Design, Landscape Design, Fit-Out Design
 * - SubServices (2): Engineering Consultation, Engineering Supervision (under Engineering)
 * - ServiceStages (20+): Pre-Design, Conceptual Design, Schematic Design, etc.
 * - Services (200+): Individual service items from all disciplines
 * - ServicePackages (3): Basic, Standard, Premium (generic packages not tied to specific services)
 *
 * New Architecture:
 * - Packages are now generic (Basic/Standard/Premium) and NOT linked to specific main/sub services
 * - Users select Main Service + Sub Service (if applicable) during project creation
 * - Users then select specific services from the available pool based on their main/sub service
 * - Package selection is independent and used for pricing/scope guidance
 */
class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Clear existing data (in correct order to handle foreign keys)
        $this->command->info('Clearing existing service data...');
        DB::table('package_service')->delete();
        DB::table('project_services')->delete();
        ServicePackage::query()->delete();
        Service::query()->delete();
        ServiceStage::query()->delete();
        SubService::query()->delete();
        MainService::query()->delete();

        // Step 2: Seed ServiceStages
        $this->command->info('Seeding service stages...');
        $stages = $this->seedServiceStages();

        // Step 3: Seed Services
        $this->command->info('Seeding services...');
        $services = $this->seedServices($stages);

        // Step 4: Seed MainServices
        $this->command->info('Seeding main services...');
        $mainServices = $this->seedMainServices();

        // Step 5: Seed SubServices
        $this->command->info('Seeding sub-services...');
        $subServices = $this->seedSubServices($mainServices);

        // Step 6: Seed ServicePackages
        $this->command->info('Seeding service packages...');
        $packages = $this->seedServicePackages($mainServices, $subServices);

        // Step 7: Link Services to Packages via pivot table
        $this->command->info('Linking services to packages...');
        $this->linkServicesToPackages($packages, $services, $stages);

        $this->command->info('Service seeding completed successfully!');
    }

    /**
     * Seed all unique service stages from the JSON structure
     */
    private function seedServiceStages(): array
    {
        $stageDefinitions = [
            // Engineering Consultation Stages
            ['id' => 'pre_design', 'name' => 'Pre-Design / Programming'],
            ['id' => 'conceptual_design', 'name' => 'Conceptual Design'],
            ['id' => 'schematic_design', 'name' => 'Schematic Design'],
            ['id' => 'design_development', 'name' => 'Design Development'],
            ['id' => '3d_visualization', 'name' => '3D Visualization'],
            ['id' => 'construction_documents', 'name' => 'Construction Documents'],
            ['id' => 'tendering_bidding', 'name' => 'Tendering & Bidding'],
            ['id' => 'contract_administration', 'name' => 'Contract Administration & Supervision'],
            ['id' => 'procurement_management', 'name' => 'Procurement Management'],
            ['id' => 'multidisciplinary_coordination', 'name' => 'Multidisciplinary Coordination'],
            ['id' => 'quality_handover', 'name' => 'Quality & Handover'],

            // Engineering Supervision Stages
            ['id' => 'pre_construction', 'name' => 'Pre-Construction'],
            ['id' => 'construction_supervision', 'name' => 'Construction Supervision'],
            ['id' => 'quality_control', 'name' => 'Quality Control'],
            ['id' => 'cost_schedule_control', 'name' => 'Cost & Schedule Control'],
            ['id' => 'project_closeout', 'name' => 'Project Closeout'],

            // Interior Design Stages
            ['id' => 'briefing', 'name' => 'Project Briefing'],
            ['id' => 'concept', 'name' => 'Concept Development'],
            ['id' => 'visualization', 'name' => 'Visualization'],
            ['id' => 'documentation', 'name' => 'Documentation'],
            ['id' => 'procurement', 'name' => 'Procurement'],
            ['id' => 'implementation', 'name' => 'Implementation & Supervision'],

            // Landscape Design Stages
            ['id' => 'site_analysis', 'name' => 'Site Analysis'],
            ['id' => 'tendering', 'name' => 'Tendering'],

            // Fit-Out Design Stages
            ['id' => 'design', 'name' => 'Design'],
            ['id' => 'technical_design', 'name' => 'Technical Design'],
            ['id' => 'closeout', 'name' => 'Project Closeout'],
        ];

        $stages = [];
        foreach ($stageDefinitions as $index => $stageData) {
            $stage = ServiceStage::create([
                'slug' => $stageData['id'],
                'name' => $stageData['name'],
                'description' => null,
                'sort_order' => $index + 1,
            ]);
            $stages[$stageData['id']] = $stage;
        }

        return $stages;
    }

    /**
     * Seed all services from the service catalog
     */
    private function seedServices(array $stages): array
    {
        $serviceDefinitions = [
            // Engineering Consultation Services
            ['id' => 'site_data_collection', 'name' => 'Site Data Collection', 'category' => 'pre_design'],
            ['id' => 'client_requirements_study', 'name' => 'Client Requirements Study', 'category' => 'pre_design'],
            ['id' => 'preliminary_budget_schedule', 'name' => 'Preliminary Budget & Schedule', 'category' => 'pre_design'],
            ['id' => 'concept_idea', 'name' => 'Concept Idea', 'category' => 'conceptual_design'],
            ['id' => 'bubble_diagrams_zoning', 'name' => 'Bubble Diagrams & Zoning', 'category' => 'conceptual_design'],
            ['id' => 'design_narrative', 'name' => 'Design Narrative', 'category' => 'conceptual_design'],
            ['id' => 'initial_structural_mep_studies', 'name' => 'Initial Structural & MEP Studies', 'category' => 'conceptual_design'],
            ['id' => 'floor_plan_development', 'name' => 'Floor Plan Development', 'category' => 'schematic_design'],
            ['id' => 'circulation_functional_relationships', 'name' => 'Circulation & Functional Relationships', 'category' => 'schematic_design'],
            ['id' => 'preliminary_3d_perspectives', 'name' => 'Preliminary 3D Perspectives', 'category' => 'schematic_design'],
            ['id' => 'preliminary_elevations', 'name' => 'Preliminary Elevations', 'category' => 'schematic_design'],
            ['id' => 'column_locations', 'name' => 'Column Locations', 'category' => 'schematic_design'],
            ['id' => 'initial_mep_layouts', 'name' => 'Initial MEP Layouts', 'category' => 'schematic_design'],
            ['id' => 'refined_drawings', 'name' => 'Refined Drawings', 'category' => 'design_development'],
            ['id' => 'service_space_allocation', 'name' => 'Service & Space Allocation', 'category' => 'design_development'],
            ['id' => 'architectural_elevations', 'name' => 'Architectural Elevations', 'category' => 'design_development'],
            ['id' => 'architectural_structural_electrical_mechanical_development', 'name' => 'Architectural + Structural + Electrical + Mechanical Development', 'category' => 'design_development'],
            ['id' => 'realistic_3d_renderings', 'name' => 'Realistic 3D Renderings', 'category' => '3d_visualization'],
            ['id' => 'day_night_views', 'name' => 'Day & Night Views', 'category' => '3d_visualization'],
            ['id' => 'walkthrough_animation', 'name' => 'Walkthrough Animation', 'category' => '3d_visualization'],
            ['id' => '3d_visualization_optional', 'name' => '3D Visualization (Optional)', 'category' => '3d_visualization'],
            ['id' => 'detailed_working_drawings', 'name' => 'Detailed Working Drawings', 'category' => 'construction_documents'],
            ['id' => 'bill_of_quantities', 'name' => 'Bill of Quantities (BOQ)', 'category' => 'construction_documents'],
            ['id' => 'technical_specifications', 'name' => 'Technical Specifications', 'category' => 'construction_documents'],
            ['id' => 'tender_documents', 'name' => 'Tender Documents', 'category' => 'tendering_bidding'],
            ['id' => 'bid_evaluation', 'name' => 'Bid Evaluation', 'category' => 'tendering_bidding'],
            ['id' => 'contract_award', 'name' => 'Contract Award', 'category' => 'tendering_bidding'],
            ['id' => 'contract_administration', 'name' => 'Contract Administration', 'category' => 'contract_administration'],
            ['id' => 'site_supervision', 'name' => 'Site Supervision', 'category' => 'contract_administration'],
            ['id' => 'progress_reports', 'name' => 'Progress Reports', 'category' => 'contract_administration'],
            ['id' => 'material_delivery_schedules', 'name' => 'Material Delivery Schedules', 'category' => 'procurement_management'],
            ['id' => 'price_comparison', 'name' => 'Price Comparison', 'category' => 'procurement_management'],
            ['id' => 'material_approval', 'name' => 'Material Approval', 'category' => 'procurement_management'],
            ['id' => 'architectural_structural_mep_coordination', 'name' => 'Architectural / Structural / MEP Coordination', 'category' => 'multidisciplinary_coordination'],
            ['id' => 'architectural_structural_mep_finishes_coordination', 'name' => 'Architectural / Structural / MEP / Finishes Coordination', 'category' => 'multidisciplinary_coordination'],
            ['id' => 'delivery_models_visuals', 'name' => 'Delivery of Models & Visuals', 'category' => 'quality_handover'],
            ['id' => 'snagging', 'name' => 'Snagging', 'category' => 'quality_handover'],
            ['id' => 'as_built_drawings', 'name' => 'As-Built Drawings', 'category' => 'quality_handover'],
            ['id' => 'vendor_warranties', 'name' => 'Vendor Warranties', 'category' => 'quality_handover'],

            // Engineering Supervision Services
            ['id' => 'review_construction_drawings', 'name' => 'Review Construction Drawings', 'category' => 'pre_construction'],
            ['id' => 'contractor_coordination', 'name' => 'Contractor Coordination', 'category' => 'pre_construction'],
            ['id' => 'supervision_schedule', 'name' => 'Supervision Schedule', 'category' => 'pre_construction'],
            ['id' => 'baseline_schedule_review', 'name' => 'Baseline Schedule Review', 'category' => 'pre_construction'],
            ['id' => 'pre_construction_meeting', 'name' => 'Pre-Construction Meeting', 'category' => 'pre_construction'],
            ['id' => 'periodic_site_visits', 'name' => 'Periodic Site Visits', 'category' => 'construction_supervision'],
            ['id' => 'regular_site_visits', 'name' => 'Regular Site Visits', 'category' => 'construction_supervision'],
            ['id' => 'fulltime_site_presence', 'name' => 'Full-Time Site Presence', 'category' => 'construction_supervision'],
            ['id' => 'progress_monitoring', 'name' => 'Progress Monitoring', 'category' => 'construction_supervision'],
            ['id' => 'daily_progress_monitoring', 'name' => 'Daily Progress Monitoring', 'category' => 'construction_supervision'],
            ['id' => 'photo_documentation', 'name' => 'Photo Documentation', 'category' => 'construction_supervision'],
            ['id' => 'rfi_responses', 'name' => 'RFI Responses', 'category' => 'construction_supervision'],
            ['id' => 'weekly_progress_reports', 'name' => 'Weekly Progress Reports', 'category' => 'construction_supervision'],
            ['id' => 'change_order_management', 'name' => 'Change Order Management', 'category' => 'construction_supervision'],
            ['id' => 'meeting_coordination', 'name' => 'Meeting Coordination', 'category' => 'construction_supervision'],
            ['id' => 'workmanship_inspection', 'name' => 'Workmanship Inspection', 'category' => 'quality_control'],
            ['id' => 'comprehensive_workmanship_inspection', 'name' => 'Comprehensive Workmanship Inspection', 'category' => 'quality_control'],
            ['id' => 'compliance_verification', 'name' => 'Compliance Verification', 'category' => 'quality_control'],
            ['id' => 'material_testing_coordination', 'name' => 'Material Testing Coordination', 'category' => 'quality_control'],
            ['id' => 'deficiency_tracking', 'name' => 'Deficiency Tracking', 'category' => 'quality_control'],
            ['id' => 'quality_audits', 'name' => 'Quality Audits', 'category' => 'quality_control'],
            ['id' => 'schedule_monitoring', 'name' => 'Schedule Monitoring', 'category' => 'cost_schedule_control'],
            ['id' => 'payment_certification', 'name' => 'Payment Certification', 'category' => 'cost_schedule_control'],
            ['id' => 'cost_tracking', 'name' => 'Cost Tracking', 'category' => 'cost_schedule_control'],
            ['id' => 'variation_management', 'name' => 'Variation Management', 'category' => 'cost_schedule_control'],
            ['id' => 'final_inspection', 'name' => 'Final Inspection', 'category' => 'project_closeout'],
            ['id' => 'snagging_list', 'name' => 'Snagging List', 'category' => 'project_closeout'],
            ['id' => 'comprehensive_snagging', 'name' => 'Comprehensive Snagging', 'category' => 'project_closeout'],
            ['id' => 'closeout_documentation', 'name' => 'Closeout Documentation', 'category' => 'project_closeout'],
            ['id' => 'as_built_coordination', 'name' => 'As-Built Coordination', 'category' => 'project_closeout'],
            ['id' => 'warranty_documentation', 'name' => 'Warranty Documentation', 'category' => 'project_closeout'],
            ['id' => 'handover_management', 'name' => 'Handover Management', 'category' => 'project_closeout'],

            // Interior Design Services
            ['id' => 'client_interview', 'name' => 'Client Interview', 'category' => 'briefing'],
            ['id' => 'space_requirements', 'name' => 'Space Requirements', 'category' => 'briefing'],
            ['id' => 'style_preferences', 'name' => 'Style Preferences', 'category' => 'briefing'],
            ['id' => 'budget_discussion', 'name' => 'Budget Discussion', 'category' => 'briefing'],
            ['id' => 'site_survey', 'name' => 'Site Survey', 'category' => 'briefing'],
            ['id' => 'detailed_site_survey', 'name' => 'Detailed Site Survey', 'category' => 'briefing'],
            ['id' => 'mood_boards', 'name' => 'Mood Boards', 'category' => 'concept'],
            ['id' => 'color_schemes', 'name' => 'Color Schemes', 'category' => 'concept'],
            ['id' => 'material_palettes', 'name' => 'Material Palettes', 'category' => 'concept'],
            ['id' => 'concept_sketches', 'name' => 'Concept Sketches', 'category' => 'concept'],
            ['id' => 'space_planning', 'name' => 'Space Planning', 'category' => 'concept'],
            ['id' => 'concept_presentation', 'name' => 'Concept Presentation', 'category' => 'concept'],
            ['id' => 'detailed_floor_plans', 'name' => 'Detailed Floor Plans', 'category' => 'design_development'],
            ['id' => 'elevation_drawings', 'name' => 'Elevation Drawings', 'category' => 'design_development'],
            ['id' => 'ceiling_plans', 'name' => 'Ceiling Plans', 'category' => 'design_development'],
            ['id' => 'lighting_design', 'name' => 'Lighting Design', 'category' => 'design_development'],
            ['id' => 'furniture_layout', 'name' => 'Furniture Layout', 'category' => 'design_development'],
            ['id' => 'custom_furniture_design', 'name' => 'Custom Furniture Design', 'category' => 'design_development'],
            ['id' => 'millwork_details', 'name' => 'Millwork Details', 'category' => 'design_development'],
            ['id' => '3d_renderings', 'name' => '3D Renderings', 'category' => 'visualization'],
            ['id' => 'key_views', 'name' => 'Key Views', 'category' => 'visualization'],
            ['id' => 'multiple_views', 'name' => 'Multiple Views', 'category' => 'visualization'],
            ['id' => 'material_samples', 'name' => 'Material Samples', 'category' => 'visualization'],
            ['id' => 'photorealistic_renderings', 'name' => 'Photorealistic Renderings', 'category' => 'visualization'],
            ['id' => 'walkthrough_animation_interior', 'name' => 'Walkthrough Animation', 'category' => 'visualization'],
            ['id' => 'construction_drawings', 'name' => 'Construction Drawings', 'category' => 'documentation'],
            ['id' => 'material_specifications', 'name' => 'Material Specifications', 'category' => 'documentation'],
            ['id' => 'furniture_specifications', 'name' => 'Furniture Specifications', 'category' => 'documentation'],
            ['id' => 'finishes_schedule', 'name' => 'Finishes Schedule', 'category' => 'documentation'],
            ['id' => 'joinery_details', 'name' => 'Joinery Details', 'category' => 'documentation'],
            ['id' => 'lighting_specifications', 'name' => 'Lighting Specifications', 'category' => 'documentation'],
            ['id' => 'vendor_sourcing', 'name' => 'Vendor Sourcing', 'category' => 'procurement'],
            ['id' => 'price_negotiation', 'name' => 'Price Negotiation', 'category' => 'procurement'],
            ['id' => 'purchase_orders', 'name' => 'Purchase Orders', 'category' => 'procurement'],
            ['id' => 'delivery_coordination', 'name' => 'Delivery Coordination', 'category' => 'procurement'],
            ['id' => 'site_supervision_interior', 'name' => 'Site Supervision', 'category' => 'implementation'],
            ['id' => 'quality_inspection', 'name' => 'Quality Inspection', 'category' => 'implementation'],
            ['id' => 'installation_coordination', 'name' => 'Installation Coordination', 'category' => 'implementation'],
            ['id' => 'snagging_interior', 'name' => 'Snagging', 'category' => 'implementation'],
            ['id' => 'final_styling', 'name' => 'Final Styling', 'category' => 'implementation'],

            // Landscape Design Services
            ['id' => 'site_visit', 'name' => 'Site Visit', 'category' => 'site_analysis'],
            ['id' => 'topography_review', 'name' => 'Topography Review', 'category' => 'site_analysis'],
            ['id' => 'climate_analysis', 'name' => 'Climate Analysis', 'category' => 'site_analysis'],
            ['id' => 'existing_vegetation_survey', 'name' => 'Existing Vegetation Survey', 'category' => 'site_analysis'],
            ['id' => 'detailed_site_survey_landscape', 'name' => 'Detailed Site Survey', 'category' => 'site_analysis'],
            ['id' => 'soil_testing', 'name' => 'Soil Testing', 'category' => 'site_analysis'],
            ['id' => 'drainage_analysis', 'name' => 'Drainage Analysis', 'category' => 'site_analysis'],
            ['id' => 'comprehensive_site_survey', 'name' => 'Comprehensive Site Survey', 'category' => 'site_analysis'],
            ['id' => 'utility_mapping', 'name' => 'Utility Mapping', 'category' => 'site_analysis'],
            ['id' => 'environmental_assessment', 'name' => 'Environmental Assessment', 'category' => 'site_analysis'],
            ['id' => 'master_plan_concept', 'name' => 'Master Plan Concept', 'category' => 'concept'],
            ['id' => 'zoning_diagram', 'name' => 'Zoning Diagram', 'category' => 'concept'],
            ['id' => 'plant_palette', 'name' => 'Plant Palette', 'category' => 'concept'],
            ['id' => 'concept_sketches_landscape', 'name' => 'Concept Sketches', 'category' => 'concept'],
            ['id' => 'hardscape_materials', 'name' => 'Hardscape Materials', 'category' => 'concept'],
            ['id' => 'water_features_concept', 'name' => 'Water Features Concept', 'category' => 'concept'],
            ['id' => 'client_presentations', 'name' => 'Client Presentations', 'category' => 'concept'],
            ['id' => 'detailed_master_plan', 'name' => 'Detailed Master Plan', 'category' => 'design_development'],
            ['id' => 'planting_plan', 'name' => 'Planting Plan', 'category' => 'design_development'],
            ['id' => 'irrigation_layout', 'name' => 'Irrigation Layout', 'category' => 'design_development'],
            ['id' => 'irrigation_design', 'name' => 'Irrigation Design', 'category' => 'design_development'],
            ['id' => 'hardscape_layout', 'name' => 'Hardscape Layout', 'category' => 'design_development'],
            ['id' => 'lighting_plan', 'name' => 'Lighting Plan', 'category' => 'design_development'],
            ['id' => 'grading_plan', 'name' => 'Grading Plan', 'category' => 'design_development'],
            ['id' => 'drainage_design', 'name' => 'Drainage Design', 'category' => 'design_development'],
            ['id' => 'water_features_design', 'name' => 'Water Features Design', 'category' => 'design_development'],
            ['id' => '3d_renderings_landscape', 'name' => '3D Renderings', 'category' => 'visualization'],
            ['id' => 'perspective_views', 'name' => 'Perspective Views', 'category' => 'visualization'],
            ['id' => 'multiple_perspective_views', 'name' => 'Multiple Perspective Views', 'category' => 'visualization'],
            ['id' => 'material_boards', 'name' => 'Material Boards', 'category' => 'visualization'],
            ['id' => 'photorealistic_renderings_landscape', 'name' => 'Photorealistic Renderings', 'category' => 'visualization'],
            ['id' => 'seasonal_views', 'name' => 'Seasonal Views', 'category' => 'visualization'],
            ['id' => 'walkthrough_animation_landscape', 'name' => 'Walkthrough Animation', 'category' => 'visualization'],
            ['id' => 'construction_drawings_landscape', 'name' => 'Construction Drawings', 'category' => 'construction_documents'],
            ['id' => 'planting_specifications', 'name' => 'Planting Specifications', 'category' => 'construction_documents'],
            ['id' => 'material_specifications_landscape', 'name' => 'Material Specifications', 'category' => 'construction_documents'],
            ['id' => 'construction_details', 'name' => 'Construction Details', 'category' => 'construction_documents'],
            ['id' => 'irrigation_specifications', 'name' => 'Irrigation Specifications', 'category' => 'construction_documents'],
            ['id' => 'lighting_specifications_landscape', 'name' => 'Lighting Specifications', 'category' => 'construction_documents'],
            ['id' => 'bill_of_quantities_landscape', 'name' => 'Bill of Quantities', 'category' => 'construction_documents'],
            ['id' => 'tender_documents_landscape', 'name' => 'Tender Documents', 'category' => 'tendering'],
            ['id' => 'contractor_prequalification', 'name' => 'Contractor Prequalification', 'category' => 'tendering'],
            ['id' => 'bid_evaluation_landscape', 'name' => 'Bid Evaluation', 'category' => 'tendering'],
            ['id' => 'construction_supervision_landscape', 'name' => 'Construction Supervision', 'category' => 'implementation'],
            ['id' => 'plant_material_inspection', 'name' => 'Plant Material Inspection', 'category' => 'implementation'],
            ['id' => 'quality_control_landscape', 'name' => 'Quality Control', 'category' => 'implementation'],
            ['id' => 'progress_monitoring_landscape', 'name' => 'Progress Monitoring', 'category' => 'implementation'],
            ['id' => 'snagging_landscape', 'name' => 'Snagging', 'category' => 'implementation'],
            ['id' => 'maintenance_guidelines', 'name' => 'Maintenance Guidelines', 'category' => 'implementation'],

            // Fit-Out Design Services
            ['id' => 'space_assessment', 'name' => 'Space Assessment', 'category' => 'briefing'],
            ['id' => 'functional_requirements', 'name' => 'Functional Requirements', 'category' => 'briefing'],
            ['id' => 'budget_estimation', 'name' => 'Budget Estimation', 'category' => 'briefing'],
            ['id' => 'timeline_planning', 'name' => 'Timeline Planning', 'category' => 'briefing'],
            ['id' => 'detailed_space_assessment', 'name' => 'Detailed Space Assessment', 'category' => 'briefing'],
            ['id' => 'brand_guidelines_review', 'name' => 'Brand Guidelines Review', 'category' => 'briefing'],
            ['id' => 'comprehensive_space_assessment', 'name' => 'Comprehensive Space Assessment', 'category' => 'briefing'],
            ['id' => 'workflow_analysis', 'name' => 'Workflow Analysis', 'category' => 'briefing'],
            ['id' => 'stakeholder_consultation', 'name' => 'Stakeholder Consultation', 'category' => 'briefing'],
            ['id' => 'space_layout', 'name' => 'Space Layout', 'category' => 'design'],
            ['id' => 'finishes_selection', 'name' => 'Finishes Selection', 'category' => 'design'],
            ['id' => 'basic_furniture_layout', 'name' => 'Basic Furniture Layout', 'category' => 'design'],
            ['id' => 'color_scheme', 'name' => 'Color Scheme', 'category' => 'design'],
            ['id' => 'furniture_layout_fitout', 'name' => 'Furniture Layout', 'category' => 'design'],
            ['id' => 'lighting_design_fitout', 'name' => 'Lighting Design', 'category' => 'design'],
            ['id' => 'signage_design', 'name' => 'Signage Design', 'category' => 'design'],
            ['id' => 'custom_furniture_design_fitout', 'name' => 'Custom Furniture Design', 'category' => 'design'],
            ['id' => 'branding_integration', 'name' => 'Branding Integration', 'category' => 'design'],
            ['id' => 'mep_coordination', 'name' => 'MEP Coordination', 'category' => 'technical_design'],
            ['id' => 'partition_details', 'name' => 'Partition Details', 'category' => 'technical_design'],
            ['id' => 'door_schedule', 'name' => 'Door Schedule', 'category' => 'technical_design'],
            ['id' => 'ceiling_details', 'name' => 'Ceiling Details', 'category' => 'technical_design'],
            ['id' => 'hvac_coordination', 'name' => 'HVAC Coordination', 'category' => 'technical_design'],
            ['id' => 'data_infrastructure', 'name' => 'Data Infrastructure', 'category' => 'technical_design'],
            ['id' => 'acoustic_design', 'name' => 'Acoustic Design', 'category' => 'technical_design'],
            ['id' => 'construction_drawings_fitout', 'name' => 'Construction Drawings', 'category' => 'documentation'],
            ['id' => 'reflected_ceiling_plans', 'name' => 'Reflected Ceiling Plans', 'category' => 'documentation'],
            ['id' => 'electrical_layouts', 'name' => 'Electrical Layouts', 'category' => 'documentation'],
            ['id' => 'mechanical_layouts', 'name' => 'Mechanical Layouts', 'category' => 'documentation'],
            ['id' => 'plumbing_layouts', 'name' => 'Plumbing Layouts', 'category' => 'documentation'],
            ['id' => 'finishes_schedule_fitout', 'name' => 'Finishes Schedule', 'category' => 'documentation'],
            ['id' => 'specifications', 'name' => 'Specifications', 'category' => 'documentation'],
            ['id' => 'basic_specifications', 'name' => 'Basic Specifications', 'category' => 'documentation'],
            ['id' => 'joinery_details_fitout', 'name' => 'Joinery Details', 'category' => 'documentation'],
            ['id' => 'door_hardware_schedule', 'name' => 'Door Hardware Schedule', 'category' => 'documentation'],
            ['id' => 'floor_plans', 'name' => 'Floor Plans', 'category' => 'documentation'],
            ['id' => '3d_renderings_fitout', 'name' => '3D Renderings', 'category' => 'visualization'],
            ['id' => 'key_area_views', 'name' => 'Key Area Views', 'category' => 'visualization'],
            ['id' => 'photorealistic_renderings_fitout', 'name' => 'Photorealistic Renderings', 'category' => 'visualization'],
            ['id' => 'multiple_area_views', 'name' => 'Multiple Area Views', 'category' => 'visualization'],
            ['id' => 'virtual_tour', 'name' => 'Virtual Tour', 'category' => 'visualization'],
            ['id' => 'tender_documents_fitout', 'name' => 'Tender Documents', 'category' => 'tendering'],
            ['id' => 'bill_of_quantities_fitout', 'name' => 'Bill of Quantities', 'category' => 'tendering'],
            ['id' => 'bid_analysis', 'name' => 'Bid Analysis', 'category' => 'tendering'],
            ['id' => 'contractor_selection', 'name' => 'Contractor Selection', 'category' => 'tendering'],
            ['id' => 'furniture_procurement', 'name' => 'Furniture Procurement', 'category' => 'procurement'],
            ['id' => 'equipment_procurement', 'name' => 'Equipment Procurement', 'category' => 'procurement'],
            ['id' => 'project_management', 'name' => 'Project Management', 'category' => 'implementation'],
            ['id' => 'site_supervision_fitout', 'name' => 'Site Supervision', 'category' => 'implementation'],
            ['id' => 'contractor_coordination_fitout', 'name' => 'Contractor Coordination', 'category' => 'implementation'],
            ['id' => 'quality_control_fitout', 'name' => 'Quality Control', 'category' => 'implementation'],
            ['id' => 'progress_monitoring_fitout', 'name' => 'Progress Monitoring', 'category' => 'implementation'],
            ['id' => 'change_management', 'name' => 'Change Management', 'category' => 'implementation'],
            ['id' => 'snagging_fitout', 'name' => 'Snagging', 'category' => 'implementation'],
            ['id' => 'handover_coordination', 'name' => 'Handover Coordination', 'category' => 'implementation'],
            ['id' => 'final_inspection_closeout', 'name' => 'Final Inspection', 'category' => 'closeout'],
            ['id' => 'as_built_drawings_closeout', 'name' => 'As-Built Drawings', 'category' => 'closeout'],
            ['id' => 'operation_manuals', 'name' => 'Operation Manuals', 'category' => 'closeout'],
            ['id' => 'warranty_documentation_closeout', 'name' => 'Warranty Documentation', 'category' => 'closeout'],
            ['id' => 'maintenance_guidelines_closeout', 'name' => 'Maintenance Guidelines', 'category' => 'closeout'],
            ['id' => 'space_handbook', 'name' => 'Space Handbook', 'category' => 'closeout'],
        ];

        $services = [];
        foreach ($serviceDefinitions as $index => $serviceData) {
            $stageId = $serviceData['category'];
            if (!isset($stages[$stageId])) {
                continue;
            }

            $service = Service::create([
                'service_stage_id' => $stages[$stageId]->id,
                'slug' => $serviceData['id'],
                'name' => $serviceData['name'],
                'description' => null,
                'is_optional' => false,
                'sort_order' => $index + 1,
            ]);
            $services[$serviceData['id']] = $service;
        }

        return $services;
    }

    /**
     * Seed main services
     */
    private function seedMainServices(): array
    {
        $mainServiceDefinitions = [
            ['id' => 'engineering', 'name' => 'Engineering', 'description' => 'Consultation and supervision services'],
            ['id' => 'design_interior', 'name' => 'Interior Design', 'description' => 'Interior design and space planning services'],
            ['id' => 'design_landscape', 'name' => 'Landscape Design', 'description' => 'Landscape and outdoor space design services'],
            ['id' => 'design_fitout', 'name' => 'Fit-Out Design', 'description' => 'Commercial and residential fit-out services'],
        ];

        $mainServices = [];
        foreach ($mainServiceDefinitions as $index => $mainServiceData) {
            $mainService = MainService::create([
                'slug' => $mainServiceData['id'],
                'name' => $mainServiceData['name'],
                'description' => $mainServiceData['description'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
            $mainServices[$mainServiceData['id']] = $mainService;
        }

        return $mainServices;
    }

    /**
     * Seed sub-services
     */
    private function seedSubServices(array $mainServices): array
    {
        $subServiceDefinitions = [
            ['id' => 'engineering_consultation', 'name' => 'Engineering Consultation', 'main_service' => 'engineering'],
            ['id' => 'engineering_supervision', 'name' => 'Engineering Supervision', 'main_service' => 'engineering'],
        ];

        $subServices = [];
        foreach ($subServiceDefinitions as $index => $subServiceData) {
            $mainServiceId = $mainServices[$subServiceData['main_service']]->id;
            $subService = SubService::create([
                'main_service_id' => $mainServiceId,
                'slug' => $subServiceData['id'],
                'name' => $subServiceData['name'],
                'description' => null,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
            $subServices[$subServiceData['id']] = $subService;
        }

        return $subServices;
    }

    /**
     * Seed service packages - Generic 3-tier packages not tied to specific services
     */
    private function seedServicePackages(array $mainServices, array $subServices): array
    {
        $packageDefinitions = [
            [
                'id' => 'basic',
                'name' => 'Basic Package',
                'description' => 'Essential services covering concept development and initial design phases. Ideal for clients with clear requirements seeking fundamental design solutions.',
            ],
            [
                'id' => 'standard',
                'name' => 'Standard Package',
                'description' => 'Comprehensive services including detailed design, documentation, and coordination. Perfect for clients requiring complete design and technical specifications with professional oversight.',
            ],
            [
                'id' => 'premium',
                'name' => 'Premium Package',
                'description' => 'Full-service solution with end-to-end project support including design, documentation, procurement, supervision, and project closeout. Designed for clients seeking turnkey solutions with complete project management.',
            ],
        ];

        $packages = [];
        foreach ($packageDefinitions as $index => $packageData) {
            $package = ServicePackage::create([
                'main_service_id' => null,
                'sub_service_id' => null,
                'slug' => $packageData['id'],
                'name' => $packageData['name'],
                'description' => $packageData['description'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
            $packages[$packageData['id']] = $package;
        }

        return $packages;
    }

    /**
     * Link services to packages via pivot table
     * Note: With the new 3-package system, services are NOT pre-linked to packages.
     * Users will select services independently during project creation based on their chosen main/sub services.
     * The package selection (Basic, Standard, Premium) is separate and used for pricing/scope definition.
     */
    private function linkServicesToPackages(array $packages, array $services, array $stages): void
    {
        // With the new design, packages are generic and not pre-linked to specific services.
        // Services are selected during project creation based on:
        // 1. Main Service (e.g., Engineering, Interior Design)
        // 2. Sub Service (e.g., Engineering Consultation, Engineering Supervision) - if applicable
        // 3. Package level (Basic, Standard, Premium) - for pricing/scope guidance

        // The package_service pivot table is no longer used for pre-defined relationships.
        // Instead, the project_services table will store the user's custom service selections.

        $this->command->info('Skipping service-to-package linking (services are now selected independently during project creation)');
    }

    /**
     * Helper method to attach services to a package
     */
    private function attachServicesToPackage(ServicePackage $package, array $stageServices, array $services, array $stages): void
    {
        $sortOrder = 1;
        foreach ($stageServices as $stageData) {
            $stageSlug = $stageData['stage'];
            $serviceIds = $stageData['services'];

            if (!isset($stages[$stageSlug])) {
                continue;
            }

            $stageId = $stages[$stageSlug]->id;

            foreach ($serviceIds as $serviceSlug) {
                if (!isset($services[$serviceSlug])) {
                    continue;
                }

                $service = $services[$serviceSlug];

                // Check if the relationship already exists
                if (!DB::table('package_service')
                    ->where('service_package_id', $package->id)
                    ->where('service_id', $service->id)
                    ->exists()) {
                    DB::table('package_service')->insert([
                        'service_package_id' => $package->id,
                        'service_id' => $service->id,
                        'service_stage_id' => $stageId,
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
