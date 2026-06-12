<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStockMagasinCentral implements WithMultipleSheets
{
    protected $date;
    protected $magasin;

    public function __construct($date = null, $magasin = null)
    {
        $this->date    = $date;
        $this->magasin = $magasin;
    }

    public function sheets(): array
    {
        $feuilles = [];
        $sheets   = [new StockCentralLivraisonExport($this->date, $this->magasin)];
        $sheets   = array_merge($sheets, $feuilles);

        return $sheets;
    }
}
