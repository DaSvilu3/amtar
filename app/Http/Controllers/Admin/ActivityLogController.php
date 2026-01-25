<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description or model name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(25)->withQueryString();

        // Get unique actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        // Get unique model types for filter dropdown
        $modelTypes = ActivityLog::distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type')
            ->map(fn ($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();

        // Get users for filter dropdown
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact(
            'activities',
            'actions',
            'modelTypes',
            'users'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $date = now()->subDays($request->days);
        $count = ActivityLog::where('created_at', '<', $date)->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', "Deleted {$count} activity log entries older than {$request->days} days.");
    }
}
