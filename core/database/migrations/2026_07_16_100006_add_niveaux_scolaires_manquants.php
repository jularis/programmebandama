<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNiveauxScolairesManquants extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $superieureId = DB::table('niveaux_etudes')->where('nom', 'Superieure')->value('id');

        $autreId = DB::table('niveaux_etudes')->where('nom', 'Autre')->value('id');
        if (!$autreId) {
            $autreId = DB::table('niveaux_etudes')->insertGetId([
                'nom' => 'Autre',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $classes = [
            [$superieureId, 'Bac+1'],
            [$superieureId, 'Bac+2'],
            [$superieureId, 'Bac+3'],
            [$autreId, 'Classe passerelle'],
            [$autreId, 'Enseignement coranique'],
            [$autreId, 'Enseignement professionnel'],
        ];

        foreach ($classes as [$niveauId, $nom]) {
            $exists = DB::table('classes')->where('niveaux_etude_id', $niveauId)->where('nom', $nom)->exists();
            if (!$exists) {
                DB::table('classes')->insert([
                    'niveaux_etude_id' => $niveauId,
                    'nom' => $nom,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $names = ['Bac+1', 'Bac+2', 'Bac+3', 'Classe passerelle', 'Enseignement coranique', 'Enseignement professionnel'];
        DB::table('classes')->whereIn('nom', $names)->delete();
        DB::table('niveaux_etudes')->where('nom', 'Autre')->delete();
    }
}
