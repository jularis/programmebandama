@extends('manager.layouts.app')
@section('panel')
<?php use Carbon\Carbon; ?>
<div class="row mb-none-30">

    {{-- En-tête --}}
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">
                        {{ $formation->cooperative->name ?? '—' }}
                        @php echo $formation->statusBadge; @endphp
                    </h5>
                    @if($formation->campagne)
                        <small class="text-muted"><i class="las la-calendar"></i> Campagne : {{ $formation->campagne->nom ?? $formation->campagne->id }}</small>
                    @endif
                </div>
                <a href="{{ route('manager.formation-staff.edit', $formation->id) }}" class="btn btn--primary h-45">
                    <i class="la la-pen"></i> Modifier
                </a>
            </div>
        </div>
    </div>

    {{-- Informations générales --}}
    <div class="col-lg-6 mb-30">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="las la-info-circle"></i> Informations générales</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="45%">Lieu de formation</th>
                            <td>{{ $formation->lieu_formation ?? '—' }}</td>
                        </tr>
                        @if($formation->date_debut_formation && $formation->date_fin_formation)
                        <tr>
                            <th>Date de début</th>
                            <td>{{ Carbon::parse($formation->date_debut_formation)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date de fin</th>
                            <td>{{ Carbon::parse($formation->date_fin_formation)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Durée</th>
                            <td>{{ $formation->duree_formation ?? '—' }} h</td>
                        </tr>
                        <tr>
                            <th>Enregistrée le</th>
                            <td>{{ Carbon::parse($formation->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Formateurs --}}
    <div class="col-lg-6 mb-30">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="las la-chalkboard-teacher"></i> Formateur(s)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="40%">Entreprise(s)</th>
                            <td>
                                @forelse($formation->entreprises->unique('nom_entreprise') as $e)
                                    {{ $e->nom_entreprise }}@if(!$loop->last), @endif
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                        <tr>
                            <th>Formateur(s)</th>
                            <td>
                                @forelse($formation->formateurs as $f)
                                    {{ $f->nom_formateur }} {{ $f->prenom_formateur }}@if(!$loop->last), @endif
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Contenu pédagogique --}}
    <div class="col-lg-6 mb-30">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="las la-book"></i> Contenu pédagogique</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="35%">Modules</th>
                            <td>
                                @forelse($modules as $m)
                                    <span class="badge badge--success">{{ $m->nom }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                        <tr>
                            <th>Thèmes</th>
                            <td>
                                @forelse($themes as $t)
                                    <span class="badge badge--info">{{ $t->nom }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Participants staff --}}
    <div class="col-lg-6 mb-30">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="las la-users"></i> Participants (Staff)</h5>
                <span class="badge badge--primary">{{ $staffsListe->count() }}</span>
            </div>
            <div class="card-body" style="max-height:300px; overflow-y:auto;">
                @if($staffsListe->isNotEmpty())
                    <ol class="mb-0 ps-3">
                        @foreach($staffsListe as $item)
                            <li>
                                @if($item->user)
                                    {{ $item->user->lastname }} {{ $item->user->firstname }}
                                @else
                                    <span class="text-muted">Utilisateur supprimé</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-muted mb-0">Aucun participant enregistré.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Visiteurs --}}
    @if($visiteurs->isNotEmpty())
    <div class="col-lg-6 mb-30">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="las la-user-friends"></i> Visiteurs</h5>
                <span class="badge badge--info">{{ $visiteurs->count() }}</span>
            </div>
            <div class="card-body" style="max-height:300px; overflow-y:auto;">
                <ol class="mb-0 ps-3">
                    @foreach($visiteurs as $v)
                        <li>{{ $v->visiteur }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
    @endif

    {{-- Observation --}}
    @if($formation->observation_formation)
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="las la-comment-alt"></i> Observation</h5>
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $formation->observation_formation }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Documents --}}
    @if($formation->photo_formation || $formation->rapport_formation)
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="las la-file-alt"></i> Documents</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($formation->photo_formation)
                    <div class="col-md-4 col-sm-6 text-center">
                        <p class="text-muted small mb-1">Photo de la formation</p>
                        <a href="{{ asset('core/storage/app/' . $formation->photo_formation) }}" target="_blank">
                            <img src="{{ asset('core/storage/app/' . $formation->photo_formation) }}"
                                 alt="Photo formation" class="img-fluid rounded" style="max-height:160px; object-fit:cover;">
                        </a>
                    </div>
                    @endif
                    @if($formation->rapport_formation)
                    <div class="col-md-4 col-sm-6 text-center">
                        <p class="text-muted small mb-1">Rapport de formation</p>
                        <a href="{{ asset('core/storage/app/' . $formation->rapport_formation) }}" target="_blank"
                           class="btn btn-outline--success btn-sm">
                            <i class="las la-download"></i> Télécharger
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.formation-staff.index') }}" />
@endpush
