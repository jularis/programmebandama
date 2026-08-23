<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suivi_parcelles', function (Blueprint $table) {
            if (!Schema::hasColumn('suivi_parcelles', 'productionCampagnePrecedente')) {
                $table->decimal('productionCampagnePrecedente', 12, 2)->nullable()->after('campagne_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suivi_parcelles', function (Blueprint $table) {
            if (Schema::hasColumn('suivi_parcelles', 'productionCampagnePrecedente')) {
                $table->dropColumn('productionCampagnePrecedente');
            }
        });
    }
};
