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
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('main_service_id')->nullable()->after('project_manager_id')->constrained('main_services')->onDelete('set null');
            $table->foreignId('sub_service_id')->nullable()->after('main_service_id')->constrained('sub_services')->onDelete('set null');
            $table->foreignId('service_package_id')->nullable()->after('sub_service_id')->constrained('service_packages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['main_service_id']);
            $table->dropForeign(['sub_service_id']);
            $table->dropForeign(['service_package_id']);
            $table->dropColumn(['main_service_id', 'sub_service_id', 'service_package_id']);
        });
    }
};
