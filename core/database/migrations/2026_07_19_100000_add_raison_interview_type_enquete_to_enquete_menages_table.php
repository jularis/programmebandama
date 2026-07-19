<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enquete_menages', function (Blueprint $table) {
            $table->string('raisonInterview', 50)->nullable()->after('id');
            $table->string('typeEnquete', 50)->nullable()->after('raisonInterview');
        });
    }

    public function down()
    {
        Schema::table('enquete_menages', function (Blueprint $table) {
            $table->dropColumn(['raisonInterview', 'typeEnquete']);
        });
    }
};
