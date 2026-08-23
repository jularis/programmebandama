<?php

namespace App\Exports;

use App\Models\FormationStaff;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class FormationStaffsExport implements FromView, WithTitle
{
    public function view(): View
    {
        $formations = FormationStaff::with([
                'cooperative',
                'campagne',
                'staffListes.user',
                'visiteurs',
                'formationStaffModuleTheme.moduleFormationStaff',
                'formationStaffModuleTheme.themeFormationStaff',
                'formationStaffEntrepriseFormateur.entreprise',
                'formationStaffEntrepriseFormateur.formateurStaff',
            ])
            ->where('cooperative_id', auth()->user()->cooperative_id)
            ->when(request()->id, function ($query, $id) {
                $query->where('id', decrypt($id));
            })
            ->latest('id')
            ->get();

        return view('manager.formation-staff.FormationsAllExcel', [
            'formations' => $formations,
        ]);
    }

    public function title(): string
    {
        return 'Formations staff';
    }
}
