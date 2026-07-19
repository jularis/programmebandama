<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visite_plantations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id')->nullable();
            $table->unsignedInteger('localite_id')->nullable();
            $table->unsignedInteger('producteur_id')->nullable();

            $table->string('raisonInterview', 50)->nullable();
            $table->string('typeEnquete', 50)->nullable();
            $table->date('dateEnquete')->nullable();
            $table->string('nomEnqueteur', 150)->nullable();
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

            $table->decimal('superficiePlantation', 10, 2)->nullable();
            $table->integer('nombreManoeuvresPermanents')->nullable();
            $table->string('manoeuvresPermanentsMoins18', 10)->nullable();
            $table->integer('nombreManoeuvresJournaliers')->nullable();
            $table->string('manoeuvresJournaliersMoins18', 10)->nullable();
            $table->integer('nombreEnfants0a4')->nullable();
            $table->integer('nombreEnfants5a17')->nullable();
            $table->integer('nombrePersonnesTrouvees')->nullable();
            $table->integer('nombreEnfantsTrouves')->nullable();

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
        Schema::dropIfExists('visite_plantations');
    }
};
