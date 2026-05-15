<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('manager.dashboard') }}" class="sidebar__main-logo"><img
                    src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
        </div>
        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('manager.dashboard') }}">
                    <a href="{{ route('manager.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Tableau de bord')</span>
                    </a>
                </li>

                @can('manager.staff.index')
                    <li class="sidebar-menu-item {{ menuActive('manager.staff.index') }}">
                        <a href="{{ route('manager.staff.index') }}" class="nav-link ">
                            <i class="menu-icon las la-user-friends"></i>
                            <span class="menu-title">@lang('Gestion des Staffs')</span>
                        </a>
                    </li>
                @endcan
                @if (Auth::user()->can('manager.traca.producteur.index') ||
                        Auth::user()->can('manager.traca.parcelle.index') ||
                        Auth::user()->can('manager.traca.estimation.index'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('manager.traca.*', 3) }}">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Gestion des Exploitations') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('manager.traca.*', 2) }} ">
                            <ul>
                                @can('manager.traca.producteur.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.traca.producteur.index') }}">
                                        <a href="{{ route('manager.traca.producteur.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Producteurs')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.traca.parcelle.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.traca.parcelle.index') }}">
                                        <a href="{{ route('manager.traca.parcelle.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Identifications Parcelles')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.traca.estimation.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.traca.estimation.index') }}">
                                        <a href="{{ route('manager.traca.estimation.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Estimations')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.suivi.parcelles.index') ||
                        Auth::user()->can('manager.suivi.formation.index') ||
                        Auth::user()->can('manager.suivi.inspection.index') ||
                        Auth::user()->can('manager.suivi.application.index'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('manager.suivi*', 3) }}">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Gestion Suivi Productivité') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('manager.suivi*', 2) }} ">
                            <ul>
                                @can('manager.suivi.parcelles.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.parcelles.index') }}">
                                        <a href="{{ route('manager.suivi.parcelles.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Suivis Parcelles')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.suivi.formation.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.formation.index') }}">
                                        <a href="{{ route('manager.suivi.formation.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Formations')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.suivi.inspection.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.inspection.index') }}">
                                        <a href="{{ route('manager.suivi.inspection.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Inspections')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.suivi.application.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.application.index') }}">
                                        <a href="{{ route('manager.suivi.application.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Applications Phytos')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.livraison.stock.section') ||
                        Auth::user()->can('manager.livraison.magcentral.stock') ||
                        Auth::user()->can('manager.livraison.usine.connaissement') ||
                        Auth::user()->can('manager.livraison.prime.producteur'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['manager.livraison.*', 'manager.livraison.magcentral.*'], 3) }}">
                            <i class="menu-icon las la-university"></i>
                            <span class="menu-title">@lang('Gestion de la Traçabilité') </span>
                        </a>
                        <div
                            class="sidebar-submenu {{ menuActive(['manager.livraison.*', 'manager.livraison.magcentral.*'], 2) }} ">
                            <ul>
                                <!-- <
                                class="sidebar-menu-item {{ menuActive(['manager.livraison.index']) }}">
                                <a href="{{ route('manager.livraison.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Livraison Magasins de Section')</span>
                                </a>

                            </> -->
                                @can('manager.livraison.stock.section')
                                    <li class="sidebar-menu-item {{ menuActive(['manager.livraison.stock.*']) }}">
                                        <a href="{{ route('manager.livraison.stock.section') }}" class="nav-link">
                                            <i class="menu-icon las la-server"></i>
                                            <span class="menu-title">@lang('Stock Magasins de Section')</span>
                                        </a>
                                    </li>
                                    <!-- <li class="sidebar-menu-item {{ menuActive(['manager.livraison.magcentral.*']) }}">
                                    <a href="{{ route('manager.livraison.magcentral.index') }}" class="nav-link">
                                        <i class="menu-icon las la-server"></i>
                                        <span class="menu-title">@lang('Livraison Magasins Centraux')</span>
                                    </a>
                                </li> -->
                                @endcan
                                @can('manager.livraison.magcentral.stock')
                                    <li class="sidebar-menu-item {{ menuActive(['manager.livraison.magcentral*']) }}">
                                        <a href="{{ route('manager.livraison.magcentral.stock') }}" class="nav-link">
                                            <i class="menu-icon las la-server"></i>
                                            <span class="menu-title">@lang('Stock Magasins Centraux')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.livraison.usine.connaissement')
                                    <li
                                        class="sidebar-menu-item {{ menuActive(['manager.livraison.usine.connaissement']) }}">
                                        <a href="{{ route('manager.livraison.usine.connaissement') }}" class="nav-link">
                                            <i class="menu-icon las la-server"></i>
                                            <span class="menu-title">@lang('Connaissement Usine')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.livraison.prime.producteur')
                                    <li
                                        class="sidebar-menu-item {{ menuActive(['manager.livraison.prime.producteur']) }}">
                                        <a href="{{ route('manager.livraison.prime.producteur') }}" class="nav-link">
                                            <i class="menu-icon las la-server"></i>
                                            <span class="menu-title">@lang('Prime aux Producteurs')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.suivi.ssrteclmrs.index') || Auth::user()->can('manager.suivi.menage.index'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['manager.suivi.menage.*', 'manager.suivi.ssrteclmrs.*'], 3) }}">
                            <i class=" menu-icon las la-universal-access"></i>
                            <span class="menu-title">@lang('SSRTE') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive(['manager.suivi.menage.*'], 2) }} ">
                            <ul>
                                @can('manager.suivi.ssrteclmrs.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.ssrteclmrs.index') }}">
                                        <a href="{{ route('manager.suivi.ssrteclmrs.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('SSRTE-CLMRS')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.suivi.menage.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.suivi.menage.index') }}">
                                        <a href="{{ route('manager.suivi.menage.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Enquete Menage')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.agro.evaluation.index') ||
                        Auth::user()->can('manager.agro.approvisionnement.index') ||
                        Auth::user()->can('manager.agro.distribution.index') ||
                        Auth::user()->can('manager.agro.deforestation.index') ||
                        Auth::user()->can('manager.agro.postplanting.index'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('manager.agro*', 3) }}">
                            <i class="menu-icon las la-tree"></i>
                            <span class="menu-title">@lang('Agroforesterie') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('manager.agro*', 2) }} ">
                            <ul>
                                @can('manager.agro.evaluation.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.agro.evaluation.index') }}">
                                        <a href="{{ route('manager.agro.evaluation.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Evaluation des besoins')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.agro.approvisionnement.index')
                                    <li
                                        class="sidebar-menu-item {{ menuActive('manager.agro.approvisionnement.index') }}">
                                        <a href="{{ route('manager.agro.approvisionnement.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Approvisionnement')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.agro.distribution.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.agro.distribution.index') }}">
                                        <a href="{{ route('manager.agro.distribution.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Suivi distribution')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.agro.postplanting.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.agro.postplanting.index') }}">
                                        <a href="{{ route('manager.agro.postplanting.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Evaluation Post-Planting')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.agro.deforestation.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.agro.deforestation.index') }}">
                                        <a href="{{ route('manager.agro.deforestation.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Risques de Déforestation')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.presentation-coop.index') ||
                        Auth::user()->can('manager.employees.index') ||
                        Auth::user()->can('manager.hr.attendances.index') ||
                        Auth::user()->can('manager.leaves.index') ||
                        Auth::user()->can('manager.holidays.index') ||
                        Auth::user()->can('manager.formation-staff.index') ||
                        Auth::user()->can('manager.archivages.index'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['manager.hr.*', 'manager.employees.index', 'manager.departments.*', 'manager.designations.*', 'manager.leaves.*', 'manager.archivages.*', 'manager.formation-staff.*', 'presentation-coop.*'], 3) }}">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Gouvernance Amelioree') </span>
                        </a>
                        <div
                            class="sidebar-submenu {{ menuActive(['manager.hr.*', 'manager.employees.*', 'manager.departments.*', 'manager.designations.*', 'manager.leaves.*', 'manager.archivages.*', 'manager.formation-staff.*', 'presentation-coop.*'], 2) }} ">
                            <ul>
                                @can('manager.presentation-coop.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.presentation-coop.index') }}">
                                        <a href="{{ route('manager.presentation-coop.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Presentation de la cooperative')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.employees.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.employees.index') }}">
                                        <a href="{{ route('manager.employees.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Tous les employes')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.hr.attendances.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.hr.attendances.index') }}">
                                        <a href="{{ route('manager.hr.attendances.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Presences')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.leaves.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.leaves.index') }}">
                                        <a href="{{ route('manager.leaves.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Conges')</span>
                                        </a>
                                    </li>
                                @endcan 
                                @can('manager.formation-staff.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.formation-staff.*') }}">
                                        <a href="{{ route('manager.formation-staff.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Formations Staff')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('manager.archivages.index')
                                    <li class="sidebar-menu-item {{ menuActive('manager.archivages.*') }}">
                                        <a href="{{ route('manager.archivages.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Archivages')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->can('manager.communaute.action.sociale.index') ||
                        Auth::user()->can('manager.communaute.activite.communautaire.index'))
                        
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['manager.communaute.action.sociale.*', 'manager.communaute.activite.communautaire.*'], 3) }}">
                            <i class=" menu-icon las la-universal-access"></i>
                            <span class="menu-title">@lang('Communaute resiliente') </span>
                        </a>
                        <div
                            class="sidebar-submenu {{ menuActive(['manager.communaute.action.sociale.*', 'manager.communaute.activite.communautaire.*'], 2) }} ">
                            <ul>
                                <li
                                    class="sidebar-menu-item {{ menuActive('manager.communaute.action.sociale.index') }}">
                                    <a href="{{ route('manager.communaute.action.sociale.index') }}"
                                        class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Actions Sociales')</span>
                                    </a>
                                </li>
                                <li
                                    class="sidebar-menu-item {{ menuActive('manager.communaute.activite.communautaire.index') }}">
                                    <a href="{{ route('manager.communaute.activite.communautaire.index') }}"
                                        class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Activites Communautaires')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                    @can('manager.ticket.index')
                        <li class="sidebar-menu-item  {{ menuActive('ticket*') }}">
                            <a href="{{ route('manager.ticket.index') }}" class="nav-link">
                                <i class="menu-icon las la-ticket-alt"></i>
                                <span class="menu-title">@lang('Support Ticket')</span>
                            </a>
                        </li>
                    @endcan
                    @can('manager.settings.cooperative-settings.index')
                        <li class="sidebar-menu-item {{ menuActive(['manager.settings.*', 'manager.holidays.*']) }}">
                            <a href="{{ route('manager.settings.cooperative-settings.index') }}" class="nav-link">
                                <i class="menu-icon las la-cogs"></i>
                                <span class="menu-title">@lang('Parametres')</span>
                            </a>
                        </li>
                    @endcan
            </ul>

        </div>



    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
