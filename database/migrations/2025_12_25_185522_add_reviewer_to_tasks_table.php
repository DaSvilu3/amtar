<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('assigned_to')->constrained('users')->onDelete('set null');
            $table->foreignId('task_template_id')->nullable()->after('milestone_id')->constrained()->onDelete('set null');
            $table->boolean('requires_review')->default(false)->after('progress');
            $table->timestamp('reviewed_at')->nullable()->after('completed_at');
            $table->text('review_notes')->nullable()->after('reviewed_at');

            $table->index(['reviewed_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['task_template_id']);
            $table->dropIndex(['reviewed_by', 'status']);
            $table->dropColumn(['reviewed_by', 'task_template_id', 'requires_review', 'reviewed_at', 'review_notes']);
        });
    }
};
