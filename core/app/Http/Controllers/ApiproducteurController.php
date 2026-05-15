<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Constants\Status;
use App\Models\Producteur;
use App\Models\DebugMobile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Producteur_info;
use Illuminate\Validation\Rule;
use App\Models\Infos_producteur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreInfoRequest;
use App\Models\Producteur_infos_mobile;
use App\Models\Producteur_certification;
use App\Models\Producteur_infos_typeculture;
use App\Http\Requests\UpdateProducteurRequest;
use App\Models\Producteur_infos_maladieenfant;
use Illuminate\Validation\ValidationException;
use App\Models\Producteur_infos_autresactivite;


class ApiproducteurController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    // 
  }

  public function getproducteurs(Request $request)
  {
    $userid = $request->userid;
    $manager = User::where('id', $userid)->first();
    $producteurs = Producteur::whereHas('localite.section.cooperative', function ($query) use ($manager) {
      $query->where('id', $manager->cooperative_id);
    })
      ->join('localites', 'producteurs.localite_id', '=', 'localites.id')
      ->leftJoin('producteur_certifications', 'producteurs.id', '=', 'producteur_certifications.producteur_id')
      ->select('producteurs.*', 'localites.section_id as section_id', 'localites.id as localite_id', 'producteurs.id as id', 'producteurs.codeProd as codeProd', 'producteurs.statut')
      ->selectRaw('GROUP_CONCAT(producteur_certifications.certification) as certification')
      ->groupBy('producteurs.nom', 'producteurs.prenoms', 'localites.section_id', 'localites.id', 'producteurs.id', 'producteurs.codeProd', 'producteurs.statut')
      ->get();
    return response()->json($producteurs, 201);
  }
  //creation de getstaff(elle retourne les staff d'une cooperative donnée)
  public function getstaff(Request $request)
  {

    $cooperativeId = $request->cooperative_id;
    $roleName = $request->role_name;
    $staffs = User::whereHas(
      'roles',
      function ($q) use ($roleName, $request) {
        if ($request->is_different == 'true') {
          $q->where('name', '!=', $roleName);
        } else {
          $q->where('name', $roleName);
        }
      }
    )
      ->where('cooperative_id', $cooperativeId)
      ->select('id', 'firstname', 'lastname', 'username', 'email', 'mobile', 'adresse')
      ->get();

    return response()->json($staffs, 201);
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
    if ($request->id) {
      $id = $request->id;
      $producteur = Producteur::findOrFail($request->id);

      if ($request->picture) {
        $image = $request->picture;
        $image = Str::after($image, 'base64,');
        $image = str_replace(' ', '+', $image);
        $imageName = (string) Str::uuid() . '.' . 'jpg';
        File::put(storage_path() . "/app/public/producteurs/" . $imageName, base64_decode($image));
        $picture = "public/producteurs/$imageName";
        $producteur->picture = $picture;
      }
      if ($request->esignature) {

        $image = $request->esignature;
        $image = Str::after($image, 'base64,');
        $image = str_replace(' ', '+', $image);
        $imageName = (string) Str::uuid() . '.' . 'jpg';
        File::put(storage_path() . "/app/public/producteurs/" . $imageName, base64_decode($image));
        $esignature = "public/producteurs/$imageName";

        $producteur->esignature = $esignature;
      }
      $producteur->proprietaires = $request->proprietaires;
      $producteur->statutMatrimonial = $request->statutMatrimonial;
      $producteur->programme_id = $request->programme_id;
      $producteur->localite_id = $request->localite_id;
      $producteur->habitationProducteur = $request->habitationProducteur;
      $producteur->autreMembre = $request->autreMembre;
      $producteur->autrePhone = $request->autrePhone;
      $producteur->numPiece = $request->numPiece;
      $producteur->num_ccc = $request->num_ccc;
      $producteur->carteCMU = $request->carteCMU;
      $producteur->carteCMUDispo = $request->carteCMUDispo;
      $producteur->typeCarteSecuriteSociale = $request->typeCarteSecuriteSociale;
      $producteur->numSecuriteSociale = $request->numSecuriteSociale;
      $producteur->numCMU = $request->numCMU;
      $producteur->anneeDemarrage = $request->anneeDemarrage;
      $producteur->anneeFin = $request->anneeFin;
      $producteur->autreCertificats = $request->autreCertificats;
      $producteur->consentement  = $request->consentement;
      $producteur->statut  = $request->statut;
      $producteur->certificat     = $request->certificat;
      $producteur->nom = $request->nom;
      $producteur->prenoms    = $request->prenoms;
      $producteur->sexe    = $request->sexe;
      $producteur->nationalite    = $request->nationalite;
      $producteur->dateNaiss    = $request->dateNaiss;
      $producteur->phone1    = $request->phone1;
      $producteur->phone2    = $request->phone2;
      $producteur->niveau_etude    = $request->niveau_etude;
      $producteur->type_piece    = $request->type_piece;
      $producteur->numPiece    = $request->numPiece;
      $producteur->codeProd = $request->codeProd;
      $producteur->plantePartage = $request->plantePartage;
      $producteur->numeroAssocie = $request->numeroAssocie;
      $producteur->userid = $request->userid;
      if ($producteur->codeProdapp == null) {
        $coop = DB::table('localites as l')->join('cooperatives as c', 'l.cooperative_id', '=', 'c.id')->where('l.id', $request->localite)->select('c.codeApp')->first();
        if ($coop != null) {
          $producteur->codeProdapp = $this->generecodeProdApp($request->nom, $request->prenoms, $coop->codeApp);
        } else {
          $producteur->codeProdapp = null;
        }
      }
      $data = $request->all();
      unset($data['certificats']);
      $producteur->update($data);
      if ($producteur != null) {
        $id = $producteur->id;
        $datas  = $data2 = [];
        if (($request->certificats != null)) {
          Producteur_certification::where('producteur_id', $id)->delete();
          $i = 0;
          foreach ($request->certificats as $certificat) {
            if (!empty($certificat)) {
              $datas[] = [
                'producteur_id' => $id,
                'certification' => $certificat,
              ];
            }

            $i++;
          }
        }

        Producteur_certification::insert($datas);
      }

      $message = "Le producteur a été mis à jour avec succès";
    } else {
      $producteur = new Producteur();
      $producteur->proprietaires = $request->proprietaires;
      $producteur->statutMatrimonial = $request->statutMatrimonial;
      $producteur->programme_id = $request->programme_id;
      $producteur->localite_id = $request->localite_id;
      $producteur->habitationProducteur = $request->habitationProducteur;
      $producteur->autreMembre = $request->autreMembre;
      $producteur->autrePhone = $request->autrePhone;
      $producteur->numPiece = $request->numPiece;
      $producteur->num_ccc = $request->num_ccc;
      $producteur->carteCMU = $request->carteCMU;
      $producteur->carteCMUDispo = $request->carteCMUDispo;
      $producteur->typeCarteSecuriteSociale = $request->typeCarteSecuriteSociale;
      $producteur->numSecuriteSociale = $request->numSecuriteSociale;
      $producteur->numCMU = $request->numCMU;
      $producteur->anneeDemarrage = $request->anneeDemarrage;
      $producteur->anneeFin = $request->anneeFin;
      $producteur->autreCertificats = $request->autreCertificats;
      $producteur->consentement  = $request->consentement;
      $producteur->statut  = $request->statut;
      $producteur->certificat     = $request->certificat;
      $producteur->nom = $request->nom;
      $producteur->prenoms    = $request->prenoms;
      $producteur->sexe    = $request->sexe;
      $producteur->nationalite    = $request->nationalite;
      $producteur->dateNaiss    = $request->dateNaiss;
      $producteur->phone1    = $request->phone1;
      $producteur->phone2    = $request->phone2;
      $producteur->niveau_etude    = $request->niveau_etude;
      $producteur->type_piece    = $request->type_piece;
      $producteur->numPiece    = $request->numPiece;
      $producteur->userid = $request->userid;
      $producteur->codeProd = $request->codeProd;
      $producteur->plantePartage = $request->plantePartage;
      $producteur->userid = $request->userid;
      if (!file_exists(storage_path() . "/app/public/producteurs/pieces")) {
        File::makeDirectory(storage_path() . "/app/public/producteurs/pieces", 0777, true);
      }
      if ($request->picture) {
        $image = $request->picture;
        $image = Str::after($image, 'base64,');
        $image = str_replace(' ', '+', $image);
        $imageName = (string) Str::uuid() . '.' . 'jpg';
        File::put(storage_path() . "/app/public/producteurs/pieces/" . $imageName, base64_decode($image));
        $picture = "public/producteurs/pieces/$imageName";
        $producteur->picture = $picture;
      }
      if ($request->esignature) {

        $image = $request->esignature;
        $image = Str::after($image, 'base64,');
        $image = str_replace(' ', '+', $image);
        $imageName = (string) Str::uuid() . '.' . 'jpg';
        File::put(storage_path() . "/app/public/producteurs/pieces/" . $imageName, base64_decode($image));
        $esignature = "public/producteurs/pieces/$imageName";

        $producteur->esignature = $esignature;
      }
      $coop = DB::table('sections as s')->join('cooperatives as c', 's.cooperative_id', '=', 'c.id')->join('localites as l', 's.id', '=', 'l.section_id')->where('l.id', $request->localite_id)->select('c.codeApp')->first();
      if ($coop != null) {
        $producteur->codeProdapp = $this->generecodeProdApp($request->nom, $request->prenoms, $coop->codeApp);
      } else {
        $producteur->codeProdapp = null;
      }
      $producteur->save();

      if ($producteur != null) {
        $id = $producteur->id;
        if ($producteur != null) {
          $id = $producteur->id;
          $datas  = [];
          if ($request->certificats != null) {
            $certificats = array_filter($request->certificats);
            if (!empty($certificats)) {
              Producteur_certification::where('producteur_id', $id)->delete();
              foreach ($certificats as $certificat) {
                if ($certificat == 'Autre') {
                  $autre = new Certification();
                  $autre->nom = $request->autreCertificats;
                  $autre->save();
                  $certificat = $request->autreCertificats;
                }
                $datas[] = [
                  'producteur_id' => $id,
                  'certification' => $certificat,
                ];
              }
              Producteur_certification::insert($datas);
            }
          }
        }
      }
      $message = "Le producteur a été créé avec succès";
    }
    return response()->json($producteur, 201);
  }


  public function apiinfosproducteur(Request $request)
  {
    DB::beginTransaction();
    try {

      // $debug = new DebugMobile();
      // $debug->content = json_encode($request->all());
      // $debug->save();

      $producteur = Producteur::where('id', $request->producteur_id)->first();
      if ($producteur->status == Status::NO) {
        $notify = 'Ce producteur est désactivé';
        return response()->json($notify, 201);
      }
      if ($request->id) {
        $infoproducteur = Producteur_info::findOrFail($request->id);
        $message = "L'info du producteur a été mise à jour avec succès";
      } else {
        $infoproducteur = new Producteur_info();

        $hasInfoProd = Producteur_info::where('producteur_id', $request->producteur_id)->exists();

        if ($hasInfoProd) {
          $notify = "L'info existe déjà pour ce producteur. Veuillez apporter des mises à jour.";
          return response()->json([
            'message' => $notify,
          ], 301);
        }
      }
      $infoproducteur->producteur_id = $request->producteur_id;
      $infoproducteur->foretsjachere  = $request->foretsjachere;
      $infoproducteur->superficie  = $request->superficie;
      $infoproducteur->autresCultures = $request->autresCultures;
      $infoproducteur->autreActivite = $request->autreActivite;
      $infoproducteur->travailleurs = $request->travailleurs;
      $infoproducteur->travailleurspermanents = $request->travailleurspermanents;
      $infoproducteur->travailleurstemporaires = $request->travailleurstemporaires;
      $infoproducteur->mobileMoney = $request->mobileMoney;
      $infoproducteur->compteBanque    = $request->compteBanque;
      $infoproducteur->nomBanque    = $request->nomBanque;
      $infoproducteur->mainOeuvreFamilial = $request->mainOeuvreFamilial;
      $infoproducteur->travailleurFamilial    = $request->travailleurFamilial;
      $infoproducteur->societeTravail = $request->societeTravail;
      $infoproducteur->nombrePersonne = $request->nombrePersonne;
      $infoproducteur->userid = $request->userid;
      $infoproducteur->save();
      if ($infoproducteur != null) {
        $id = $infoproducteur->id;
        if (($request->typeculture != null)) {

          $verification   = Producteur_infos_typeculture::where('producteur_info_id', $id)->get();
          if ($verification->count()) {
            DB::table('producteur_infos_typecultures')->where('producteur_info_id', $id)->delete();
          }
          $i = 0;

          foreach ($request->typeculture as $data) {
            if ($data != null) {
              DB::table('producteur_infos_typecultures')->insert(['producteur_info_id' => $id, 'typeculture' => $data, 'superficieculture' => $request->superficieculture[$i]]);
            }
            $i++;
          }
        }
        if ($request->typeactivite != null) {
          $verification   = Producteur_infos_autresactivite::where('producteur_info_id', $id)->get();
          if ($verification->count()) {
            DB::table('producteur_infos_autresactivites')->where('producteur_info_id', $id)->delete();
          }
          $i = 0;
          foreach ($request->typeactivite as $data) {
            if ($data != null) {
              DB::table('producteur_infos_autresactivites')->insert(['producteur_info_id' => $id, 'typeactivite' => $data]);
            }
            $i++;
          }
        }
        if ($request->operateurMM != null && $request->numeros != null) {
          $verification   = Producteur_infos_mobile::where('producteur_info_id', $id)->get();
          if ($verification->count()) {
            DB::table('producteur_infos_mobiles')->where('producteur_info_id', $id)->delete();
          }
          $i = 0;
          foreach ($request->operateurMM as $data) {
            if ($data != null) {
              DB::table('producteur_infos_mobiles')->insert(['producteur_info_id' => $id, 'operateur' => $data, 'numero' => $request->numeros[$i]]);
            }
            $i++;
          }
        }
      }
    } catch (ValidationException $e) {
      DB::rollBack();
    }

    DB::commit();
    return response()->json($infoproducteur, 201);
  }

  public function getproducteurUpdate(Request $request)
  {
    $input = $request->all();
    if ($request->userid) {
      $userid = $input['userid'];


      $producteur = DB::select(DB::raw("SELECT * FROM producteurs WHERE (localite_id is null or  
    nationalite_id is null or  
    type_piece_id is null or 
    codeProd is null or  
    picture is null or 
    nom is null or 
    prenoms is null or 
    sexe is null or 
    dateNaiss is null or 
    phone1 is null or 
    numPiece is null or  
    niveaux_id is null or  
    picture is null or 
    copiecarterecto is null or 
    copiecarteverso is null or 
    consentement is null or 
    statut is null or 
    certificat is null or 
    esignature is null 
  )
    AND deleted_at IS NULL
    "));

      if (isset($input['id'])) {
        if ($request->picture) {
          $image = $request->picture;
          $image = Str::after($image, 'base64,');
          $image = str_replace(' ', '+', $image);
          $imageName = (string) Str::uuid() . '.' . 'jpg';
          File::put(storage_path() . "/app/public/producteurs/" . $imageName, base64_decode($image));
          $picture = "public/producteurs/$imageName";
          $input['picture'] = $picture;
        }
        if ($request->esignature) {

          $image = $request->esignature;
          $image = Str::after($image, 'base64,');
          $image = str_replace(' ', '+', $image);
          $imageName = (string) Str::uuid() . '.' . 'jpg';
          File::put(storage_path() . "/app/public/producteurs/pieces/" . $imageName, base64_decode($image));
          $esignature = "public/producteurs/pieces/$imageName";

          $input['esignature'] = $esignature;
        }
        $producteur = Producteur::find($input['id']);
        $producteur->update($input);
        if ($producteur != null) {
          $id = $producteur->id;
          if ($producteur != null) {
            $id = $producteur->id;
            $datas  = [];
            if ($request->certificats != null) {
              $certificats = array_filter($request->certificats);
              if (!empty($certificats)) {
                Producteur_certification::where('producteur_id', $id)->delete();
                foreach ($certificats as $certificat) {
                  if ($certificat == 'Autre') {
                    $autre = new Certification();
                    $autre->nom = $request->autreCertificats;
                    $autre->save();
                    $certificat = $request->autreCertificats;
                  }
                  $datas[] = [
                    'producteur_id' => $id,
                    'certification' => $certificat,
                  ];
                }
                Producteur_certification::insert($datas);
              }
            }
          }
        }

        $producteur = Producteur::find($input['id']);
      }
    } else {
      $producteur = array();
    }

    return response()->json($producteur, 201);
  }

  private function generecodeProdApp($nom, $prenoms, $codeApp)
  {
    $action = 'non';

    $data = Producteur::select('codeProdapp')->join('localites as l', 'producteurs.localite_id', '=', 'l.id')->join('sections as s', 'l.section_id', '=', 's.id')->join('cooperatives as c', 's.cooperative_id', '=', 'c.id')->where([
      ['codeProdapp', '!=', null], ['codeApp', $codeApp]
    ])->orderby('producteurs.id', 'desc')->first();

    if ($data != null) {

      $code = $data->codeProdapp;
      if ($code != null) {
        $chaine_number = Str::afterLast($code, '-');
      } else {
        $chaine_number = 0;
      }
    } else {
      $chaine_number = 0;
    }

    $lastCode = $chaine_number + 1;
    $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;

    do {

      $verif = Producteur::select('codeProdapp')->where('codeProdapp', $codeP)->orderby('id', 'desc')->first();
      if ($verif == null) {
        $action = 'non';
      } else {
        $action = 'oui';
        $code = $codeP;
        $chaine_number = Str::afterLast($code, '-');
        $lastCode = $chaine_number + 1;
        $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;
      }
    } while ($action != 'non');

    return $codeP;
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

    dd(Producteur::find($id));
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
