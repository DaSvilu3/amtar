<?php

namespace Tests\Browser\Administrator;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReportsTest extends DuskTestCase
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

    public function test_admin_accesses_reports_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->assertSee('Reports')
                ->assertSee('Project Summary')
                ->assertSee('Task Status')
                ->assertSee('Team Performance')
                ->assertSee('Financial Report')
                ->assertSee('Client Activity')
                ->assertSee('Milestone Tracking');
        });
    }

    public function test_admin_generates_project_summary_report()
    {
        Project::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'project-summary')
                ->select('format', 'pdf')
                ->press('Generate Report')
                ->waitForText('Report generated successfully');
        });
    }

    public function test_admin_exports_task_status_to_excel()
    {
        Task::factory()->count(10)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'task-status')
                ->select('format', 'excel')
                ->press('Generate Report')
                ->waitForText('Report generated successfully')
                ->pause(2000); // Wait for download
        });
    }

    public function test_admin_filters_report_by_date_range()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'project-summary')
                ->type('start_date', '2026-01-01')
                ->type('end_date', '2026-12-31')
                ->select('format', 'pdf')
                ->press('Generate Report')
                ->waitForText('Report generated successfully');
        });
    }

    public function test_admin_filters_report_by_client()
    {
        $client = Client::factory()->create();

        $this->browse(function (Browser $browser) use ($client) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'client-activity')
                ->select('client_id', $client->id)
                ->select('format', 'pdf')
                ->press('Generate Report')
                ->waitForText('Report generated successfully');
        });
    }

    public function test_admin_generates_team_performance_report()
    {
        User::factory()->count(5)->create();
        Task::factory()->count(20)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'team-performance')
                ->select('format', 'excel')
                ->press('Generate Report')
                ->waitForText('Report generated successfully');
        });
    }

    public function test_admin_generates_financial_report()
    {
        Project::factory()->count(5)->create([
            'budget' => 100000,
            'status' => 'active',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/reports')
                ->select('report_type', 'financial')
                ->select('format', 'pdf')
                ->press('Generate Report')
                ->waitForText('Report generated successfully');
        });
    }
}
