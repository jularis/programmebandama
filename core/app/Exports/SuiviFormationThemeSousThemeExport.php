<?php

namespace App\Exports;

use App\Models\SousThemeFormation;
use App\Models\SuiviFormationTheme;
use App\Models\ThemeSousTheme;
use App\Models\TypeFormationTheme;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SuiviFormationThemeSousThemeExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.formation.FormationsSousThemeExcel',[
            'sousthemes' => ThemeSousTheme::joinRelationship('suiviFormation.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)
            ->when(request()->id, function ($query, $id) {
                $query->where('theme_sous_themes.suivi_formation_id',decrypt($id)); 
           })
            ->get()
        ]);
    }

    public function title(): string
    {
        Return "Formation Sous Themes";
    }
}
