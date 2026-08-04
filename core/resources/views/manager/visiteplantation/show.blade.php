@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Identification</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Pourquoi cet interview ?</th><td>{{ $visitePlantation->raisonInterview }}</td></tr>
                        <tr><th>Type d'enquête</th><td>{{ $visitePlantation->typeEnquete }}</td></tr>
                        <tr><th>Producteur/rice</th><td>{{ stripslashes(@$visitePlantation->producteur->nom) }} {{ stripslashes(@$visitePlantation->producteur->prenoms) }} ({{ $visitePlantation->sexeProducteur }} - {{ $visitePlantation->codeProducteur }})</td></tr>
                        <tr><th>Localité</th><td>{{ @$visitePlantation->localite->nom }}</td></tr>
                        <tr><th>Date de l'enquête</th><td>{{ showDateTime($visitePlantation->dateEnquete) }}</td></tr>
                        <tr><th>Enquêteur/trice</th><td>{{ $visitePlantation->nomEnqueteur }}</td></tr>
                        <tr><th>Jour de cette visite plantation</th><td>{{ $visitePlantation->jourVisite ?? 'Non renseigné' }}</td></tr>
                        <tr><th>Localisation GPS</th><td>{{ $visitePlantation->latitude }}, {{ $visitePlantation->longitude }}</td></tr>
                        <tr><th>Statut fin</th><td>{{ $visitePlantation->statutFin }}</td></tr>
                        <tr><th>État</th><td>{{ $visitePlantation->etatSoumission }}</td></tr>
                    </table>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Filtre / Consentement</h5></legend>
                    <table class="table table-borderless">
                        <tr><th style="width:35%">Répondant = producteur/rice ?</th><td>{{ $visitePlantation->estProducteurRepondant }}</td></tr>
                        @if($visitePlantation->estProducteurRepondant == 'Non')
                            <tr><th>Nom du répondant</th><td>{{ $visitePlantation->nomRepondant }}</td></tr>
                            <tr><th>Titre du répondant</th><td>{{ $visitePlantation->titreRepondant }}</td></tr>
                        @endif
                        <tr><th>Producteur/rice disponible ?</th><td>{{ $visitePlantation->producteurDisponible }}</td></tr>
                        @if($visitePlantation->producteurDisponible == 'Non')
                            <tr><th>Raison d'indisponibilité</th><td>{{ $visitePlantation->raisonIndisponibilite }}</td></tr>
                            <tr><th>Date de replanification</th><td>{{ $visitePlantation->datePlanification }}</td></tr>
                            <tr><th>Raison(s) de refus</th><td>{{ $visitePlantation->raisonsRefus->pluck('valeur')->implode(', ') }}</td></tr>
                            <tr><th>Autre raison de refus</th><td>{{ $visitePlantation->autreRaisonRefus }}</td></tr>
                        @endif
                        <tr><th>Consentement</th><td>{{ $visitePlantation->consentement }}</td></tr>
                    </table>

                    @if($visitePlantation->producteurDisponible == 'Oui' && $visitePlantation->consentement == 'Oui')
                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Caractéristiques de la plantation</h5></legend>
                        <table class="table table-borderless">
                            <tr><th style="width:35%">Superficie de la plantation</th><td>{{ $visitePlantation->superficiePlantation }}</td></tr>
                            <tr><th>Manœuvres permanents</th><td>{{ $visitePlantation->nombreManoeuvresPermanents }}</td></tr>
                            <tr><th>Manœuvres permanents de moins de 18 ans ?</th><td>{{ $visitePlantation->manoeuvresPermanentsMoins18 }}</td></tr>
                            <tr><th>Manœuvres journaliers</th><td>{{ $visitePlantation->nombreManoeuvresJournaliers }}</td></tr>
                            <tr><th>Manœuvres journaliers de moins de 18 ans ?</th><td>{{ $visitePlantation->manoeuvresJournaliersMoins18 }}</td></tr>
                            <tr><th>Nombre d'enfants 0-4 ans</th><td>{{ $visitePlantation->nombreEnfants0a4 }}</td></tr>
                            <tr><th>Nombre d'enfants 5-17 ans</th><td>{{ $visitePlantation->nombreEnfants5a17 }}</td></tr>
                            <tr><th>Personnes trouvées lors de la visite</th><td>{{ $visitePlantation->nombrePersonnesTrouvees }}</td></tr>
                            <tr><th>Enfants trouvés dans la plantation</th><td>{{ $visitePlantation->nombreEnfantsTrouves }}</td></tr>
                        </table>

                        @foreach ($visitePlantation->enfants as $i => $enfant)
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.visiteplantation.index') }}" />
@endpush
