<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enquete_menages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id')->nullable();
            $table->unsignedInteger('localite_id')->nullable();
            $table->unsignedInteger('producteur_id')->nullable();

            $table->date('dateEnquete')->nullable();
            $table->string('nomEnqueteur', 150)->nullable();
            $table->integer('nombreEnfantsEnquetes')->nullable();
            $table->string('sexeProducteur', 30)->nullable();
            $table->string('codeProducteur', 50)->nullable();

            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('altitude', 50)->nullable();
            $table->string('precisionGps', 50)->nullable();

            $table->string('estProducteurRepondant', 10)->nullable();
            $table->string('nomRepondant', 150)->nullable();
            $table->string('titreRepondant', 100)->nullable();

            $table->string('producteurDisponible', 10)->nullable();
            $table->string('raisonIndisponibilite', 150)->nullable();
            $table->date('datePlanification')->nullable();
            $table->text('autreRaisonRefus')->nullable();
            $table->string('consentement', 10)->nullable();

            $table->string('situationMatrimoniale', 100)->nullable();
            $table->integer('nombreAdultes')->nullable();
            $table->integer('nombreEnfants0a4')->nullable();
            $table->integer('nombreEnfants5a17')->nullable();
            $table->integer('totalPersonnesMenage')->nullable();

            $table->string('aEnfantACharge', 10)->nullable();
            $table->integer('nombreEnfantsACharge')->nullable();

            $table->string('autreThemeSensibilisation', 150)->nullable();
            $table->integer('nombreHommesSensibilises')->nullable();
            $table->integer('nombreFemmesSensibilisees')->nullable();
            $table->integer('nombreGarconsSensibilises')->nullable();
            $table->integer('nombreFillesSensibilisees')->nullable();
            $table->integer('totalPersonnesSensibilisees')->nullable();
            $table->string('telephoneProducteurSensibilisation', 30)->nullable();
            $table->string('photoSensibilisation', 255)->nullable();

            $table->string('etatSoumission', 20)->default('Soumis');
            $table->string('statutFin', 50)->nullable();

            $table->integer('status')->default(1);
            $table->integer('uid')->nullable();
            $table->integer('userid')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enquete_menages');
    }
};
