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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('project_number')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('status')->default('planning'); // planning, active, on_hold, completed, cancelled
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('location')->nullable();
            $table->integer('progress')->default(0); // 0-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
