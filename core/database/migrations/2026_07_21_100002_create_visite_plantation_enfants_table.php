<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visite_plantation_enfants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('visite_id')->index();

            $table->string('codeEnfant', 50)->nullable();
            $table->string('nom', 150)->nullable();
            $table->date('dateNaissance')->nullable();
            $table->string('sexe', 10)->nullable();

            $table->string('lienParente', 100)->nullable();
            $table->string('autreLienParente', 150)->nullable();

            $table->string('raisonNeVitPasParents', 150)->nullable();
            $table->string('autreRaisonNeVitPasParents', 150)->nullable();

            $table->string('situationScolaire', 50)->nullable();
            $table->string('niveauScolaire', 50)->nullable();
            $table->text('autreRaisonNonScolarisation')->nullable();

            $table->string('extraitNaissance', 10)->nullable();

            $table->text('autreRaisonTravailAbus')->nullable();
            $table->text('autreMesure')->nullable();

            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visite_plantation_enfants');
    }
};
