<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Skill;
use Illuminate\Support\Facades\Hash;

/**
 * EmployeeSeeder - Creates realistic employees for AMTAR Design Consultancy
 *
 * Creates 5 full-time employees + 2 freelancers with appropriate roles and skills
 */
class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding employees...');

        // Get roles
        $roles = Role::all()->keyBy('slug');

        // Get skills
        $skills = Skill::all()->keyBy('slug');

        if ($skills->isEmpty()) {
            $this->command->warn('No skills found. Run TaskTemplateSeeder first to create skills.');
        }

        // Define employees
        $employees = [
            // Leadership (2)
            [
                'name' => 'Ayman Alhandhali',
                'email' => 'ayman@amtar.om',
                'phone' => '+968 9123 4567',
                'role' => 'administrator',
                'is_active' => true,
                'skills' => [
                    'project-management' => 'expert',
                    'client-liaison' => 'expert',
                    'cost-estimation' => 'advanced',
                    'scheduling' => 'advanced',
                ],
                'notes' => 'CEO',
            ],
            [
                'name' => 'Nimah Al Rashdi',
                'email' => 'nimah@amtar.om',
                'phone' => '+968 9234 5678',
                'role' => 'project-manager',
                'is_active' => true,
                'skills' => [
                    'architecture' => 'expert',
                    'interior-design' => 'expert',
                    'space-planning' => 'expert',
                    'client-liaison' => 'expert',
                    'presentation' => 'advanced',
                    'project-management' => 'expert',
                    '3d-modeling' => 'advanced',
                    'rendering' => 'advanced',
                ],
                'notes' => 'Founder & Head of Architecture',
            ],

            // Technical Team (3)
            [
                'name' => 'Technical Staff 1',
                'email' => 'technical1@amtar.om',
                'phone' => '+968 9345 6789',
                'role' => 'engineer',
                'is_active' => true,
                'skills' => [
                    'autocad' => 'expert',
                    'revit' => 'advanced',
                    '3d-modeling' => 'advanced',
                    'rendering' => 'advanced',
                    'technical-docs' => 'advanced',
                    'interior-design' => 'intermediate',
                ],
                'notes' => 'Technical / CAD & 3D',
            ],
            [
                'name' => 'Follow-up Staff',
                'email' => 'followup@amtar.om',
                'phone' => '+968 9456 7890',
                'role' => 'engineer',
                'is_active' => true,
                'skills' => [
                    'project-management' => 'advanced',
                    'client-liaison' => 'advanced',
                    'vendor-coordination' => 'advanced',
                    'scheduling' => 'advanced',
                    'procurement' => 'intermediate',
                    'quality-control' => 'intermediate',
                ],
                'notes' => 'Follow-up & Coordination',
            ],
            [
                'name' => 'Site Staff',
                'email' => 'site@amtar.om',
                'phone' => '+968 9567 8901',
                'role' => 'engineer',
                'is_active' => true,
                'skills' => [
                    'site-supervision' => 'expert',
                    'quality-control' => 'advanced',
                    'snagging' => 'advanced',
                    'handover' => 'advanced',
                    'vendor-coordination' => 'intermediate',
                ],
                'notes' => 'Site Supervision',
            ],

            // Freelancers (2)
            [
                'name' => 'Freelance MEP Engineer',
                'email' => 'mep.freelance@amtar.om',
                'phone' => '+968 9678 9012',
                'role' => 'engineer',
                'is_active' => true,
                'skills' => [
                    'mep' => 'expert',
                    'hvac' => 'expert',
                    'electrical' => 'advanced',
                    'plumbing' => 'advanced',
                    'technical-docs' => 'intermediate',
                ],
                'notes' => 'Freelance MEP Engineer',
            ],
            [
                'name' => 'Freelance Landscape Designer',
                'email' => 'landscape.freelance@amtar.om',
                'phone' => '+968 9789 0123',
                'role' => 'engineer',
                'is_active' => true,
                'skills' => [
                    'landscape' => 'expert',
                    'architecture' => 'intermediate',
                    'autocad' => 'advanced',
                    '3d-modeling' => 'intermediate',
                    'rendering' => 'intermediate',
                ],
                'notes' => 'Freelance Landscape Designer',
            ],
        ];

        $employeesCreated = 0;

        foreach ($employees as $employeeData) {
            // Check if user already exists
            if (User::where('email', $employeeData['email'])->exists()) {
                $this->command->warn("User {$employeeData['email']} already exists, skipping.");
                continue;
            }

            // Create user
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('Amtar2024!'),
                'phone' => $employeeData['phone'],
                'is_active' => $employeeData['is_active'],
            ]);

            // Assign role
            if (isset($roles[$employeeData['role']])) {
                $user->roles()->attach($roles[$employeeData['role']]->id);
            }

            // Assign skills with proficiency levels
            if (!empty($employeeData['skills']) && $skills->isNotEmpty()) {
                $skillData = [];
                foreach ($employeeData['skills'] as $skillSlug => $proficiency) {
                    if ($skills->has($skillSlug)) {
                        $skillData[$skills[$skillSlug]->id] = [
                            'proficiency_level' => $proficiency,
                        ];
                    }
                }
                if (!empty($skillData)) {
                    $user->skills()->sync($skillData);
                }
            }

            $employeesCreated++;
            $this->command->line("  Created: {$employeeData['name']} ({$employeeData['role']})");
        }

        $this->command->info("Created {$employeesCreated} employees.");
        $this->command->newLine();
        $this->command->warn('Default password for all employees: Amtar2024!');
        $this->command->warn('Please change passwords after first login.');
    }
}
