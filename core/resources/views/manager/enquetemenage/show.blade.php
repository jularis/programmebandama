@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Identification</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Pourquoi cet interview ?</th><td>{{ $enqueteMenage->raisonInterview }}</td></tr>
                        <tr><th>Type d'enquête</th><td>{{ $enqueteMenage->typeEnquete }}</td></tr>
                        <tr><th style="width:35%">Producteur/rice</th><td>{{ stripslashes(@$enqueteMenage->producteur->nom) }} {{ stripslashes(@$enqueteMenage->producteur->prenoms) }} ({{ $enqueteMenage->sexeProducteur }} - {{ $enqueteMenage->codeProducteur }})</td></tr>
                        <tr><th>Localité</th><td>{{ @$enqueteMenage->localite->nom }}</td></tr>
                        <tr><th>Date de l'enquête</th><td>{{ showDateTime($enqueteMenage->dateEnquete) }}</td></tr>
                        <tr><th>Enquêteur/trice</th><td>{{ $enqueteMenage->nomEnqueteur }}</td></tr>
                        <tr><th>Nombre d'enfants enquêtés</th><td>{{ $enqueteMenage->nombreEnfantsEnquetes }}</td></tr>
                        <tr><th>Localisation GPS</th><td>{{ $enqueteMenage->latitude }}, {{ $enqueteMenage->longitude }}</td></tr>
                        <tr><th>Statut fin</th><td>{{ $enqueteMenage->statutFin }}</td></tr>
                        <tr><th>État</th><td>{{ $enqueteMenage->etatSoumission }}</td></tr>
                    </table>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Filtre / Consentement</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Répondant = producteur/rice ?</th><td>{{ $enqueteMenage->estProducteurRepondant }}</td></tr>
                        @if($enqueteMenage->estProducteurRepondant == 'Non')
                            <tr><th>Nom du répondant</th><td>{{ $enqueteMenage->nomRepondant }}</td></tr>
                            <tr><th>Titre du répondant</th><td>{{ $enqueteMenage->titreRepondant }}</td></tr>
                        @endif
                        <tr><th>Producteur/rice disponible ?</th><td>{{ $enqueteMenage->producteurDisponible }}</td></tr>
                        @if($enqueteMenage->producteurDisponible == 'Non')
                            <tr><th>Raison d'indisponibilité</th><td>{{ $enqueteMenage->raisonIndisponibilite }}</td></tr>
                            <tr><th>Date de replanification</th><td>{{ $enqueteMenage->datePlanification }}</td></tr>
                            <tr><th>Raison(s) de refus</th><td>{{ $enqueteMenage->raisonsRefus->pluck('valeur')->implode(', ') }}</td></tr>
                            <tr><th>Autre raison de refus</th><td>{{ $enqueteMenage->autreRaisonRefus }}</td></tr>
                        @endif
                        <tr><th>Consentement</th><td>{{ $enqueteMenage->consentement }}</td></tr>
                    </table>

                    @if($enqueteMenage->producteurDisponible == 'Oui' && $enqueteMenage->consentement == 'Oui')
                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Caractéristiques du ménage</h5></legend>
                        <table class="table table-borderless">
                            <tr><th style="width:35%">Situation matrimoniale</th><td>{{ $enqueteMenage->situationMatrimoniale }}</td></tr>
                            <tr><th>Nombre d'adultes</th><td>{{ $enqueteMenage->nombreAdultes }}</td></tr>
                            <tr><th>Nombre d'enfants 0-4 ans</th><td>{{ $enqueteMenage->nombreEnfants0a4 }}</td></tr>
                            <tr><th>Nombre d'enfants 5-17 ans</th><td>{{ $enqueteMenage->nombreEnfants5a17 }}</td></tr>
                            <tr><th>Total personnes du ménage</th><td>{{ $enqueteMenage->totalPersonnesMenage }}</td></tr>
                            <tr><th>Enfant(s) à charge ?</th><td>{{ $enqueteMenage->aEnfantACharge }}</td></tr>
                            <tr><th>Nombre d'enfants à charge</th><td>{{ $enqueteMenage->nombreEnfantsACharge }}</td></tr>
                        </table>

                        @foreach ($enqueteMenage->enfants as $i => $enfant)
                            <div class="fieldset-like">
                                <legend class="legend-center"><h5 class="font-weight-bold">Enfant n°{{ $i + 1 }} — {{ $enfant->nom }}</h5></legend>
                                <table class="table table-borderless">
                                    <tr><th style="width:35%">Code enfant</th><td>{{ $enfant->codeEnfant }}</td></tr>
                                    <tr><th>Date de naissance</th><td>{{ $enfant->dateNaissance }}</td></tr>
                                    <tr><th>Sexe</th><td>{{ $enfant->sexe }}</td></tr>
                                    <tr><th>Lien de parenté</th><td>{{ $enfant->lienParente }} {{ $enfant->autreLienParente }}</td></tr>
                                    <tr><th>Raison si ne vit pas avec ses parents</th><td>{{ $enfant->raisonNeVitPasParents }} {{ $enfant->autreRaisonNeVitPasParents }}</td></tr>
                                    <tr><th>Situation scolaire</th><td>{{ $enfant->situationScolaire }}</td></tr>
                                    <tr><th>Niveau scolaire</th><td>{{ $enfant->niveauScolaire }}</td></tr>
                                    <tr><th>Raison(s) de non-scolarisation</th><td>{{ $enfant->raisonsNonScolarisation->pluck('valeur')->implode(', ') }} {{ $enfant->autreRaisonNonScolarisation }}</td></tr>
                                    <tr><th>Extrait de naissance ?</th><td>{{ $enfant->extraitNaissance }}</td></tr>
                                    <tr><th>Raison(s) si pas d'extrait</th><td>{{ $enfant->raisonsPasExtrait->pluck('valeur')->implode(', ') }}</td></tr>
                                    <tr><th>Situation(s) nécessitant un soutien</th><td>{{ $enfant->situationsPfte->pluck('valeur')->implode(', ') }}</td></tr>
                                    <tr><th>Raison(s) du travail/abus</th><td>{{ $enfant->raisonsTravailAbus->pluck('valeur')->implode(', ') }} {{ $enfant->autreRaisonTravailAbus }}</td></tr>
                                    <tr><th>Mesures - enfant</th><td>{{ $enfant->mesuresEnfant->pluck('valeur')->implode(', ') }}</td></tr>
                                    <tr><th>Mesures - ménage</th><td>{{ $enfant->mesuresMenage->pluck('valeur')->implode(', ') }}</td></tr>
                                    <tr><th>Mesures - communauté</th><td>{{ $enfant->mesuresCommunaute->pluck('valeur')->implode(', ') }}</td></tr>
                                    <tr><th>Autre mesure précisée</th><td>{{ $enfant->autreMesure }}</td></tr>
                                </table>
                            </div>
                        @endforeach

                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Sensibilisation de proximité du ménage</h5></legend>
                        <table class="table table-borderless">
                            <tr><th style="width:35%">Thème(s) abordé(s)</th><td>{{ $enqueteMenage->themes->pluck('valeur')->implode(', ') }} {{ $enqueteMenage->autreThemeSensibilisation }}</td></tr>
                            <tr><th>Outils utilisés</th><td>{{ $enqueteMenage->outils->pluck('valeur')->implode(', ') }}</td></tr>
                            <tr><th>Hommes sensibilisés</th><td>{{ $enqueteMenage->nombreHommesSensibilises }}</td></tr>
                            <tr><th>Femmes sensibilisées</th><td>{{ $enqueteMenage->nombreFemmesSensibilisees }}</td></tr>
                            <tr><th>Garçons sensibilisés</th><td>{{ $enqueteMenage->nombreGarconsSensibilises }}</td></tr>
                            <tr><th>Filles sensibilisées</th><td>{{ $enqueteMenage->nombreFillesSensibilisees }}</td></tr>
                            <tr><th>Total personnes sensibilisées</th><td>{{ $enqueteMenage->totalPersonnesSensibilisees }}</td></tr>
                            <tr><th>Téléphone</th><td>{{ $enqueteMenage->telephoneProducteurSensibilisation }}</td></tr>
                            <tr><th>Photo</th><td>
                                @if($enqueteMenage->photoSensibilisation)
                                    <a href="{{ asset(str_replace('public/', 'storage/', $enqueteMenage->photoSensibilisation)) }}" target="_blank">Voir la photo</a>
                                @endif
                            </td></tr>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.menage.index') }}" />
@endpush

@push('style')
    <style type="text/css">
        .fieldset-like {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: left;
        }

        .legend-center {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
@endpush
