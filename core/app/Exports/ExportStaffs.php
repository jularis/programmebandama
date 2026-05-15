<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportStaffs implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        $manager = auth()->user();
       $staffs = User::active()->where([['user_type','staff'],['cooperative_id', $manager->cooperative_id]])->get();
        // TODO: Implement view() method.
        return view('manager.staff.StaffsAllExcel',compact('staffs'));
    } 
}
