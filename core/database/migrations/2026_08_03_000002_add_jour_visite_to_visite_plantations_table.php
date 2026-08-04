<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visite_plantations', function (Blueprint $table) {
            $table->string('jourVisite', 20)->nullable()->after('dateEnquete');
        });
    }

    public function down(): void
    {
        Schema::table('visite_plantations', function (Blueprint $table) {
            $table->dropColumn('jourVisite');
        });
    }
};
