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
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_stage_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->integer('estimated_hours')->nullable();
            $table->integer('default_duration_days')->nullable();
            $table->json('required_skills')->nullable();
            $table->enum('required_expertise_level', ['junior', 'mid', 'senior', 'lead'])->nullable();
            $table->boolean('requires_review')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id', 'is_active']);
        });

        // Task template dependencies
        Schema::create('task_template_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('depends_on_template_id')->constrained('task_templates')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['task_template_id', 'depends_on_template_id'], 'template_dependency_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_template_dependencies');
        Schema::dropIfExists('task_templates');
    }
};
