<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::with('client')->get();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectSeeder first.');
            return;
        }

        $admin = User::whereHas('roles', fn($q) => $q->where('slug', 'administrator'))->first();
        $projectManager = User::whereHas('roles', fn($q) => $q->where('slug', 'project-manager'))->first();
        $createdBy = $projectManager?->id ?? $admin?->id;

        $contractTerms = "TERMS AND CONDITIONS\n\n" .
            "1. SCOPE OF WORK\n" .
            "The Consultant shall provide professional interior design services as outlined in the project description.\n\n" .
            "2. PAYMENT TERMS\n" .
            "Payment shall be made according to the milestone schedule attached hereto.\n\n" .
            "3. TIMELINE\n" .
            "Work shall commence upon contract signing and proceed according to the agreed schedule.\n\n" .
            "4. REVISIONS\n" .
            "Up to three (3) rounds of revisions are included in the scope of work.\n\n" .
            "5. INTELLECTUAL PROPERTY\n" .
            "All design concepts and documents become the property of the Client upon final payment.\n\n" .
            "6. CONFIDENTIALITY\n" .
            "Both parties agree to maintain confidentiality of project information.\n\n" .
            "7. TERMINATION\n" .
            "Either party may terminate this agreement with thirty (30) days written notice.";

        $totalContracts = 0;

        foreach ($projects as $project) {
            // Skip cancelled projects (no contracts)
            if ($project->status === 'cancelled') {
                continue;
            }

            // Determine contract status based on project status
            $contractStatus = match($project->status) {
                'pending' => rand(0, 100) > 70 ? 'draft' : 'active',
                'in_progress' => 'active',
                'on_hold' => 'active',
                'completed' => 'completed',
                default => 'draft',
            };

            // Determine signed date
            $signedDate = null;
            if (in_array($contractStatus, ['active', 'completed'])) {
                $signedDate = $project->start_date?->copy()->subDays(rand(3, 14));
            }

            // Generate contract number
            $year = ($project->start_date ?? now())->format('Y');
            $contractNumber = sprintf('CNT-%s-%04d', $year, $totalContracts + 1);

            // Contract services list
            $services = [];
            if ($project->mainService) {
                $services[] = $project->mainService->name;
            }
            if ($project->subService) {
                $services[] = $project->subService->name;
            }
            if ($project->servicePackage) {
                $services[] = $project->servicePackage->name;
            }

            Contract::create([
                'contract_number' => $contractNumber,
                'title' => "{$project->name} - Service Agreement",
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'description' => "Professional design services contract for {$project->name}. " .
                    "This agreement covers all design, documentation, and supervision services as specified in the project scope.",
                'value' => $project->budget,
                'currency' => 'OMR',
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'status' => $contractStatus,
                'file_path' => null,
                'terms' => $contractTerms,
                'services' => $services,
                'auto_generated' => false,
                'signed_date' => $signedDate,
                'created_by' => $createdBy,
            ]);

            $totalContracts++;
        }

        // Create some additional standalone contracts (not linked to projects yet)
        $clients = Client::where('status', 'active')->get();

        if ($clients->isNotEmpty()) {
            $standaloneContracts = [
                [
                    'title' => 'Annual Retainer Agreement - Design Services',
                    'description' => 'Annual retainer agreement for on-call interior design consultation services.',
                    'value' => 24000.00,
                    'status' => 'active',
                ],
                [
                    'title' => 'Framework Agreement - Multiple Projects',
                    'description' => 'Framework agreement covering design services for multiple upcoming projects.',
                    'value' => 150000.00,
                    'status' => 'draft',
                ],
            ];

            foreach ($standaloneContracts as $index => $contractData) {
                $client = $clients->random();
                $totalContracts++;

                Contract::create([
                    'contract_number' => sprintf('CNT-2024-%04d', $totalContracts),
                    'title' => $contractData['title'],
                    'client_id' => $client->id,
                    'project_id' => null,
                    'description' => $contractData['description'],
                    'value' => $contractData['value'],
                    'currency' => 'OMR',
                    'start_date' => now()->addDays(rand(7, 30)),
                    'end_date' => now()->addYear(),
                    'status' => $contractData['status'],
                    'file_path' => null,
                    'terms' => $contractTerms,
                    'services' => ['Consultation', 'Design Services'],
                    'auto_generated' => false,
                    'signed_date' => $contractData['status'] === 'active' ? now()->subDays(rand(5, 20)) : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info("Created {$totalContracts} contracts");
    }
}
