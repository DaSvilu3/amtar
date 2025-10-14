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
        Schema::table('files', function (Blueprint $table) {
            $table->foreignId('document_type_id')->nullable()->after('category')->constrained()->onDelete('set null');
            $table->string('entity_type')->nullable()->after('document_type_id'); // client, project, contract, etc
            $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropColumn(['document_type_id', 'entity_type', 'entity_id']);
        });
    }
};
