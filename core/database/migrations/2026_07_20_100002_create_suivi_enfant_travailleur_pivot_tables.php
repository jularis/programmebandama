<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'suivi_enf_trav_actions_remediation',
        'suivi_enf_trav_raisons_non_scolarisation',
        'suivi_enf_trav_raisons_pas_extrait',
        'suivi_enf_trav_situations_pfte',
        'suivi_enf_trav_raisons_travail_abus',
        'suivi_enf_trav_mesures_enfant',
        'suivi_enf_trav_mesures_menage',
        'suivi_enf_trav_mesures_communaute',
        'suivi_enf_trav_themes',
        'suivi_enf_trav_outils',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('suivi_id')->index();
                $table->string('valeur', 191);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
