<?php

namespace Tests\Browser\Engineer;

use App\Models\File;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FileUploadTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $engineer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer', 'name' => 'Engineer']);
        $this->engineer->roles()->attach($engineerRole);

        Storage::fake('public');
    }

    public function test_engineer_uploads_file_to_task()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Task Details')
                ->attach('file', __DIR__ . '/../../Fixtures/sample.pdf')
                ->press('Upload File')
                ->waitForText('File uploaded successfully');
        });

        $this->assertDatabaseHas('files', [
            'fileable_type' => Task::class,
            'fileable_id' => $task->id,
        ]);
    }

    public function test_engineer_uploads_multiple_files()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->attach('files[]', __DIR__ . '/../../Fixtures/sample.pdf')
                ->attach('files[]', __DIR__ . '/../../Fixtures/image.jpg')
                ->press('Upload Files')
                ->waitForText('2 files uploaded successfully');
        });

        $fileCount = File::where('fileable_type', Task::class)
            ->where('fileable_id', $task->id)
            ->count();

        $this->assertEquals(2, $fileCount);
    }

    public function test_engineer_cannot_upload_invalid_file_type()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->attach('file', __DIR__ . '/../../Fixtures/malicious.exe')
                ->press('Upload File')
                ->waitForText('The file must be a file of type: pdf, jpg, jpeg, png, docx');
        });
    }

    public function test_engineer_cannot_upload_oversized_file()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->attach('file', __DIR__ . '/../../Fixtures/large-file.pdf') // > 10MB
                ->press('Upload File')
                ->waitForText('The file must not be greater than 10240 kilobytes');
        });
    }

    public function test_engineer_views_uploaded_files()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        File::factory()->create([
            'fileable_type' => Task::class,
            'fileable_id' => $task->id,
            'original_name' => 'requirements.pdf',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Attached Files')
                ->assertSee('requirements.pdf');
        });
    }

    public function test_engineer_downloads_file()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $file = File::factory()->create([
            'fileable_type' => Task::class,
            'fileable_id' => $task->id,
        ]);

        $this->browse(function (Browser $browser) use ($task, $file) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->click('a[href*="/files/' . $file->id . '/download"]')
                ->pause(2000); // Wait for download
        });
    }

    public function test_engineer_deletes_uploaded_file()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $file = File::factory()->create([
            'fileable_type' => Task::class,
            'fileable_id' => $task->id,
        ]);

        $this->browse(function (Browser $browser) use ($file) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Delete File')
                ->waitForDialog()
                ->acceptDialog()
                ->waitForText('File deleted successfully');
        });

        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    public function test_engineer_previews_image_file()
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $file = File::factory()->create([
            'fileable_type' => Task::class,
            'fileable_id' => $task->id,
            'mime_type' => 'image/jpeg',
            'original_name' => 'screenshot.jpg',
        ]);

        $this->browse(function (Browser $browser) use ($file) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->click('.file-preview-button')
                ->waitFor('#file-preview-modal')
                ->assertVisible('img[src*="/files/' . $file->id . '"]');
        });
    }
}
