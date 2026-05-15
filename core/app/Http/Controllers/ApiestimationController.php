<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApiestimationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {

    //

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
     
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {


    if ($request->id) {
      $estimation = Estimation::find($request->id);
    } else {
      $estimation = new Estimation();
    }
    $coefV1 = 1;
    $coefV2 = 0.6;
    $coefV3 = 0.2;

    $estimation->parcelle_id  = $request->parcelle;
    $estimation->campagne_id  = $request->campagne;
    $estimation->EA1  = $request->EA1;
    $estimation->EA2  = $request->EA2;
    $estimation->EA3  = $request->EA3;
    $estimation->EB1  = $request->EB1;
    $estimation->EB2  = $request->EB2;
    $estimation->EB3  = $request->EB3;
    $estimation->EC1  = $request->EC1;
    $estimation->EC2  = $request->EC2;
    $estimation->EC3  = $request->EC3;
    $superficie = Str::before($request->superficie, ' ');
    if (Str::contains($superficie, ",")) {
      $superficie = Str::replaceFirst(',', '.', $superficie);
      if (Str::contains($superficie, ",")) {
        $superficie = Str::replaceFirst('m²', '', $superficie);
      }
      $superficie = $superficie * 0.0001;
    }

    $supT = $superficie;

    if (isset($request->EA1) && isset($request->EB1) && isset($request->EC1)) {

      $T1 = $request->EA1 + $request->EB1 + $request->EC1;
    }
    if (isset($request->EA2) && isset($request->EB2) && isset($request->EC2)) {
      $T2 = $request->EA2 + $request->EB2 + $request->EC2;
    }
    if (isset($request->EA3) && isset($request->EB3) && isset($request->EC3)) {
      $T3 = $request->EA3 + $request->EB3 + $request->EC3;
    }
    //dd(isset($T1),isset($T2),isset($T3));

    if (isset($T1) && isset($T2) && isset($T3)) {
      $T1 = $T1 * $coefV1;
      $T2 = $T2 * $coefV2;
      $T3 = $T3 * $coefV3;
      $V1 = round($T1, 2);
      $V2 = round($T2, 2);
      $V3 = round($T3, 2);
    }
    if (isset($V1) && isset($V2) && isset($V3)) {
      $V1 = $V1 / 3;
      $V2 = $V2 / 3;
      $V3 = $V3 / 3;
      $VM1 = round($V1, 2);
      $VM2 = round($V2, 2);
      $VM3 = round($V3, 2);
    }
    if (isset($VM1) && isset($VM2) && isset($VM3)) {
      $VM1 = $VM1;
      $VM2 = $VM2;
      $VM3 = $VM3;
      $VT = $VM1 + $VM2 + $VM3;
      $Q = round($VT, 2);
    }

    if (isset($Q)) {
      $Q = $Q * 25;
      $RF = round($Q, 2);
    }
    if (isset($RF)) {
      $RF = $RF * $supT;
      $EsP = round($RF, 2);
    }
    $estimation->T1 = $T1;
    $estimation->T2 = $T2;
    $estimation->T3 = $T3;
    $estimation->V1 = $V1;
    $estimation->V2 = $V2;
    $estimation->V3 = $V3;
    $estimation->VM1    = $VM1;
    $estimation->VM2    = $VM2;
    $estimation->VM3    = $VM3;
    $estimation->Q    = $Q;
    $estimation->RF    = $RF;
    $estimation->EsP    = $EsP;
    $estimation->ajustement    = $request->ajustement;
    $estimation->typeEstimation    = $request->typeEstimation;
    $estimation->date_estimation    = $request->date_estimation;

    $estimation->save();

    if ($estimation == null) {
      return response()->json("L'estimation n'a pas été enregistré", 501);
    }

    return response()->json($estimation, 201);
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
