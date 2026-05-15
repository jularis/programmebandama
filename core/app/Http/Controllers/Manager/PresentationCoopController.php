<?php

namespace App\Http\Controllers\Manager;

use App\Models\Parcelle;
use App\Models\Producteur;
use Illuminate\Http\Request;
use App\Models\CoopChiffreAffaire;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ChiffreAffairePartenaire;

class PresentationCoopController extends Controller
{
    public function index()
    {


        // $nombreProducteur = Producteur::whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();
        // $hommes = Producteur::where('sexe', 'Homme')->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $femmes = Producteur::where('sexe', 'Femme')->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursRainforest = Producteur::whereHas('certifications', function ($query) {
        //     $query->where('certification', 'RAINFOREST');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursRainforestHomme = Producteur::where('sexe', 'Homme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'RAINFOREST');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursRainforestFemme = Producteur::where('sexe', 'Femme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'RAINFOREST');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursFairtrade = Producteur::whereHas('certifications', function ($query) {
        //     $query->where('certification', 'FAIRTRADE');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursFairtradeHomme = Producteur::where('sexe', 'Homme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'FAIRTRADE');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursFairtradeFemme = Producteur::where('sexe', 'Femme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'FAIRTRADE');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursBio = Producteur::whereHas('certifications', function ($query) {
        //     $query->where('certification', 'BIO');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursBioHomme = Producteur::where('sexe', 'Homme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'BIO');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();

        // $countProducteursBioFemme = Producteur::where('sexe', 'Femme')->whereHas('certifications', function ($query) {
        //     $query->where('certification', 'BIO');
        // })->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Bandama');
        // })->count();


        // $nombreProducteurAutreProgramme = Producteur::whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Aucun programme');
        // })->count();
        // $hommesAutrePragramme = Producteur::where('sexe', 'Homme')->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Aucun programme');
        // })->count();

        // $femmesAutreProgramme = Producteur::where('sexe', 'Femme')->whereHas('programme', function ($query) {
        //     $query->where('libelle', 'Aucun programme');
        // })->count();


        // $sumSuperficie = Parcelle::join('producteurs', 'parcelles.producteur_id', '=', 'producteurs.id')
        // ->sum('parcelles.superficie');
        $pageTitle = "Présentation de la coopérative";
        return view('manager.presentation-coop.index', compact('pageTitle'));
    }

    public function create()
    {
        return view('manager.presentation-coop.create');
    }

    public function edit()
    {
        return view('manager.presentation-coop.edit');
    }
    public function store(Request $request)
    {
        $manager = auth()->user();
        if (DB::table('coop_chiffre_affaires')->where(['cooperative_id' => $manager->cooperative_id, 'annee' => $request->date])->exists()) {
            DB::table('coop_chiffre_affaires')->where(['cooperative_id' => $manager->cooperative_id, 'annee' => $request->date])->update(['montant' => $request->montant, 'annee' => $request->date]);
        } else {
            $coopChiffreAffaire = new CoopChiffreAffaire();
            $coopChiffreAffaire->cooperative_id = $manager->cooperative_id;
            $coopChiffreAffaire->montant = $request->montant;
            $coopChiffreAffaire->annee = $request->date;
            $coopChiffreAffaire->save();
        }
        return response()->json(['message' => 'Chiffre d\'affaires enregistré avec succès', "montant" => $coopChiffreAffaire->montant]);
    }
    public function chiffre_affaire_partenaire(Request $request)
    {
        $manager = auth()->user();
        if (DB::table('chiffre_affaire_partenaires')->where(['cooperative_id' => $manager->cooperative_id, 'annee' => $request->date])->exists()) {
            DB::table('chiffre_affaire_partenaires')->where(['cooperative_id' => $manager->cooperative_id, 'annee' => $request->date])->update(['montant' => $request->montant, 'annee' => $request->date]);
        } else {
            $chiffreAffairePartenaire = new ChiffreAffairePartenaire();
            $chiffreAffairePartenaire->cooperative_id = $manager->cooperative_id;
            $chiffreAffairePartenaire->montant = $request->montant;
            $chiffreAffairePartenaire->annee = $request->date;
            $chiffreAffairePartenaire->save();
        }
        return response()->json(['message' => 'Chiffre d\'affaires enregistré avec succès', "montant" => $chiffreAffairePartenaire->montant]);
        
    }
}
