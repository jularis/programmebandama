<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateSensibilisationThemes extends Migration
{
    public function up()
    {
        $updates = [
            "Taille, récolte ou écabossage avec objet tranchant" => "Taille, Récolte ou Cabossage avec objet tranchant",
            "Brûlage des parcelles" => "Brulages des parcelles",
        ];

        foreach ($updates as $old => $new) {
            DB::table('sensibilisation_themes')
                ->where('nom', $old)
                ->update(['nom' => $new]);
        }

        $insert = [
            "Production de bois de chauffe",
            "Chasse de gibier avec une arme",
            "Manipulation de produits agro-chimiques",
            "Trouaison",
            "Conduite d'engins motorisés",
            "Droits des enfants",
            "Longues heures sur les tâches non-dangereuses",
            "Travail de nuit",
            "Maltraitance physique ou morale",
        ];

        $existing = DB::table('sensibilisation_themes')->pluck('nom')->all();
        $now = date('Y-m-d H:i:s');

        foreach ($insert as $nom) {
            if (!in_array($nom, $existing, true)) {
                DB::table('sensibilisation_themes')->insert([
                    'nom' => $nom,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        DB::table('sensibilisation_themes')
            ->where('nom', 'Bûcheronnage')
            ->delete();
    }

    public function down()
    {
        $reverts = [
            "Taille, Récolte ou Cabossage avec objet tranchant" => "Taille, récolte ou écabossage avec objet tranchant",
            "Brulages des parcelles" => "Brûlage des parcelles",
        ];

        foreach ($reverts as $new => $old) {
            DB::table('sensibilisation_themes')
                ->where('nom', $new)
                ->update(['nom' => $old]);
        }

        $delete = [
            "Production de bois de chauffe",
            "Chasse de gibier avec une arme",
            "Manipulation de produits agro-chimiques",
            "Trouaison",
            "Conduite d'engins motorisés",
            "Droits des enfants",
            "Longues heures sur les tâches non-dangereuses",
            "Travail de nuit",
            "Maltraitance physique ou morale",
        ];

        DB::table('sensibilisation_themes')
            ->whereIn('nom', $delete)
            ->delete();

        if (!DB::table('sensibilisation_themes')->where('nom', 'Bûcheronnage')->exists()) {
            DB::table('sensibilisation_themes')->insert([
                'nom' => 'Bûcheronnage',
                'status' => 1,
                'created_at' => $now = date('Y-m-d H:i:s'),
                'updated_at' => $now,
            ]);
        }
    }
}
