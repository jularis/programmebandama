<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('organisation_settings') && !Schema::hasTable('companies')) {
            Schema::rename('organisation_settings', 'companies');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('organisation_settings') && Schema::hasTable('companies')) {
            Schema::rename('companies', 'organisation_settings');
        }
    }
};
