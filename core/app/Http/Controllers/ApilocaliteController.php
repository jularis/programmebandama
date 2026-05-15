<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Localite;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApilocaliteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = User::with('cooperative.sections.localites')->find($request->userid);
        $localites = $user->cooperative->sections->flatMap->localites; 
       // $localites = DB::table('user_localites as rl')->join('localites as l', 'rl.localite_id','=','l.id')->join('sections as sec', 'l.section_id','=','sec.id')->where('user_id', $request->userid)->select('l.id','l.nom','cooperative_id')->get();

        return response()->json($localites, 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $input = $request->all();
        $input['nom'] = $this->verifylocalite($input['nom']);
        $input['codeLocal'] = $this->generelocalitecode($input['nom']);
        $usersid = $input['userid'];
        $localite = Localite::create($input);

        if ($localite != null) {

            $id = $localite->id;
            if (isset($input['nomecolesprimaires']) && count($input['nomecolesprimaires']) > 0) {

                $i = 0;
                foreach ($input['nomecolesprimaires'] as $data) {
                    DB::table('localites_nomecoleprimaire')->insert(['localite_id' => $id, 'nomecole' => $data]);
                    $i++;
                }
            }
        }

        if ($usersid) {
            $local = $localite->id;
            $cooperativesid = $localite->cooperatives_id;
            $upd = DB::table('roles_localites')
                ->insert(
                    ['users_id' => $usersid, 'localites_id' => $local]
                );

            $manager = User::whereHas(
                'roles',
                function ($q) {
                    $q->where('name', 'Manager General');
                }
            )
                ->where('cooperatives_id', $cooperativesid)
                ->get();

            foreach ($manager as $data) {
                $upd2 = DB::table('roles_localites')
                    ->insert(
                        ['users_id' => $data->id, 'localites_id' => $local]
                    );
            }
        }

        return response()->json($localite, 201);
    }

    private function verifylocalite($nom)
    {
        $action = 'non';
        do {
            $data = Localite::select('nom')->where('nom', $nom)->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomLocal = $data->nom;
                $nom = Str::beforeLast($nomLocal, ' ');
                $chaine_number = Str::afterLast($nomLocal, ' ');

                if (is_numeric($chaine_number) && ($chaine_number < 10)) {
                    $zero = "00";
                } else if (is_numeric($chaine_number) && ($chaine_number < 100)) {
                    $zero = "0";
                } else {
                    $zero = "00";
                    $chaine_number = 0;
                }

                $sub = $nom . ' ';
                $lastCode = $chaine_number + 1;
                $nomLocal = $sub . $zero . $lastCode;
            } else {

                $nomLocal = $nom;
            }
            $verif = Localite::select('nom')->where('nom', $nomLocal)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->nom;
            }
        } while ($action != 'non');

        return $nomLocal;
    }

    private function generelocalitecode($name)
    {
        $action = 'non';
        do {

            $data = Localite::select('codeLocal')->where('nom', $name)->orderby('id', 'desc')->first();

            if ($data != '') {

                $code = $data->codeLocal;

                $chaine_number = Str::afterLast($code, '-');

                if ($chaine_number < 10) {
                    $zero = "00";
                } else if ($chaine_number < 100) {
                    $zero = "0";
                } else {
                    $zero = "";
                }
            } else {
                $zero = "00";
                $chaine_number = 0;
            }

            $abrege = Str::upper(Str::substr($name, 0, 3));
            $sub = $abrege . '-';
            $lastCode = $chaine_number + 1;
            $codeP = $sub . $zero . $lastCode;

            $verif = Localite::select('nom')->where('codeLocal', $codeP)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $name = $verif->nom;
            }
        } while ($action != 'non');

        return $codeP;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //
    }
}
