<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Handle login logic here
    return redirect('/admin/dashboard');
})->name('login.post');

// Admin Routes (protected by auth middleware in production)
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Multiple Dashboards
    Route::prefix('dashboards')->group(function () {
        Route::get('/finance', function () {
            return view('admin.dashboards.finance');
        })->name('admin.dashboards.finance');
        
        Route::get('/projects', function () {
            return view('admin.dashboards.projects');
        })->name('admin.dashboards.projects');
        
        Route::get('/services', function () {
            return view('admin.dashboards.services');
        })->name('admin.dashboards.services');
        
        Route::get('/pipeline', function () {
            return view('admin.dashboards.pipeline');
        })->name('admin.dashboards.pipeline');
        
        Route::get('/hr', function () {
            return view('admin.dashboards.hr');
        })->name('admin.dashboards.hr');
        
        Route::get('/performance', function () {
            return view('admin.dashboards.performance');
        })->name('admin.dashboards.performance');
    });
    
    // Engineering Services
    Route::prefix('engineering')->group(function () {
        Route::get('/consulting', function () {
            return view('admin.engineering.consulting');
        })->name('admin.engineering.consulting');
        
        Route::get('/supervision', function () {
            return view('admin.engineering.supervision');
        })->name('admin.engineering.supervision');
    });
    
    // Design Services
    Route::prefix('design')->group(function () {
        Route::get('/interiors', function () {
            return view('admin.design.interiors');
        })->name('admin.design.interiors');
        
        Route::get('/landscape', function () {
            return view('admin.design.landscape');
        })->name('admin.design.landscape');
        
        Route::get('/fitout', function () {
            return view('admin.design.fitout');
        })->name('admin.design.fitout');
    });
    
    // Projects Management
    Route::get('/projects', function () {
        return view('admin.projects');
    })->name('admin.projects');
    
    Route::get('/tasks', function () {
        return view('admin.tasks');
    })->name('admin.tasks');
    
    Route::get('/milestones', function () {
        return view('admin.milestones');
    })->name('admin.milestones');
    
    // Client Management
    Route::get('/clients', function () {
        return view('admin.clients');
    })->name('admin.clients');
    
    Route::get('/contracts', function () {
        return view('admin.contracts');
    })->name('admin.contracts');
    
    Route::get('/approvals', function () {
        return view('admin.approvals');
    })->name('admin.approvals');
    
    // Communication
    Route::get('/notifications', function () {
        return view('admin.notifications');
    })->name('admin.notifications');
    
    Route::get('/emails', function () {
        return view('admin.emails');
    })->name('admin.emails');
    
    Route::get('/messages', function () {
        return view('admin.messages');
    })->name('admin.messages');
    
    // Reports & Analytics
    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('admin.analytics');
    
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('admin.reports');
    
    // Settings
    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');
    
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
    
    Route::get('/integrations', function () {
        return view('admin.integrations');
    })->name('admin.integrations');
});
