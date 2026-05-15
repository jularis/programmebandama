<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class StockMagasinSection extends Model
{

    use Searchable, GlobalStatus, PowerJoins;
    protected $table = "stock_magasin_sections";

    public function livraisonInfo()
    {
        return $this->belongsTo(LivraisonInfo::class, 'livraison_info_id');
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
    public function campagnePeriode()
    {
        return $this->belongsTo(CampagnePeriode::class, 'campagne_periode_id');
    }
    public function magasinSection()
    {
        return $this->belongsTo(MagasinSection::class, 'magasin_section_id');
    }
    public function scopeDateFilterWithTable($query, $column = 'created_at')
    {
        $table = 'stock_magasin_sections.';
        if (!request()->date) {
            return $query;
        }
        if (request()->table) {
            $table = request()->table . '.';
        }
        $date      = explode('-', request()->date);

        $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');

        $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

        request()->merge(['start_date' => $startDate, 'end_date' => $endDate]);

        request()->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
        ]);

        return  $query->whereDate($table . $column, '>=', $startDate)->whereDate($table . $column, '<=', $endDate);
    }
}
