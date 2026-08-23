<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStockMagasinCentral implements WithMultipleSheets
{
    protected $date;
    protected $magasin;
    protected $search;

    public function __construct($date = null, $magasin = null, $search = null)
    {
        $this->date    = $date;
        $this->magasin = $magasin;
        $this->search  = $search;
    }

    public function sheets(): array
    {
        $feuilles = [];
        $sheets   = [new StockCentralLivraisonExport($this->date, $this->magasin, $this->search)];
        $sheets   = array_merge($sheets, $feuilles);

        return $sheets;
    }
}
