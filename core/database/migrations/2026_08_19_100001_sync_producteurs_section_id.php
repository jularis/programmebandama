<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('producteurs')
            ->join('localites', 'producteurs.localite_id', '=', 'localites.id')
            ->update([
                'producteurs.section_id' => DB::raw('localites.section_id'),
            ]);
    }

    public function down()
    {
        // This data migration cannot safely restore the previous section_id values.
    }
};
