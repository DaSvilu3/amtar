<?php

namespace Tests\Browser\Administrator;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProjectCreationTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator', 'name' => 'Administrator']);
        $this->admin->roles()->attach($adminRole);
    }

    public function test_admin_creates_project_with_package_selection()
    {
        $client = Client::factory()->create(['name' => 'Test Client Inc']);
        $package = ServicePackage::factory()->create(['name' => 'Engineering Package']);

        $this->browse(function (Browser $browser) use ($client, $package) {
            $browser->loginAs($this->admin)
                ->visit('/admin/projects/create')
                ->assertSee('Create New Project')
                ->type('name', 'Engineering Project Alpha')
                ->type('project_number', 'PRJ-2026-001')
                ->select('client_id', $client->id)
                ->select('service_package_id', $package->id)
                ->type('budget', '50000')
                ->type('start_date', '2026-02-01')
                ->type('end_date', '2026-08-01')
                ->press('Create Project')
                ->waitForText('Project created successfully')
                ->assertPathBeginsWith('/admin/projects/');
        });

        $this->assertDatabaseHas('projects', [
            'name' => 'Engineering Project Alpha',
            'project_number' => 'PRJ-2026-001',
            'client_id' => $client->id,
            'budget' => 50000,
        ]);
    }

    public function test_admin_creates_project_with_auto_contract_generation()
    {
        $client = Client::factory()->create();
        $package = ServicePackage::factory()->create();

        $this->browse(function (Browser $browser) use ($client, $package) {
            $browser->loginAs($this->admin)
                ->visit('/admin/projects/create')
                ->type('name', 'Contract Test Project')
                ->type('project_number', 'PRJ-2026-002')
                ->select('client_id', $client->id)
                ->select('service_package_id', $package->id)
                ->check('generate_contract')
                ->type('budget', '75000')
                ->press('Create Project')
                ->waitForText('Project created successfully');
        });

        $project = Project::where('project_number', 'PRJ-2026-002')->first();
        $this->assertNotNull($project);
        $this->assertDatabaseHas('contracts', ['project_id' => $project->id]);
    }

    public function test_admin_creates_project_with_custom_services()
    {
        $client = Client::factory()->create();

        $this->browse(function (Browser $browser) use ($client) {
            $browser->loginAs($this->admin)
                ->visit('/admin/projects/create')
                ->type('name', 'Custom Services Project')
                ->type('project_number', 'PRJ-2026-003')
                ->select('client_id', $client->id)
                ->radio('service_selection_method', 'custom')
                ->waitFor('.custom-services-section')
                ->type('budget', '100000')
                ->press('Create Project')
                ->waitForText('Project created successfully');
        });

        $this->assertDatabaseHas('projects', [
            'name' => 'Custom Services Project',
            'project_number' => 'PRJ-2026-003',
        ]);
    }

    public function test_project_creation_validation_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/projects/create')
                ->press('Create Project')
                ->waitForText('The name field is required')
                ->assertSee('The project number field is required')
                ->assertSee('The client id field is required');
        });
    }
}
