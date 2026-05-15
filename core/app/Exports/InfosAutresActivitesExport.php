<?php

namespace App\Exports;

use App\Models\Producteur_info;
use App\Models\Producteur_infos_autresactivite;
use App\Models\Producteur_infos_maladieenfant;
use App\Models\Producteur_infos_typeculture;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class InfosAutresActivitesExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        $manager = auth()->user();
        $autresactivites = Producteur_infos_autresactivite::joinRelationship('producteurInfo.producteur.localite.section')
            ->where('cooperative_id', $manager->cooperative_id)->get();

        return view('manager.producteur.InfosAutresActivitesExcel',compact('autresactivites'));
    }

    public function title(): string
    {
        Return "Infos Autres Activites";
    }
}
