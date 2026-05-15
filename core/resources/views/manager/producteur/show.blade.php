@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>Avez-vous des forets ou jachère ?</td>
                            <td>
                                {{ @$infosproducteur->foretsjachere }}

                            </td>
                        </tr>

                        <tr>
                            <td>Superficie</td>
                            <td>{{ @$infosproducteur->superficie }}</td>
                        </tr>
                        <tr>
                            <td>Avez-vous D’autres Cultures En Dehors Du Cacao?</td>
                            <td>{{ @$infosproducteur->autresCultures }}</td>
                        </tr>
                        @if ($infosproducteur->typesculture)
                            <tr>
                                <td>Type de culture</td>
                                <td>
                                    {{ implode(', ', $infosproducteur->typesculture->pluck('typeculture')->toArray()) }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>Avez-vous d’autres activités en dehors des cultures?</td>
                            <td>
                                {{ @$infosproducteur->autreActivite }}

                            </td>
                        </tr>
                        @if ($infosproducteur->autresactivites)
                            <tr>
                                <td>Activités</td>
                                <td>
                                    {{ implode(', ', $infosproducteur->autresactivites->pluck('typeactivite')->toArray()) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Avez-vous recours à une main d'œuvre familiale ?</td>
                            <td>
                                {{ @$infosproducteur->mainOeuvreFamilial }}
                            </td>
                        </tr>
                        @if ($infosproducteur->mainOeuvreFamilial == 'oui')
                            <tr>
                                <td>Combien De Personnes (de La Famille Travaillent)</td>
                                <td>
                                    {{ @$infosproducteur->travailleurFamilial }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Combien de travailleurs (rémunéré) avez-vous ?</td>
                            <td>
                                {{ @$infosproducteur->travailleurs }}
                            </td>
                        </tr>
                        <tr>
                            <td>Nombre de Travailleurs Permanents (plus de 12mois)</td>
                            <td>
                                {{ @$infosproducteur->travailleurspermanents }}
                            </td>
                        </tr>

                        <tr>
                            <td>Nombre de Travailleurs temporaires</td>
                            <td>
                                {{ @$infosproducteur->travailleurstemporaires }}
                            </td>
                        </tr>
                        <tr>
                            <td>Etes vous membre de société de travail ?</td>
                            <td>
                                {{ @$infosproducteur->societeTravail }}
                            </td>
                        </tr>
                        @if ($infosproducteur->societeTravail == 'oui')
                            <tr>
                                <td>Nombre De Personne</td>
                                <td>
                                    {{ @$infosproducteur->nombrePersonne }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>As-tu un Compte Mobile Money ?</td>
                            <td>{{ @$infosproducteur->mobileMoney }}</td>
                        </tr>
                        @if ($infosproducteur->mobileMoney == 'oui')
                            @foreach($infosproducteur->mobiles as $mobile)
                                <tr>
                                    <td>Compte {{ @$mobile->operateur }}</td>
                                    <td>
                                        {{ @$mobile->numero }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td>As-tu un compte bancaire (dans une banque) ?</td>
                            <td>
                                {{ @$infosproducteur->compteBanque }}
                            </td>
                        </tr>
                        @if ($infosproducteur->compteBanque == 'oui')
                            <tr>
                                <td>Nom de la banque</td>
                                <td>
                                    {{ @$infosproducteur->nomBanque }}
                                </td>
                            </tr>
                            @if ($infosproducteur->nomBanque == 'Autre')
                                <tr>
                                    <td>Nom de la banque</td>
                                    <td>
                                        {{ @$infosproducteur->autreBanque }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.producteur.infos', encrypt($infosproducteur->producteur_id)) }}" />
@endpush

@push('script')
@endpush
