@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Identification</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Pourquoi cet interview ?</th><td>{{ $suivi->raisonInterview }}</td></tr>
                        <tr><th>Date de l'enquête</th><td>{{ showDateTime($suivi->dateEnquete) }}</td></tr>
                        <tr><th>Enquêteur/trice</th><td>{{ $suivi->nomEnqueteur }}</td></tr>
                        <tr><th>Code enfant</th><td>{{ @$suivi->enfant->codeEnfant }}</td></tr>
                        <tr><th>Producteur/rice</th><td>{{ stripslashes(@$suivi->enfant->menage->producteur->nom) }} {{ stripslashes(@$suivi->enfant->menage->producteur->prenoms) }}</td></tr>
                        <tr><th>Localité</th><td>{{ @$suivi->enfant->menage->localite->nom }}</td></tr>
                        <tr><th>État</th><td>{{ $suivi->etatSoumission }}</td></tr>
                    </table>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Suivi depuis la dernière visite</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Action(s) de remédiation menée(s)</th><td>{{ $suivi->actionsRemediation->pluck('valeur')->implode(', ') }}</td></tr>
                    </table>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Réévaluation de la situation de l'enfant</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Nom</th><td>{{ $suivi->nom }}</td></tr>
                        <tr><th>Date de naissance</th><td>{{ $suivi->dateNaissance }}</td></tr>
                        <tr><th>Sexe</th><td>{{ $suivi->sexe }}</td></tr>
                        <tr><th>Lien de parenté</th><td>{{ $suivi->lienParente }} {{ $suivi->autreLienParente }}</td></tr>
                        <tr><th>Raison si ne vit pas avec ses parents</th><td>{{ $suivi->raisonNeVitPasParents }} {{ $suivi->autreRaisonNeVitPasParents }}</td></tr>
                        <tr><th>Situation scolaire</th><td>{{ $suivi->situationScolaire }}</td></tr>
                        <tr><th>Niveau scolaire</th><td>{{ $suivi->niveauScolaire }}</td></tr>
                        <tr><th>Raison(s) de non-scolarisation</th><td>{{ $suivi->raisonsNonScolarisation->pluck('valeur')->implode(', ') }} {{ $suivi->autreRaisonNonScolarisation }}</td></tr>
                        <tr><th>Extrait de naissance ?</th><td>{{ $suivi->extraitNaissance }}</td></tr>
                        <tr><th>Raison(s) si pas d'extrait</th><td>{{ $suivi->raisonsPasExtrait->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Situation(s) nécessitant un soutien</th><td>{{ $suivi->situationsPfte->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Raison(s) du travail/abus</th><td>{{ $suivi->raisonsTravailAbus->pluck('valeur')->implode(', ') }} {{ $suivi->autreRaisonTravailAbus }}</td></tr>
                        <tr><th>Mesures - enfant</th><td>{{ $suivi->mesuresEnfant->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Mesures - ménage</th><td>{{ $suivi->mesuresMenage->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Mesures - communauté</th><td>{{ $suivi->mesuresCommunaute->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Autre mesure précisée</th><td>{{ $suivi->autreMesure }}</td></tr>
                    </table>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Sensibilisation de proximité</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Thème(s) abordé(s)</th><td>{{ $suivi->themes->pluck('valeur')->implode(', ') }} {{ $suivi->autreThemeSensibilisation }}</td></tr>
                        <tr><th>Outils utilisés</th><td>{{ $suivi->outils->pluck('valeur')->implode(', ') }}</td></tr>
                        <tr><th>Hommes sensibilisés</th><td>{{ $suivi->nombreHommesSensibilises }}</td></tr>
                        <tr><th>Femmes sensibilisées</th><td>{{ $suivi->nombreFemmesSensibilisees }}</td></tr>
                        <tr><th>Garçons sensibilisés</th><td>{{ $suivi->nombreGarconsSensibilises }}</td></tr>
                        <tr><th>Filles sensibilisées</th><td>{{ $suivi->nombreFillesSensibilisees }}</td></tr>
                        <tr><th>Total personnes sensibilisées</th><td>{{ $suivi->totalPersonnesSensibilisees }}</td></tr>
                        <tr><th>Téléphone</th><td>{{ $suivi->telephoneProducteurSensibilisation }}</td></tr>
                        <tr><th>Photo</th><td>
                            @if($suivi->photoSensibilisation)
                                <a href="{{ asset(str_replace('public/', 'storage/', $suivi->photoSensibilisation)) }}" target="_blank">Voir la photo</a>
                            @endif
                        </td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.enfanttravailleur.index') }}" />
@endpush
