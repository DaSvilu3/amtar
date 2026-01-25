<?php

namespace Tests\Browser\Administrator;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContractGenerationTest extends DuskTestCase
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

    public function test_admin_generates_contract_for_project()
    {
        $client = Client::factory()->create([
            'name' => 'Test Corporation',
            'email' => 'client@testcorp.om',
        ]);

        $project = Project::factory()->create([
            'name' => 'Engineering Services',
            'client_id' => $client->id,
            'budget' => 50000,
        ]);

        $this->browse(function (Browser $browser) use ($project) {
            $browser->loginAs($this->admin)
                ->visit('/admin/contracts/create?project_id=' . $project->id)
                ->assertSee('Generate Contract')
                ->assertSee($project->name)
                ->select('language', 'ar')
                ->check('include_terms')
                ->press('Generate Contract')
                ->waitForText('Contract generated successfully');
        });

        $this->assertDatabaseHas('contracts', [
            'project_id' => $project->id,
        ]);
    }

    public function test_admin_downloads_contract_as_pdf()
    {
        $contract = Contract::factory()->create();

        $this->browse(function (Browser $browser) use ($contract) {
            $browser->loginAs($this->admin)
                ->visit('/admin/contracts/' . $contract->id)
                ->assertSee('Contract Details')
                ->click('a[href*="download"][href*="pdf"]')
                ->pause(2000); // Wait for download
        });

        // Note: Actual file download verification would require checking download directory
    }

    public function test_admin_downloads_contract_as_docx()
    {
        $contract = Contract::factory()->create();

        $this->browse(function (Browser $browser) use ($contract) {
            $browser->loginAs($this->admin)
                ->visit('/admin/contracts/' . $contract->id)
                ->assertSee('Contract Details')
                ->click('a[href*="download"][href*="docx"]')
                ->pause(2000); // Wait for download
        });
    }

    public function test_admin_regenerates_contract_with_different_language()
    {
        $contract = Contract::factory()->create(['language' => 'ar']);

        $this->browse(function (Browser $browser) use ($contract) {
            $browser->loginAs($this->admin)
                ->visit('/admin/contracts/' . $contract->id . '/edit')
                ->select('language', 'en')
                ->press('Regenerate Contract')
                ->waitForText('Contract regenerated successfully');
        });

        $contract->refresh();
        $this->assertEquals('en', $contract->language);
    }

    public function test_contract_includes_service_tables()
    {
        $contract = Contract::factory()->create();

        $this->browse(function (Browser $browser) use ($contract) {
            $browser->loginAs($this->admin)
                ->visit('/admin/contracts/' . $contract->id)
                ->assertSee('Service Details')
                ->assertSee('Total Amount');
        });
    }
}
