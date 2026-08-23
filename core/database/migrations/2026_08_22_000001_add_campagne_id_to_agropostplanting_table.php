<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agropostplanting', function (Blueprint $table) {
            if (!Schema::hasColumn('agropostplanting', 'campagne_id')) {
                $table->unsignedBigInteger('campagne_id')->nullable()->after('cooperative_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agropostplanting', function (Blueprint $table) {
            if (Schema::hasColumn('agropostplanting', 'campagne_id')) {
                $table->dropColumn('campagne_id');
            }
        });
    }
};
