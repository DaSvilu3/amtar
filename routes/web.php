<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\MilestoneController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\TaskTemplateController;
use App\Http\Controllers\Admin\NotificationController;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (protected by auth middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // ============================================
    // SHARED: All authenticated users can access
    // ============================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear/all', [NotificationController::class, 'clearAll'])->name('clear-all');
    });

    // API Routes for dynamic service loading (needed by all roles for project views)
    Route::get('/api/services/sub-services/{mainServiceId}', [ProjectController::class, 'getSubServices'])->name('api.services.subservices');
    Route::get('/api/services/packages', [ProjectController::class, 'getPackages'])->name('api.services.packages');
    Route::get('/api/services/package-services/{packageId}', [ProjectController::class, 'getPackageServices'])->name('api.services.package-services');
    Route::get('/api/services/all', [ProjectController::class, 'getAllServices'])->name('api.services.all');
    Route::get('/api/services/stages', [ProjectController::class, 'getServiceStages'])->name('api.services.stages');
    Route::post('/api/task-templates-preview', [ProjectController::class, 'getTaskTemplatesPreview'])->name('api.task-templates-preview');

    // ============================================
    // PM + ADMIN: Project management, clients, contracts
    // (Must be before Engineer routes to match specific routes like 'tasks/create' before 'tasks/{task}')
    // ============================================
    Route::middleware(['role:administrator,project-manager'])->group(function () {
        // Clients - full CRUD
        Route::resource('clients', ClientController::class);

        // Projects - create, edit, update, delete
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::patch('projects/{project}', [ProjectController::class, 'update']);
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Contracts - full CRUD
        Route::resource('contracts', ContractController::class);
        Route::get('contracts/{contract}/print', [ContractController::class, 'print'])->name('contracts.print');
        Route::get('contracts/{contract}/download-docx', [ContractController::class, 'downloadDocx'])->name('contracts.download-docx');
        Route::get('contracts/{contract}/download-pdf', [ContractController::class, 'downloadPdf'])->name('contracts.download-pdf');
        Route::get('contracts/{contract}/preview', [ContractController::class, 'preview'])->name('contracts.preview');

        // Milestones - full CRUD
        Route::resource('milestones', MilestoneController::class);
        Route::post('milestones/generate/{project}', [MilestoneController::class, 'generateFromProject'])->name('milestones.generate');
        Route::get('api/projects/{project}/milestones', [MilestoneController::class, 'getProjectMilestones'])->name('api.projects.milestones');

        // Tasks - create, edit, delete, assignment (must be before tasks/{task} route)
        Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('tasks/{task}', [TaskController::class, 'update']);
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Task assignment & review management
        Route::get('tasks/{task}/suggestions', [TaskController::class, 'getAssignmentSuggestions'])->name('tasks.suggestions');
        Route::post('tasks/{task}/auto-assign', [TaskController::class, 'autoAssign'])->name('tasks.auto-assign');
        Route::post('tasks/batch-assign', [TaskController::class, 'batchAutoAssign'])->name('tasks.batch-assign');
        Route::post('projects/{project}/tasks/generate', [TaskController::class, 'generateFromTemplates'])->name('tasks.generate');
        Route::post('tasks/{task}/approve', [TaskController::class, 'approveReview'])->name('tasks.approve');
        Route::post('tasks/{task}/reject', [TaskController::class, 'rejectReview'])->name('tasks.reject');
    });

    // ============================================
    // ENGINEER + PM + ADMIN: Task & Project viewing
    // ============================================
    Route::middleware(['role:administrator,project-manager,engineer'])->group(function () {
        // Tasks - view and status update
        Route::get('tasks/pending-reviews', [TaskController::class, 'pendingReviews'])->name('tasks.pending-reviews');
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::patch('tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.update-progress');
        Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::post('tasks/{task}/submit-review', [TaskController::class, 'submitForReview'])->name('tasks.submit-review');
        Route::get('api/projects/{project}/tasks', [TaskController::class, 'getProjectTasks'])->name('api.projects.tasks');

        // Task file uploads (engineers can upload to their assigned tasks)
        Route::post('tasks/{task}/files', [TaskController::class, 'uploadFile'])->name('tasks.upload-file');
        Route::delete('tasks/{task}/files/{file}', [TaskController::class, 'deleteFile'])->name('tasks.delete-file');

        // Projects - view only
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show')
            ->where('project', '[0-9]+');  // Only match numeric IDs, not 'create'

        // Project Notes (comments, reminders, calendar events)
        Route::post('projects/{project}/notes', [\App\Http\Controllers\Admin\ProjectNoteController::class, 'store'])->name('projects.notes.store');
        Route::put('project-notes/{note}', [\App\Http\Controllers\Admin\ProjectNoteController::class, 'update'])->name('projects.notes.update');
        Route::post('project-notes/{note}/toggle-pin', [\App\Http\Controllers\Admin\ProjectNoteController::class, 'togglePin'])->name('projects.notes.toggle-pin');
        Route::delete('project-notes/{note}', [\App\Http\Controllers\Admin\ProjectNoteController::class, 'destroy'])->name('projects.notes.destroy');
        Route::get('projects/{project}/calendar-events', [\App\Http\Controllers\Admin\ProjectNoteController::class, 'calendarEvents'])->name('projects.calendar-events');

        // Files - view and upload
        Route::get('files', [FileController::class, 'index'])->name('files.index');
        Route::get('files/create', [FileController::class, 'create'])->name('files.create');
        Route::post('files', [FileController::class, 'store'])->name('files.store');
        Route::get('files/{file}', [FileController::class, 'show'])->name('files.show');

    });

    // ============================================
    // PM + ADMIN: Additional routes (files edit/delete, approvals)
    // ============================================
    Route::middleware(['role:administrator,project-manager'])->group(function () {
        // Files - edit and delete
        Route::get('files/{file}/edit', [FileController::class, 'edit'])->name('files.edit');
        Route::put('files/{file}', [FileController::class, 'update'])->name('files.update');
        Route::patch('files/{file}', [FileController::class, 'update']);
        Route::delete('files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

        // Approvals
        Route::get('/approvals', [DashboardController::class, 'approvals'])->name('approvals');

        // Team Workload API (for tasks page)
        Route::get('/api/team-workload', [DashboardController::class, 'getTeamWorkload'])->name('api.team-workload');
    });

    // ============================================
    // ADMINISTRATOR ONLY: System management
    // ============================================
    Route::middleware(['role:administrator'])->group(function () {
        // User & Role Management
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::get('roles-matrix', [RoleController::class, 'matrix'])->name('roles.matrix');
        Route::post('roles/update-permission', [RoleController::class, 'updatePermission'])->name('roles.update-permission');

        // System Settings
        Route::resource('settings', SettingController::class);
        Route::resource('integrations', IntegrationController::class);
        Route::resource('document-types', DocumentTypeController::class);

        // Communication Templates
        Route::resource('notification-templates', NotificationTemplateController::class);
        Route::resource('email-templates', EmailTemplateController::class);
        Route::resource('message-templates', MessageTemplateController::class);

        // Skills & Task Templates
        Route::resource('skills', SkillController::class)->except(['show']);
        Route::resource('task-templates', TaskTemplateController::class);

        // Reports & Analytics
        Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

        // Service Management Routes
        Route::prefix('services')->name('services.')->group(function () {
            // Overview
            Route::get('/', [ServiceManagementController::class, 'index'])->name('index');

            // Main Services
            Route::get('/main', [ServiceManagementController::class, 'mainServicesIndex'])->name('main.index');
            Route::get('/main/create', [ServiceManagementController::class, 'mainServicesCreate'])->name('main.create');
            Route::post('/main', [ServiceManagementController::class, 'mainServicesStore'])->name('main.store');
            Route::get('/main/{mainService}/edit', [ServiceManagementController::class, 'mainServicesEdit'])->name('main.edit');
            Route::put('/main/{mainService}', [ServiceManagementController::class, 'mainServicesUpdate'])->name('main.update');
            Route::delete('/main/{mainService}', [ServiceManagementController::class, 'mainServicesDestroy'])->name('main.destroy');

            // Sub Services
            Route::get('/sub', [ServiceManagementController::class, 'subServicesIndex'])->name('sub.index');
            Route::get('/sub/create', [ServiceManagementController::class, 'subServicesCreate'])->name('sub.create');
            Route::post('/sub', [ServiceManagementController::class, 'subServicesStore'])->name('sub.store');
            Route::get('/sub/{subService}/edit', [ServiceManagementController::class, 'subServicesEdit'])->name('sub.edit');
            Route::put('/sub/{subService}', [ServiceManagementController::class, 'subServicesUpdate'])->name('sub.update');
            Route::delete('/sub/{subService}', [ServiceManagementController::class, 'subServicesDestroy'])->name('sub.destroy');

            // Service Packages
            Route::get('/packages', [ServiceManagementController::class, 'packagesIndex'])->name('packages.index');
            Route::get('/packages/create', [ServiceManagementController::class, 'packagesCreate'])->name('packages.create');
            Route::post('/packages', [ServiceManagementController::class, 'packagesStore'])->name('packages.store');
            Route::get('/packages/{package}', [ServiceManagementController::class, 'packagesShow'])->name('packages.show');
            Route::get('/packages/{package}/edit', [ServiceManagementController::class, 'packagesEdit'])->name('packages.edit');
            Route::put('/packages/{package}', [ServiceManagementController::class, 'packagesUpdate'])->name('packages.update');
            Route::delete('/packages/{package}', [ServiceManagementController::class, 'packagesDestroy'])->name('packages.destroy');

            // Service Stages
            Route::get('/stages', [ServiceManagementController::class, 'stagesIndex'])->name('stages.index');
            Route::get('/stages/create', [ServiceManagementController::class, 'stagesCreate'])->name('stages.create');
            Route::post('/stages', [ServiceManagementController::class, 'stagesStore'])->name('stages.store');
            Route::get('/stages/{stage}/edit', [ServiceManagementController::class, 'stagesEdit'])->name('stages.edit');
            Route::put('/stages/{stage}', [ServiceManagementController::class, 'stagesUpdate'])->name('stages.update');
            Route::delete('/stages/{stage}', [ServiceManagementController::class, 'stagesDestroy'])->name('stages.destroy');

            // Individual Services
            Route::get('/services', [ServiceManagementController::class, 'servicesIndex'])->name('services.index');
            Route::get('/services/create', [ServiceManagementController::class, 'servicesCreate'])->name('services.create');
            Route::post('/services', [ServiceManagementController::class, 'servicesStore'])->name('services.store');
            Route::get('/services/{service}/edit', [ServiceManagementController::class, 'servicesEdit'])->name('services.edit');
            Route::put('/services/{service}', [ServiceManagementController::class, 'servicesUpdate'])->name('services.update');
            Route::delete('/services/{service}', [ServiceManagementController::class, 'servicesDestroy'])->name('services.destroy');
        });

        // Engineering Services (Admin only)
        Route::prefix('engineering')->name('engineering.')->group(function () {
            Route::get('/consulting', function () {
                return view('admin.engineering.consulting');
            })->name('consulting');

            Route::get('/supervision', function () {
                return view('admin.engineering.supervision');
            })->name('supervision');
        });

        // Design Services (Admin only)
        Route::prefix('design')->name('design.')->group(function () {
            Route::get('/interiors', function () {
                return view('admin.design.interiors');
            })->name('interiors');

            Route::get('/landscape', function () {
                return view('admin.design.landscape');
            })->name('landscape');

            Route::get('/fitout', function () {
                return view('admin.design.fitout');
            })->name('fitout');
        });
    });
});
