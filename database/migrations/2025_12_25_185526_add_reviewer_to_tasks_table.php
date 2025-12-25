<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('assigned_to')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'task_template_id')) {
                $table->foreignId('task_template_id')->nullable()->after('milestone_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'requires_review')) {
                $table->boolean('requires_review')->default(false)->after('progress');
            }
            if (!Schema::hasColumn('tasks', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('tasks', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('reviewed_at');
            }
        });

        // Add index if it doesn't exist
        $indexExists = collect(DB::select("SHOW INDEX FROM tasks WHERE Key_name = 'tasks_reviewed_by_status_index'"))->isNotEmpty();
        if (!$indexExists && Schema::hasColumn('tasks', 'reviewed_by')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index(['reviewed_by', 'status'], 'tasks_reviewed_by_status_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('tasks', 'task_template_id')) {
                $table->dropForeign(['task_template_id']);
                $table->dropColumn('task_template_id');
            }
            if (Schema::hasColumn('tasks', 'requires_review')) {
                $table->dropColumn('requires_review');
            }
            if (Schema::hasColumn('tasks', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('tasks', 'review_notes')) {
                $table->dropColumn('review_notes');
            }
        });
    }
};
