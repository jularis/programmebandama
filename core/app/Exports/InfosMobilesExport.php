<?php

namespace App\Exports;

use App\Models\Producteur_info;
use App\Models\Producteur_infos_autresactivite;
use App\Models\Producteur_infos_maladieenfant;
use App\Models\Producteur_infos_mobile;
use App\Models\Producteur_infos_typeculture;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class InfosMobilesExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.producteur.InfosMobilesExcel',[
            'mobiles' => Producteur_infos_mobile::joinRelationship('producteurInfo.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Infos Mobiles Money";
    }
}
