<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiAbreController extends Controller
{
    public function getarbre()
    {
        //$arbres = DB::table('agroespecesarbres')->select("id","nom_scientifique","nom","strate")->get();
        $arbres = DB::table('agroespecesarbres')
            ->select("id", "nom_scientifique", "nom", "strate")
            ->where('strate', '!=', 0)
            ->get();

        return response()->json($arbres);
    }
}
