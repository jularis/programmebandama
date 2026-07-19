<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnqueteMenagePivotTables extends Migration
{
    protected $tables = [
        'enquete_menage_raison_refus',
        'enquete_menage_themes',
        'enquete_menage_outils',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('enquete_menage_id')->index();
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
