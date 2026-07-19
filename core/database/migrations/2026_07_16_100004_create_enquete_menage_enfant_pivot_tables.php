<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnqueteMenageEnfantPivotTables extends Migration
{
    protected $tables = [
        'enquete_menage_enfant_raisons_non_scolarisation',
        'enquete_menage_enfant_raisons_pas_extrait',
        'enquete_menage_enfant_situations_pfte',
        'enquete_menage_enfant_raisons_travail_abus',
        'enquete_menage_enfant_mesures_enfant',
        'enquete_menage_enfant_mesures_menage',
        'enquete_menage_enfant_mesures_communaute',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
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
        foreach ($this->tables as $table) {
            Schema::dropIfExists($table);
        }
    }
}
