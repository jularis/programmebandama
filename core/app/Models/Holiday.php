<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

 
class Holiday extends BaseModel
{

    use HasCooperative;

    const SUNDAY = 0;

    const MONDAY = 1;

    const TUESDAY = 2;

    const WEDNESDAY = 3;

    const THURSDAY = 4;

    const FRIDAY = 5;

    const SATURDAY = 6;

    // Don't forget to fill this array
    protected $fillable = ['cooperative_id','date', 'occassion'];

    protected $guarded = ['id'];
    protected $casts = [
        'date' => 'datetime',
    ];

    public static function getHolidayByDates($startDate, $endDate)
    {
        return Holiday::select(DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as holiday_date'), 'occassion')
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get();
    }

    public static function checkHolidayByDate($date)
    {
        return Holiday::Where('date', $date)->first();
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by')->withoutGlobalScope(ActiveScope::class);
    }

    public static function weekMap($format='l')
    {
        return [
            Holiday::MONDAY => now()->startOfWeek(1)->translatedFormat($format),
            Holiday::TUESDAY => now()->startOfWeek(2)->translatedFormat($format),
            Holiday::WEDNESDAY => now()->startOfWeek(3)->translatedFormat($format),
            Holiday::THURSDAY => now()->startOfWeek(4)->translatedFormat($format),
            Holiday::FRIDAY => now()->startOfWeek(5)->translatedFormat($format),
            Holiday::SATURDAY => now()->startOfWeek(6)->translatedFormat($format),
            Holiday::SUNDAY => now()->startOfWeek(7)->translatedFormat($format),
        ];
    }

}
