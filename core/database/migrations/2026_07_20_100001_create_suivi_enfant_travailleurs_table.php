<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suivi_enfant_travailleurs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('enfant_id')->index();

            $table->string('raisonInterview', 50)->nullable();
            $table->date('dateEnquete')->nullable();
            $table->string('nomEnqueteur', 150)->nullable();

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

            $table->string('autreThemeSensibilisation', 150)->nullable();
            $table->integer('nombreHommesSensibilises')->nullable();
            $table->integer('nombreFemmesSensibilisees')->nullable();
            $table->integer('nombreGarconsSensibilises')->nullable();
            $table->integer('nombreFillesSensibilisees')->nullable();
            $table->integer('totalPersonnesSensibilisees')->nullable();
            $table->string('telephoneProducteurSensibilisation', 30)->nullable();
            $table->string('photoSensibilisation', 255)->nullable();

            $table->string('etatSoumission', 20)->default('Soumis');

            $table->integer('status')->default(1);
            $table->integer('uid')->nullable();
            $table->integer('userid')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suivi_enfant_travailleurs');
    }
};
