<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstimationImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'campagne' => 'required',
            'estimproduction' => 'required',
            'codeproducteur' => 'required',
            'superficie' => 'required',
        ];
    }

    public function collection(Collection $collection)
    {
        $manager = auth()->user();
        $cooperativeId = $manager->cooperative_id;
        $imported = 0;
        $missingParcelles = '';
        $invalidCampagnes = '';

        if (!count($collection)) {
            $notify[] = ['error', "Il n'y a aucune donnees dans le fichier."];
            return back()->withNotify($notify);
        }

        foreach ($collection as $row) {
            $campagneValue = trim((string) $row['campagne']);
            $codeProd = trim((string) $row['codeproducteur']);
            $superficie = $this->normalizeNumber($row['superficie']);
            $superficie = is_numeric($superficie) ? round($superficie, 2) : $superficie;

            $campagne = $this->findCampagne($campagneValue, $cooperativeId);
            if ($campagne == null) {
                $invalidCampagnes .= $campagneValue . ' , ';
                continue;
            }

            $parcelle = DB::table('parcelles as pa')
                ->join('producteurs as p', 'pa.producteur_id', '=', 'p.id')
                ->join('localites as l', 'p.localite_id', '=', 'l.id')
                ->join('sections as s', 'l.section_id', '=', 's.id')
                ->where('s.cooperative_id', $cooperativeId)
                ->where(function ($query) use ($codeProd) {
                    $query->where('p.codeProd', $codeProd)
                        ->orWhere('p.codeProdapp', $codeProd);
                })
                ->where('pa.superficie', $superficie)
                ->select('pa.*', 'p.codeProdapp', 'p.codeProd')
                ->first();

            if ($parcelle == null) {
                $missingParcelles .= $codeProd . ' , ';
                continue;
            }

            DB::table('estimations')->insert([
                'parcelle_id' => $parcelle->id,
                'campagne_id' => $campagne->id,
                'EsP' => $this->normalizeNumber($row['estimproduction']),
                'date_estimation' => now(),
                'userid' => $manager->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $imported++;
        }

        if ($imported > 0) {
            if ($invalidCampagnes != '') {
                $notify[] = ['warning', "Les campagnes suivantes ne sont pas liees a votre cooperative ou sont inactives : $invalidCampagnes"];
            }

            if ($missingParcelles != '') {
                $notify[] = ['warning', "Les Producteurs dont les codes suivent : $missingParcelles n'ont pas de parcelles dans la base."];
            }

            $notify[] = ['success', "$imported Estimations ont ete creees avec succes."];
            return back()->withNotify($notify);
        }

        if ($invalidCampagnes != '') {
            $notify[] = ['error', "Les campagnes suivantes ne sont pas liees a votre cooperative ou sont inactives : $invalidCampagnes"];
            return back()->withNotify($notify);
        }

        if ($missingParcelles != '') {
            $notify[] = ['error', "Les Producteurs dont les codes suivent : $missingParcelles n'ont pas de parcelles dans la base."];
            return back()->withNotify($notify);
        }
    }

    private function findCampagne($value, $cooperativeId)
    {
        return DB::table('campagnes')
            ->where('status', 1)
            ->where('cooperative_id', $cooperativeId)
            ->where(function ($query) use ($value) {
                if (is_numeric($value)) {
                    $query->where('id', $value);
                }

                $query->orWhere('nom', $value);
            })
            ->select('id')
            ->first();
    }

    private function normalizeNumber($value)
    {
        $value = Str::before((string) $value, ' ');
        $value = Str::replaceFirst(',', '.', $value);
        $value = Str::replaceFirst('m2', '', $value);
        $value = Str::replaceFirst('m²', '', $value);
        $value = Str::replaceFirst('mÂ²', '', $value);

        return trim($value);
    }
}
