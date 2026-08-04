<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('suivi_enfant_travailleurs', function (Blueprint $table) {
            $table->string('presentDisponible', 3)->nullable()->after('extraitNaissance');
            $table->string('raisonAbsent', 150)->nullable()->after('presentDisponible');
        });
    }

    public function down()
    {
        Schema::table('suivi_enfant_travailleurs', function (Blueprint $table) {
            $table->dropColumn(['presentDisponible', 'raisonAbsent']);
        });
    }
};
