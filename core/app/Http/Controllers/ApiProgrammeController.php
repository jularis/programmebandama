<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiProgrammeController extends Controller
{
    public function index(Request $request)
    {

       $programmes = DB::table('programmes')->select('id', 'libelle')->get();

        return response()->json($programmes, 201);
    }
}
