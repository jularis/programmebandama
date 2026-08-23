<?php

namespace App\Imports;

use App\Models\Campagne;
use App\Models\CampagnePeriode;
use App\Models\Estimation;
use App\Models\LivraisonInfo;
use App\Models\LivraisonPayment;
use App\Models\LivraisonPrime;
use App\Models\LivraisonProduct;
use App\Models\LivraisonProductDetail;
use App\Models\MagasinSection;
use App\Models\Parcelle;
use App\Models\Producteur;
use App\Models\ProgrammePrime;
use App\Models\StockMagasinSection;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StockMagasinSectionImport implements ToCollection, WithHeadingRow
{
    public int $importedRows = 0;
    public int $createdStocks = 0;

    public function __construct(private User $user)
    {
    }

    public function collection(Collection $collection)
    {
        if ($collection->isEmpty()) {
            throw new \Exception("Il n'y a aucune donnee dans le fichier.");
        }

        $groups = [];

        foreach ($collection as $index => $row) {
            $line = $index + 2;
            $quantity = $this->number($this->value($row, ['quantite', 'quantity', 'stock_entrant']));

            if ($quantity <= 0) {
                throw new \Exception("Ligne {$line}: la quantite doit etre superieure a 0.");
            }

            $campagne = $this->campagne($row, $line);
            $periode = $this->periode($row, $campagne, $line);
            $magasin = $this->magasin($row, $line);
            $producteur = $this->producteur($row, $line);
            $parcelle = $this->parcelle($row, $producteur, $line);
            $typeProduit = trim((string) $this->value($row, ['type_produit', 'type', 'produit']));
            $dateLivraison = $this->date($this->value($row, ['date_livraison', 'date', 'estimate_date']), $line);

            if ($typeProduit === '') {
                throw new \Exception("Ligne {$line}: le type_produit est obligatoire.");
            }

            $certificat = $typeProduit === 'Ordinaire'
                ? null
                : $this->value($row, ['certificat', 'certification']);

            $senderStaff = $this->staff($row, $line) ?: $this->user;
            $numero = trim((string) $this->value($row, ['numero_connaissement', 'connaissement', 'numero']));
            $groupKey = $numero !== ''
                ? $numero
                : implode('|', [$campagne->id, $periode->id, $magasin->id, $senderStaff->id, $dateLivraison]);

            $groups[$groupKey]['campagne'] = $campagne;
            $groups[$groupKey]['periode'] = $periode;
            $groups[$groupKey]['magasin'] = $magasin;
            $groups[$groupKey]['senderStaff'] = $senderStaff;
            $groups[$groupKey]['dateLivraison'] = $dateLivraison;
            $groups[$groupKey]['rows'][] = compact('producteur', 'parcelle', 'typeProduit', 'certificat', 'quantity', 'line');
        }

        DB::transaction(function () use ($groups) {
            foreach ($groups as $group) {
                $this->storeGroup($group);
            }
        });
    }

    private function storeGroup(array $group): void
    {
        $campagne = $group['campagne'];
        $periode = $group['periode'];
        $magasin = $group['magasin'];
        $senderStaff = $group['senderStaff'];
        $rows = $group['rows'];
        $totalQuantity = array_sum(array_column($rows, 'quantity'));

        $this->validateEstimatedVolumeLimit($rows, $campagne->id);

        $livraison = new LivraisonInfo();
        $livraison->invoice_id = getTrx();
        $livraison->code = getTrx();
        $livraison->sender_cooperative_id = $this->user->cooperative_id;
        $livraison->sender_staff_id = $senderStaff->id;
        $livraison->sender_name = trim($senderStaff->lastname.' '.$senderStaff->firstname) ?: $senderStaff->username;
        $livraison->sender_email = $senderStaff->email;
        $livraison->sender_phone = $senderStaff->mobile;
        $livraison->sender_address = $senderStaff->address;
        $livraison->receiver_cooperative_id = $this->user->cooperative_id;
        $livraison->receiver_magasin_section_id = $magasin->id;
        $livraison->receiver_name = optional($magasin->user)->lastname
            ? trim($magasin->user->lastname.' '.$magasin->user->firstname)
            : $magasin->nom;
        $livraison->receiver_email = optional($magasin->user)->email;
        $livraison->receiver_phone = optional($magasin->user)->mobile;
        $livraison->receiver_address = optional($magasin->user)->address;
        $livraison->estimate_date = $group['dateLivraison'];
        $livraison->quantity = $totalQuantity;
        $livraison->save();

        $stock = new StockMagasinSection();
        $stock->livraison_info_id = $livraison->id;
        $stock->magasin_section_id = $magasin->id;
        $stock->campagne_id = $campagne->id;
        $stock->campagne_periode_id = $periode->id;
        $stock->stocks_entrant = $totalQuantity;
        $stock->stocks_sortant = 0;
        $stock->save();
        $this->createdStocks++;

        $details = [];
        $primes = [];
        $subTotal = 0;

        foreach ($rows as $row) {
            $price = (float) $periode->prix_champ * $row['quantity'];
            $subTotal += $price;

            $product = LivraisonProduct::where([
                ['campagne_id', $campagne->id],
                ['parcelle_id', $row['parcelle']->id],
                ['certificat', $row['certificat']],
                ['type_produit', $row['typeProduit']],
            ])->first() ?: new LivraisonProduct();

            $product->livraison_info_id = $livraison->id;
            $product->parcelle_id = $row['parcelle']->id;
            $product->campagne_id = $campagne->id;
            $product->campagne_periode_id = $periode->id;
            $product->qty = (float) $product->qty + $row['quantity'];
            $product->type_produit = $row['typeProduit'];
            $product->certificat = $row['certificat'];
            $product->fee = (float) $product->fee + $price;
            $product->type_price = $periode->prix_champ;
            $product->save();

            $details[] = [
                'livraison_info_id' => $livraison->id,
                'parcelle_id' => $row['parcelle']->id,
                'campagne_id' => $campagne->id,
                'campagne_periode_id' => $periode->id,
                'qty' => $row['quantity'],
                'type_produit' => $row['typeProduit'],
                'certificat' => $row['certificat'],
                'fee' => $price,
                'type_price' => $periode->prix_champ,
                'created_at' => now(),
            ];

            $this->updateEstimation($campagne->id, $row['parcelle']->id, $row['quantity']);
            $prime = $this->primeRow($livraison->id, $campagne->id, $periode->id, $row['parcelle']->id, $row['producteur'], $row['quantity']);
            if ($prime) {
                $primes[] = $prime;
            }

            $this->importedRows++;
        }

        LivraisonProductDetail::insert($details);
        if ($primes) {
            LivraisonPrime::insert($primes);
        }

        $payment = new LivraisonPayment();
        $payment->livraison_info_id = $livraison->id;
        $payment->campagne_id = $campagne->id;
        $payment->amount = $subTotal;
        $payment->final_amount = $subTotal;
        $payment->save();
    }

    private function updateEstimation(int $campagneId, int $parcelleId, float $quantity): void
    {
        $estimation = Estimation::where([['campagne_id', $campagneId], ['parcelle_id', $parcelleId]])->first();

        $estimation->productionAnnuelle = (float) $estimation->productionAnnuelle + $quantity;
        if ($estimation->productionAnnuelle >= (float) $estimation->EsP) {
            $estimation->etat = 'Atteint';
        }
        $estimation->save();
    }

    private function validateEstimatedVolumeLimit(array $rows, int $campagneId): void
    {
        $quantitiesByParcelle = [];
        $linesByParcelle = [];

        foreach ($rows as $row) {
            $parcelleId = (int) $row['parcelle']->id;
            $quantitiesByParcelle[$parcelleId] = ($quantitiesByParcelle[$parcelleId] ?? 0) + (float) $row['quantity'];
            $linesByParcelle[$parcelleId][] = $row['line'];
        }

        foreach ($quantitiesByParcelle as $parcelleId => $quantity) {
            $estimation = Estimation::where([
                ['campagne_id', $campagneId],
                ['parcelle_id', $parcelleId],
            ])->first();

            $parcelle = Parcelle::find($parcelleId);
            $codeParcelle = $parcelle?->codeParc ?: $parcelleId;
            $lines = implode(', ', $linesByParcelle[$parcelleId] ?? []);

            if (!$estimation) {
                throw new \Exception("Ligne {$lines}: aucune estimation trouvee pour la parcelle {$codeParcelle} sur cette campagne.");
            }

            $estimatedVolume = (float) $estimation->EsP;
            if ($estimatedVolume <= 0) {
                throw new \Exception("Ligne {$lines}: le volume estime de la parcelle {$codeParcelle} est vide ou nul pour cette campagne.");
            }

            $alreadyDelivered = (float) $estimation->productionAnnuelle;
            $maximumAllowed = $estimatedVolume * 1.10;
            $newTotal = $alreadyDelivered + $quantity;

            if ($newTotal > $maximumAllowed) {
                throw new \Exception("Ligne {$lines}: volume depasse pour la parcelle {$codeParcelle}. Cumul livre {$newTotal} kg, maximum autorise {$maximumAllowed} kg (volume estime {$estimatedVolume} kg + 10%).");
            }
        }
    }

    private function primeRow(int $livraisonId, int $campagneId, int $periodeId, int $parcelleId, Producteur $producteur, float $quantity): ?array
    {
        $prime = ProgrammePrime::where('programme_id', $producteur->programme_id)->latest()->first();
        if (!$prime) {
            return null;
        }

        return [
            'livraison_info_id' => $livraisonId,
            'parcelle_id' => $parcelleId,
            'campagne_id' => $campagneId,
            'campagne_periode_id' => $periodeId,
            'quantite' => $quantity,
            'montant' => (float) $prime->prime * $quantity,
            'prime_campagne' => $prime->prime,
            'created_at' => now(),
        ];
    }

    private function campagne($row, int $line): Campagne
    {
        $value = $this->value($row, ['campagne_id', 'campagne']);
        $campagne = Campagne::where('cooperative_id', $this->user->cooperative_id)
            ->where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('nom', $value);
            })->first();

        if (!$campagne) {
            throw new \Exception("Ligne {$line}: campagne introuvable pour cette cooperative.");
        }

        return $campagne;
    }

    private function periode($row, Campagne $campagne, int $line): CampagnePeriode
    {
        $value = $this->value($row, ['periode_id', 'periode', 'campagne_periode']);
        $periode = CampagnePeriode::where('campagne_id', $campagne->id)
            ->where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('nom', $value);
            })->first();

        if (!$periode) {
            throw new \Exception("Ligne {$line}: periode introuvable pour la campagne indiquee.");
        }

        return $periode;
    }

    private function magasin($row, int $line): MagasinSection
    {
        $value = $this->value($row, ['magasin_section_id', 'magasin_section', 'magasin', 'code_magasin']);
        $magasin = MagasinSection::with('user')
            ->whereHas('section', fn($q) => $q->where('cooperative_id', $this->user->cooperative_id))
            ->where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('code', $value)->orWhere('nom', $value);
            })->first();

        if (!$magasin) {
            throw new \Exception("Ligne {$line}: magasin de section introuvable pour cette cooperative.");
        }

        return $magasin;
    }

    private function producteur($row, int $line): Producteur
    {
        $value = $this->value($row, ['producteur_id', 'code_producteur', 'codeproducteur', 'producteur']);
        $producteur = Producteur::joinRelationship('localite.section')
            ->where('cooperative_id', $this->user->cooperative_id)
            ->where(function ($query) use ($value) {
                $query->where('producteurs.id', $value)
                    ->orWhere('producteurs.codeProd', $value)
                    ->orWhere('producteurs.codeProdapp', $value);
            })
            ->select('producteurs.*')
            ->first();

        if (!$producteur) {
            throw new \Exception("Ligne {$line}: producteur introuvable pour cette cooperative.");
        }

        return $producteur;
    }

    private function parcelle($row, Producteur $producteur, int $line): Parcelle
    {
        $value = $this->value($row, ['parcelle_id', 'code_parcelle', 'codeparcelle', 'parcelle']);
        $parcelle = Parcelle::where('producteur_id', $producteur->id)
            ->where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('codeParc', $value);
            })->first();

        if (!$parcelle) {
            throw new \Exception("Ligne {$line}: parcelle introuvable pour le producteur indique.");
        }

        return $parcelle;
    }

    private function staff($row, int $line): ?User
    {
        $value = $this->value($row, ['sender_staff_id', 'staff_id', 'staff', 'delegue']);
        if (!$value) {
            return null;
        }

        $staff = User::where('cooperative_id', $this->user->cooperative_id)
            ->where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('username', $value)->orWhere('email', $value);
            })->first();

        if (!$staff) {
            throw new \Exception("Ligne {$line}: staff/delegue introuvable pour cette cooperative.");
        }

        return $staff;
    }

    private function value($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                return is_string($row[$key]) ? trim($row[$key]) : $row[$key];
            }
        }

        return null;
    }

    private function number($value): float
    {
        $value = Str::replace([' ', ','], ['', '.'], (string) $value);
        return is_numeric($value) ? (float) $value : 0;
    }

    private function date($value, int $line): string
    {
        if (!$value) {
            return now()->format('Y-m-d');
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        $timestamp = strtotime((string) $value);
        if (!$timestamp) {
            throw new \Exception("Ligne {$line}: date de livraison invalide.");
        }

        return date('Y-m-d', $timestamp);
    }
}
