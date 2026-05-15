@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        @if ($producteur->consentement)
                            <tr>
                                <td>Accord de consentement du producteur
                                </td>
                                <td>
                                    {{ @$producteur->consentement }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->proprietaires)
                            <tr>
                                <td>Comment vous vous definissez ?
                                </td>
                                <td>
                                    {{ @$producteur->proprietaires }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->plantePartage)
                            <tr>
                                <td>votre associé</td>
                                <td>
                                    {{ @$producteur->plantePartage }}

                                </td>
                            </tr>
                            <tr>
                                <td>Numero de votre associé</td>
                                <td>
                                    {{ @$producteur->numeroAssocie }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->anneeDemarrage)
                            <tr>
                                <td>Année de démarrage
                                </td>
                                <td>
                                    {{ @$producteur->anneeDemarrage }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->anneeFin)
                            <tr>
                                <td>Année de fin
                                </td>
                                <td>
                                    {{ @$producteur->anneeFin }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->statut)
                            <tr>
                                <td>Statut
                                </td>
                                <td>
                                    {{ @$producteur->statut }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->certificat)
                            <tr>
                                <td>Année de certification
                                </td>
                                <td>
                                    {{ @$producteur->certificat }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->codeProd)
                            <tr>
                                <td>Code producteur
                                </td>
                                <td>
                                    {{ @$producteur->codeProd }}

                                </td>
                            </tr>
                        @endif
                        @if (count($producteur->certifications))
                            <tr>
                                <td>Certifications

                                </td>
                                <td>
                                    @forelse($producteur->certifications as $data)
                                        <span class="badge badge-info">{{ @$data->certification }} </span> <br>
                                    @empty
                                        Aucune
                                    @endforelse
                                </td>
                        @endif
                        @if ($producteur->localite_id)
                            <tr>
                                <td>
                                    Section
                                </td>

                                <td>
                                    {{ @$producteur->localite->section->libelle }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Localite
                                </td>
                                <td>
                                    {{ @$producteur->localite->nom }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->programme_id)
                            <tr>
                                <td>
                                    Programme
                                </td>
                                <td>
                                    {{ @$producteur->programme->libelle }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->habitationProducteur)
                            <tr>
                                <td>Habitez-vous dans un campement ou village ?
                                </td>
                                <td>
                                    {{ @$producteur->habitationProducteur }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->nom)
                            <tr>
                                <td>Nom du producteur
                                </td>
                                <td>
                                    {{ @stripslashes($producteur->nom) }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->prenoms)
                            <tr>
                                <td>Prenoms du producteur
                                </td>
                                <td>
                                    {{ @stripslashes($producteur->prenoms) }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->sexe)
                            <tr>
                                <td>Genre
                                </td>
                                <td>
                                    {{ @$producteur->sexe }}
                                </td>
                        @endif
                        @if ($producteur->statutMatrimonial)
                            </tr>

                            <tr>
                                <td>Statut matrimonial
                                </td>
                                <td>
                                    {{ @$producteur->statutMatrimonial }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->nationalite)
                            <tr>
                                <td>Nationalité
                                </td>
                                <td>
                                    {{ @$producteur->country->nationalite }}
                                    </select>
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->dateNaiss)
                            <tr>
                                <td>Date de naissance
                                </td>
                                <td>
                                    {{ @$producteur->dateNaiss }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->phone1)
                            <tr>
                                <td>Numero de téléphone
                                </td>
                                <td>
                                    {{ @$producteur->phone1 }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->phone2)
                            <tr>
                                <td>Numero de téléphone
                                </td>
                                <td>
                                    {{ @$producteur->phone2 }}

                                </td>
                            </tr>
                        @endif
                        @if ($producteur->autreMembre)
                            <tr>
                                <td>Avez-vous un proche à contacter pour vous joindre
                                </td>
                                <td>
                                    {{ @$producteur->autreMembre }} </td>
                            </tr>
                        @endif
                        @if ($producteur->autrePhone)
                            <tr>
                                <td>
                                </td>
                                <td>
                                    {{ @$producteur->autrePhone }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->niveau_etude)
                            <tr>
                                <td> Niveau d'étude
                                </td>
                                <td>
                                    {{ @$producteur->niveau_etude }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->type_piece)
                            <tr>
                                <td>Type de pièces
                                </td>
                                <td>
                                    {{ @$producteur->type_piece }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->numPiece)
                            <tr>
                                <td>N° de la pièce
                                </td>
                                <td>
                                    {{ @$producteur->numPiece }} </td>
                            </tr>
                        @endif
                        @if ($producteur->num_ccc)
                            <tr>
                                <td>N° de carte CCC
                                </td>
                                <td>
                                    {{ @$producteur->num_ccc }} </td>
                            </tr>
                        @endif
                        @if ($producteur->carteCMU)
                            <tr>
                                <td>Avez-vous une carte CMU ?
                                </td>
                                <td>
                                    {{ @$producteur->carteCMU }} </td>
                            </tr>
                        @endif
                        @if ($producteur->numCMU)
                            <tr>
                                <td>N° de la pièce CMU
                                </td>
                                <td>
                                    {{ @$producteur->numCMU }} </td>
                            </tr>
                        @endif
                        @if ($producteur->carteCMUDispo)
                            <tr>
                                <td>Carte CMU disponible
                                </td>
                                <td>
                                    {{ @$producteur->carteCMUDispo }} </td>
                            </tr>
                        @endif
                        @if ($producteur->numCMU)
                            <tr>
                                <td>N° De La Pièce CMU</td>
                                <td>
                                    {{ @$producteur->numCMU }} </td>
                            </tr>
                        @endif

                        @if ($producteur->typeCarteSecuriteSociale)
                            <tr>
                                <td>Votre type de carte de sécurité social
                                </td>
                                <td>
                                    {{ @$producteur->typeCarteSecuriteSociale }}
                                </td>
                            </tr>
                        @endif
                        @if ($producteur->numSecuriteSociale)
                            <tr>
                                <td>N° de carte de sécurité sociale
                                </td>
                                <td>

                                    {{ @$producteur->numSecuriteSociale }}

                                </td>
                            </tr>
                        @endif

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.producteur.index') }}" />
@endpush
