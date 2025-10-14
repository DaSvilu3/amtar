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

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (protected by auth middleware in production)
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Multiple Dashboards
    Route::prefix('dashboards')->name('dashboards.')->group(function () {
        Route::get('/finance', function () {
            return view('admin.dashboards.finance');
        })->name('finance');

        Route::get('/projects', function () {
            return view('admin.dashboards.projects');
        })->name('projects');

        Route::get('/services', function () {
            return view('admin.dashboards.services');
        })->name('services');

        Route::get('/pipeline', function () {
            return view('admin.dashboards.pipeline');
        })->name('pipeline');

        Route::get('/hr', function () {
            return view('admin.dashboards.hr');
        })->name('hr');

        Route::get('/performance', function () {
            return view('admin.dashboards.performance');
        })->name('performance');
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

    // Tasks & Milestones (existing simple routes)
    Route::get('/tasks', function () {
        return view('admin.tasks');
    })->name('tasks');

    Route::get('/milestones', function () {
        return view('admin.milestones');
    })->name('milestones');

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
});
