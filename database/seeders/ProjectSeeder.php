<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Client;
use App\Models\User;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\ServicePackage;
use App\Models\ServiceStage;
use App\Models\Service;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required references
        $clients = Client::all();
        $projectManager = User::whereHas('roles', fn($q) => $q->where('slug', 'project-manager'))->first();
        $mainServices = MainService::all()->keyBy('slug');
        $subServices = SubService::all()->keyBy('slug');
        $servicePackages = ServicePackage::all()->keyBy('slug');

        if ($clients->isEmpty() || !$projectManager) {
            $this->command->warn('Clients or Project Manager not found. Run ClientSeeder and UserSeeder first.');
            return;
        }

        $projects = [
            [
                'name' => 'Al-Rashid Villa Interior Renovation',
                'project_number' => 'PRJ-2024-001',
                'client_id' => $clients->where('company_name', 'Al-Rashid Real Estate Development')->first()?->id ?? $clients->first()->id,
                'description' => 'Complete interior renovation of a luxury villa in Shatti Al Qurum including living areas, bedrooms, and outdoor spaces.',
                'status' => 'in_progress',
                'budget' => 85000.00,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'actual_start_date' => now()->subMonths(2),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_interior']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['premium']->id ?? null,
                'location' => 'Shatti Al Qurum, Muscat',
                'progress' => 35,
            ],
            [
                'name' => 'Oman Grand Hotel Lobby Design',
                'project_number' => 'PRJ-2024-002',
                'client_id' => $clients->where('company_name', 'Oman Hospitality Group')->first()?->id ?? $clients->first()->id,
                'description' => 'Luxury hotel lobby interior design featuring modern Omani architectural elements and premium materials.',
                'status' => 'in_progress',
                'budget' => 250000.00,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(6),
                'actual_start_date' => now()->subMonths(3),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_interior']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['premium']->id ?? null,
                'location' => 'Al Khuwair, Muscat',
                'progress' => 45,
            ],
            [
                'name' => 'Gulf Commercial Tower Fit-Out',
                'project_number' => 'PRJ-2024-003',
                'client_id' => $clients->where('company_name', 'Gulf Commercial Properties')->first()?->id ?? $clients->first()->id,
                'description' => 'Complete fit-out of a 5-floor commercial office tower including common areas, executive suites, and meeting rooms.',
                'status' => 'pending',
                'budget' => 450000.00,
                'start_date' => now()->addWeeks(2),
                'end_date' => now()->addMonths(8),
                'actual_start_date' => null,
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_fitout']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'Commercial District, Sohar',
                'progress' => 0,
            ],
            [
                'name' => 'Wellness Spa Interior Design',
                'project_number' => 'PRJ-2024-004',
                'client_id' => $clients->where('company_name', 'Wellness Centers LLC')->first()?->id ?? $clients->first()->id,
                'description' => 'Serene spa interior design with relaxation areas, treatment rooms, and reception featuring natural materials.',
                'status' => 'completed',
                'budget' => 120000.00,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->subMonths(1),
                'actual_start_date' => now()->subMonths(6),
                'actual_end_date' => now()->subMonths(1)->subDays(5),
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_interior']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'Shatti Al Qurum, Muscat',
                'progress' => 100,
            ],
            [
                'name' => 'Al-Farsi Signature Restaurant',
                'project_number' => 'PRJ-2024-005',
                'client_id' => $clients->where('company_name', 'Al-Farsi Restaurants Group')->first()?->id ?? $clients->first()->id,
                'description' => 'Contemporary restaurant interior with open kitchen concept, private dining areas, and outdoor terrace.',
                'status' => 'in_progress',
                'budget' => 95000.00,
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(3),
                'actual_start_date' => now()->subMonths(1),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_interior']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'Ruwi, Muscat',
                'progress' => 25,
            ],
            [
                'name' => 'International School Campus Design',
                'project_number' => 'PRJ-2024-006',
                'client_id' => $clients->where('company_name', 'Educational Facilities Development')->first()?->id ?? $clients->first()->id,
                'description' => 'Comprehensive school interior design including classrooms, library, laboratories, and administrative offices.',
                'status' => 'on_hold',
                'budget' => 380000.00,
                'start_date' => now()->subMonths(4),
                'end_date' => now()->addMonths(6),
                'actual_start_date' => now()->subMonths(4),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_fitout']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['premium']->id ?? null,
                'location' => 'Knowledge Park, Muscat',
                'progress' => 20,
            ],
            [
                'name' => 'Industrial Warehouse Engineering',
                'project_number' => 'PRJ-2024-007',
                'client_id' => $clients->where('company_name', 'Dhofar Industrial Holdings')->first()?->id ?? $clients->first()->id,
                'description' => 'Engineering consultation for warehouse facility including structural assessment and MEP coordination.',
                'status' => 'in_progress',
                'budget' => 75000.00,
                'start_date' => now()->subMonths(1)->subWeeks(2),
                'end_date' => now()->addMonths(2),
                'actual_start_date' => now()->subMonths(1)->subWeeks(2),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['engineering']->id ?? null,
                'sub_service_id' => $subServices['engineering_consultation']->id ?? null,
                'service_package_id' => $servicePackages['basic']->id ?? null,
                'location' => 'Industrial Area, Salalah',
                'progress' => 40,
            ],
            [
                'name' => 'Luxury Villa Landscape Design',
                'project_number' => 'PRJ-2024-008',
                'client_id' => $clients->where('company_name', 'Luxury Villas Oman')->first()?->id ?? $clients->first()->id,
                'description' => 'Comprehensive landscape design for beachfront villa including gardens, pool area, and outdoor living spaces.',
                'status' => 'in_progress',
                'budget' => 65000.00,
                'start_date' => now()->subWeeks(3),
                'end_date' => now()->addMonths(3),
                'actual_start_date' => now()->subWeeks(3),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_landscape']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['premium']->id ?? null,
                'location' => 'The Wave, Muscat',
                'progress' => 15,
            ],
            [
                'name' => 'Medical Clinic Interior',
                'project_number' => 'PRJ-2024-009',
                'client_id' => $clients->where('company_name', 'Healthcare Developments LLC')->first()?->id ?? $clients->first()->id,
                'description' => 'Modern medical clinic interior design with patient comfort focus, examination rooms, and waiting areas.',
                'status' => 'completed',
                'budget' => 145000.00,
                'start_date' => now()->subMonths(8),
                'end_date' => now()->subMonths(3),
                'actual_start_date' => now()->subMonths(8),
                'actual_end_date' => now()->subMonths(3)->addDays(10),
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_interior']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'Medical City, Muscat',
                'progress' => 100,
            ],
            [
                'name' => 'Retail Store Chain Fit-Out',
                'project_number' => 'PRJ-2024-010',
                'client_id' => $clients->where('company_name', 'Muscat Retail Holdings')->first()?->id ?? $clients->first()->id,
                'description' => 'Multi-location retail store fit-out design with consistent branding elements and customer flow optimization.',
                'status' => 'cancelled',
                'budget' => 180000.00,
                'start_date' => now()->subMonths(5),
                'end_date' => now()->subMonths(1),
                'actual_start_date' => now()->subMonths(5),
                'actual_end_date' => now()->subMonths(4),
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_fitout']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'City Centre, Muscat',
                'progress' => 15,
            ],
            [
                'name' => 'Boutique Hotel Supervision',
                'project_number' => 'PRJ-2024-011',
                'client_id' => $clients->where('company_name', 'Oman Hospitality Group')->first()?->id ?? $clients->first()->id,
                'description' => 'Engineering supervision for boutique hotel construction including quality control and progress monitoring.',
                'status' => 'pending',
                'budget' => 55000.00,
                'start_date' => now()->addMonths(1),
                'end_date' => now()->addMonths(10),
                'actual_start_date' => null,
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['engineering']->id ?? null,
                'sub_service_id' => $subServices['engineering_supervision']->id ?? null,
                'service_package_id' => $servicePackages['standard']->id ?? null,
                'location' => 'Nizwa, Oman',
                'progress' => 0,
            ],
            [
                'name' => 'Corporate Headquarters Landscape',
                'project_number' => 'PRJ-2024-012',
                'client_id' => $clients->where('company_name', 'Gulf Commercial Properties')->first()?->id ?? $clients->first()->id,
                'description' => 'Corporate campus landscape design with sustainable planting, water features, and outdoor meeting areas.',
                'status' => 'in_progress',
                'budget' => 95000.00,
                'start_date' => now()->subMonths(2)->subWeeks(1),
                'end_date' => now()->addMonths(4),
                'actual_start_date' => now()->subMonths(2)->subWeeks(1),
                'actual_end_date' => null,
                'project_manager_id' => $projectManager->id,
                'main_service_id' => $mainServices['design_landscape']->id ?? null,
                'sub_service_id' => null,
                'service_package_id' => $servicePackages['premium']->id ?? null,
                'location' => 'Commercial District, Sohar',
                'progress' => 30,
            ],
        ];

        // Create projects
        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Add some project services based on the main service
            $this->seedProjectServices($project);
        }

        $this->command->info('Created ' . count($projects) . ' projects with services');
    }

    /**
     * Seed project services for a project
     */
    private function seedProjectServices(Project $project): void
    {
        // Get some random services based on the project's main service type
        $serviceStages = ServiceStage::inRandomOrder()->take(rand(3, 6))->get();

        foreach ($serviceStages as $index => $stage) {
            // Get a random service from this stage
            $service = Service::where('service_stage_id', $stage->id)->inRandomOrder()->first();

            if ($service) {
                ProjectService::create([
                    'project_id' => $project->id,
                    'service_id' => $service->id,
                    'service_stage_id' => $stage->id,
                    'is_from_package' => false,
                    'is_completed' => $project->status === 'completed',
                    'completed_at' => $project->status === 'completed' ? $project->actual_end_date : null,
                    'notes' => null,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
