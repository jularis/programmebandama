<?php

namespace App\Exports;

use App\Models\Producteur; 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ProducteursExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        $manager = auth()->user();
        $producteurs = Producteur::joinRelationship('localite.section')
            ->with('localite.section')
            ->where('cooperative_id', $manager->cooperative_id)->get();
     
        return view('manager.producteur.ProducteursAllExcel',compact('producteurs'));
    }

    public function title(): string
    {
        Return "Producteurs";
    }
}
