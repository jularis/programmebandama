@extends('layouts.app')
@section('title', @$pageTitle)
@section('content')
<?php use Illuminate\Support\Str; ?>
<section class="content-header">
      <h1>
        <i class="fa fa-users"></i> @lang('common.detail_producteur')<small>@lang('common.menu_producteur')</small>
      </h1>
    </section>
<section class="content">
<div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                <a class="btn btn-primary" href="{{ route('producteurs.index') }}"> @lang('common.retour')</a>
                </div>
            </div>
        </div>


        <div class="box">

        <div class="box-body" style="background: #ecf0f5;">


    @if($archivages)
        <div class="col-md-12">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-red">
              <h3 class="widget-user-username"  style="margin-left: 0px;">@lang('common.info_suivi')</h3>

              <!-- /.widget-user-image -->
            </div>
            <div class="box-footer no-padding">
              <ul class="nav nav-stacked">
                <li><a href="#"><strong>@lang('common.nom_cooppe')</strong> <span class="pull-right">{{ $archivages->nomCoop }}</span></a></li>
                <li><a href="#"><strong>@lang('common.localite')</strong><span class="pull-right">{{ $archivages->nomLocal }}</span></a></li>
                <li><a href="#"><strong>@lang('common.producteur_menage')</strong><span class="pull-right">{{ $archivages->nomProd }} {{ $archivages->prenoms }}</span></a></li>
                <li><a href="#"><strong>@lang('common.parcelle')</strong><span class="pull-right">{{ $archivages->codeParc }}</span></a></li>
                <li><a href="#"><strong>@lang('common.variete_cacaop')</strong><span class="pull-right">{{ $archivages->varietes_cacao_id }}</span></a></li>
                <li><a href="#"><strong>@lang('common.precisi')</strong><span class="pull-right">{{ $archivages->autreVariete }}</span></a></li>
                <li><a href="#"><strong>@lang('common.cours_eau')</strong><span class="pull-right">{{ $archivages->existeCoursEaux }}</span></a></li>
                <li><a href="#"><strong>@lang('common.type_courseau')</strong><span class="pull-right">{{ $archivages->cours_eaux_id }}</span></a></li>
                <li><a href="#"><strong>@lang('common.pente_parcelle')</strong><span class="pull-right">{{ $archivages->pente }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_arbreOmbrage') </strong><span class="pull-right">{{ $archivages->machine }}</span></a></li>
                <li><a href="#"><strong>@lang('common.varite_souhaite')</strong><span class="pull-right">{{ $archivages->varieteAbres }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_sauvageons') </strong><span class="pull-right">{{ $archivages->nombreSauvageons }}</span></a></li>
                <li><a href="#"><strong>@lang('common.activite_taille') </strong><span class="pull-right">{{ $archivages->activiteTaille }}</span></a></li>
                <li><a href="#"><strong>@lang('common.egourmandage_activite') </strong><span class="pull-right">{{ $archivages->activiteEgourmandage }}</span></a></li>
                <li><a href="#"><strong>@lang('common.desherbage_activite')</strong><span class="pull-right">{{ $archivages->activiteDesherbageManuel }}</span></a></li>
                <li><a href="#"><strong>@lang('common.recolte_sanitaire')</strong><span class="pull-right">{{ $archivages->activiteRecolteSanitaire }}</span></a></li>
                <li><a href="#"><strong>@lang('common.intrant')</strong><span class="pull-right">{{ $archivages->intrant }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_sac')</strong><span class="pull-right">{{ $archivages->nombresacs }}</span></a></li>
                <li><a href="#"><strong>@lang('common.presence_brume')</strong><span class="pull-right">{{ $archivages->presenceBioAgresseur }}</span></a></li>
                <li><a href="#"><strong>@lang('common.insecte_ravageur')</strong><span class="pull-right">{{ $archivages->presenceInsectesRavageurs }}</span></a></li>
                <li><a href="#"><strong>@lang('common.pre_fourmirouge')</strong><span class="pull-right">{{ $archivages->presenceFourmisRouge }}</span></a></li>
                <li><a href="#"><strong>@lang('common.pre_araigne')</strong><span class="pull-right">{{ $archivages->presenceAraignee }}</span></a></li>
                <li><a href="#"><strong>@lang('common.pre_verterre')</strong><span class="pull-right">{{ $archivages->presenceVerTerre }}</span></a></li>
                <li><a href="#"><strong>@lang('common.pre_menteRel')</strong><span class="pull-right">{{ $archivages->presenceMenteReligieuse }}</span></a></li>
                <li><a href="#"><strong>@lang('common.insect_utilise')</strong><span class="pull-right">{{ $archivages->nomInsecticide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_boite')</strong><span class="pull-right">{{ $archivages->nombreInsecticide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.fongicide_utilise')</strong><span class="pull-right">{{ $archivages->nomFongicide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_boite')</strong><span class="pull-right">{{ $archivages->nombreFongicide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.herbicide_utili')</strong><span class="pull-right">{{ $archivages->nomHerbicide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.nbre_boite')</strong><span class="pull-right">{{ $archivages->nombreHerbicide }}</span></a></li>
                <li><a href="#"><strong>@lang('common.desherbage_annee')</strong><span class="pull-right">{{ $archivages->nombreDesherbage }}</span></a></li>
                <li><a href="#"><strong>@lang('common.date_visite')</strong><span class="pull-right">{{ $archivages->dateVisite }}</span></a></li>

              </ul>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>

@endif



            </div>
            </div>
            </div>
            </div>
          </div>
      </div>
    </div>
</section>
@endsection
