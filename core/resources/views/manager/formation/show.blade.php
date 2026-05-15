@extends('manager.layouts.app')
@section('panel')
    <?php use Carbon\Carbon; ?>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>Localité</td>
                            <td>
                                {{ @$localite->nom }}

                            </td>
                        </tr>

                        <tr>
                            <td>Producteurs </td>
                            <td style="white-space: pre-wrap;">
                                {{ $producteurs->map(function ($producteur) {return stripslashes($producteur->nom) . ' ' . stripslashes($producteur->prenoms);})->implode(', ') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Type de formation</td>
                            <td>
                                {{ @$formation->formation_type }}

                            </td>
                        </tr>
                        <tr>
                            <td>Lieu de formation</td>
                            <td>
                                {{ @$formation->lieu_formation }}
                            </td>
                        </tr>

                        <tr>
                            <td>Modules de formation</td>
                            <td style="white-space: pre-wrap;">
                                {{ $typeformations->map(function ($module) {return $module->nom;})->implode(', ') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Thèmes de formation</td>
                            <td style="white-space: pre-wrap;">
                                {{ $themes->map(function ($theme) {return $theme->nom;})->implode(', ') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Sous thèmes de la formation</td>
                            <td style="white-space: pre-wrap;">
                                {{ $sousThemes->map(function ($soustheme) {return $soustheme->nom;})->implode(', ') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Staff ayant dispensé la formation</td>
                            <td style="white-space: pre-wrap;">
                                {{ $staffs->map(function ($staff) {return $staff->lastname . ' ' . $staff->firstname;})->implode(', ') }}
                            </td>
                        </tr>
                        <tr>
                            @if($formation->date_debut_formation != null && $formation->date_fin_formation != null)
                            <td>Date de début de la formation</td>
                            <td>
                                {{ Carbon::parse($formation->date_debut_formation)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Date de fin de la formation</td>
                            <td>
                                {{ Carbon::parse($formation->date_fin_formation)->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td>Durée de la formation</td>
                            <td>
                                {{ $formation->duree_formation }} heures
                                </td>
                        </tr>
                        <tr>
                            <td>Observation</td>
                            <td>
                                {{ $formation->observation_formation }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.formation.index') }}" />
@endpush

@push('script')
@endpush
