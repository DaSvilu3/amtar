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

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (protected by auth middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Multiple Dashboards
    Route::prefix('dashboards')->name('dashboards.')->group(function () {
        Route::get('/finance', [DashboardController::class, 'finance'])->name('finance');
        Route::get('/projects', [DashboardController::class, 'projects'])->name('projects');
        Route::get('/services', [DashboardController::class, 'services'])->name('services');
        Route::get('/pipeline', [DashboardController::class, 'pipeline'])->name('pipeline');
        Route::get('/hr', [DashboardController::class, 'hr'])->name('hr');
        Route::get('/performance', [DashboardController::class, 'performance'])->name('performance');
    });

    // Engineering Services
    Route::prefix('engineering')->name('engineering.')->group(function () {
        Route::get('/consulting', function () {
            return view('admin.engineering.consulting');
        })->name('consulting');

        Route::get('/supervision', function () {
            return view('admin.engineering.supervision');
        })->name('supervision');
    });

    // Design Services
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

    // Tasks & Milestones
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::get('api/projects/{project}/tasks', [TaskController::class, 'getProjectTasks'])->name('api.projects.tasks');

    // Task Assignment & Review Routes
    Route::get('tasks/{task}/suggestions', [TaskController::class, 'getAssignmentSuggestions'])->name('tasks.suggestions');
    Route::post('tasks/{task}/auto-assign', [TaskController::class, 'autoAssign'])->name('tasks.auto-assign');
    Route::post('projects/{project}/tasks/generate', [TaskController::class, 'generateFromTemplates'])->name('tasks.generate');
    Route::post('tasks/{task}/submit-review', [TaskController::class, 'submitForReview'])->name('tasks.submit-review');
    Route::post('tasks/{task}/approve', [TaskController::class, 'approveReview'])->name('tasks.approve');
    Route::post('tasks/{task}/reject', [TaskController::class, 'rejectReview'])->name('tasks.reject');
    Route::get('tasks/pending-reviews', [TaskController::class, 'pendingReviews'])->name('tasks.pending-reviews');

    Route::resource('milestones', MilestoneController::class);
    Route::post('milestones/generate/{project}', [MilestoneController::class, 'generateFromProject'])->name('milestones.generate');
    Route::get('api/projects/{project}/milestones', [MilestoneController::class, 'getProjectMilestones'])->name('api.projects.milestones');

    Route::get('/approvals', function () {
        return view('admin.approvals');
    })->name('approvals');

    // Reports & Analytics
    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');

    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');

    // CRUD Resource Routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('files', FileController::class);
    Route::resource('integrations', IntegrationController::class);
    Route::resource('notification-templates', NotificationTemplateController::class);
    Route::resource('email-templates', EmailTemplateController::class);
    Route::resource('message-templates', MessageTemplateController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('contracts', ContractController::class);
    Route::get('contracts/{contract}/print', [ContractController::class, 'print'])->name('contracts.print');
    Route::get('contracts/{contract}/download-docx', [ContractController::class, 'downloadDocx'])->name('contracts.download-docx');
    Route::get('contracts/{contract}/download-pdf', [ContractController::class, 'downloadPdf'])->name('contracts.download-pdf');
    Route::get('contracts/{contract}/preview', [ContractController::class, 'preview'])->name('contracts.preview');
    Route::resource('document-types', DocumentTypeController::class);

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

    // API Routes for dynamic service loading
    Route::get('/api/services/sub-services/{mainServiceId}', [ProjectController::class, 'getSubServices'])->name('api.services.subservices');
    Route::get('/api/services/packages', [ProjectController::class, 'getPackages'])->name('api.services.packages');
    Route::get('/api/services/package-services/{packageId}', [ProjectController::class, 'getPackageServices'])->name('api.services.package-services');
    Route::get('/api/services/all', [ProjectController::class, 'getAllServices'])->name('api.services.all');
    Route::get('/api/services/stages', [ProjectController::class, 'getServiceStages'])->name('api.services.stages');
});
