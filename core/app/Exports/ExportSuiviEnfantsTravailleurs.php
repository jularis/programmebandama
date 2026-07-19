<?php

namespace App\Exports;

use App\Models\SuiviEnfantTravailleur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportSuiviEnfantsTravailleurs implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('manager.suivienfanttravailleur.SuivisEnfantsTravailleursAllExcel', [
            'suivis' => SuiviEnfantTravailleur::with([
                'enfant.menage.producteur',
                'enfant.menage.localite',
                'actionsRemediation',
                'raisonsNonScolarisation',
                'raisonsPasExtrait',
                'situationsPfte',
                'raisonsTravailAbus',
                'mesuresEnfant',
                'mesuresMenage',
                'mesuresCommunaute',
                'themes',
                'outils',
            ])
                ->whereHas('enfant.menage.producteur.localite.section', function ($q) {
                    $q->where('cooperative_id', auth()->user()->cooperative_id);
                })
                ->get(),
        ]);
    }
}
