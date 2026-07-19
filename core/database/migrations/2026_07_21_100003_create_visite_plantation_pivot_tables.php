<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $visiteTables = [
        'visite_plantation_raison_refus',
    ];

    protected $enfantTables = [
        'visite_plant_enf_raisons_non_scolarisation',
        'visite_plant_enf_raisons_pas_extrait',
        'visite_plant_enf_situations_pfte',
        'visite_plant_enf_raisons_travail_abus',
        'visite_plant_enf_mesures_enfant',
        'visite_plant_enf_mesures_menage',
        'visite_plant_enf_mesures_communaute',
    ];

    public function up()
    {
        foreach ($this->visiteTables as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('visite_id')->index();
                $table->string('valeur', 191);
                $table->timestamps();
            });
        }

        foreach ($this->enfantTables as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('enfant_id')->index();
                $table->string('valeur', 191);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        foreach (array_merge($this->visiteTables, $this->enfantTables) as $table) {
            Schema::dropIfExists($table);
        }
    }
};
