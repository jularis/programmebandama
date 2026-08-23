<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('campagnes', 'cooperative_id') || !Schema::hasColumn('campagnes', 'nom')) {
            return;
        }

        $hasDuplicates = DB::table('campagnes')
            ->select('cooperative_id', 'nom', DB::raw('COUNT(*) as total'))
            ->whereNotNull('cooperative_id')
            ->whereNotNull('nom')
            ->groupBy('cooperative_id', 'nom')
            ->having('total', '>', 1)
            ->exists();

        if ($hasDuplicates) {
            throw new RuntimeException(
                'Impossible d’ajouter l’unicité des campagnes : des doublons existent déjà pour une même coopérative.'
            );
        }

        Schema::table('campagnes', function (Blueprint $table) {
            $table->unique(['cooperative_id', 'nom'], 'campagnes_cooperative_nom_unique');
        });
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropUnique('campagnes_cooperative_nom_unique');
        });
    }
};
