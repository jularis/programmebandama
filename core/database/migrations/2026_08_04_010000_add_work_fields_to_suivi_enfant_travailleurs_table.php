<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('suivi_enfant_travailleurs', function (Blueprint $table) {
            $table->integer('heuresTravailSemaine')->nullable()->after('extraitNaissance');
            $table->string('joursTravail', 10)->nullable()->after('heuresTravailSemaine');
            $table->integer('heuresTravailJournee')->nullable()->after('joursTravail');
        });
    }

    public function down()
    {
        Schema::table('suivi_enfant_travailleurs', function (Blueprint $table) {
            $table->dropColumn(['heuresTravailSemaine', 'joursTravail', 'heuresTravailJournee']);
        });
    }
};
