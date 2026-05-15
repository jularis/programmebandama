<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img
                    src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
                    
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang("Tableau de bord")</span>
                    </a>
                </li>
 
                <li class="sidebar-menu-item {{ menuActive('admin.all') }}">
                    <a href="{{ route('admin.all') }}" class="nav-link ">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Admins')</span>
                    </a>
                </li>


                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)"
                        class="{{ menuActive(['admin.cooperative*', 'admin.livraison.income'], 3) }}">
                        <i class="menu-icon las la-code-branch"></i>
                        <span class="menu-title">@lang('Cooperatives') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.cooperative*', 'admin.livraison.income'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.cooperative.index') }}">
                                <a href="{{ route('admin.cooperative.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Cooperative')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.cooperative.manager*') }}">
                                <a href="{{ route('admin.cooperative.manager.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Gestion des Managers')</span>
                                </a>
                            </li> 

                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item  {{ menuActive(['admin.config.campagne*']) }}">
                    <a href="{{ route('admin.config.campagne.index') }}" class="nav-link">
                        <i class="menu-icon las la-fax"></i>
                        <span class="menu-title">@lang('Gestion des Campagnes')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item  {{ menuActive(['admin.config.programme*']) }}">
                    <a href="{{ route('admin.config.programme.index') }}" class="nav-link">
                        <i class="menu-icon las la-fax"></i>
                        <span class="menu-title">@lang('Gestion des Programmes')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item  {{ menuActive(['admin.config.certification*']) }}">
                    <a href="{{ route('admin.config.certification.index') }}" class="nav-link">
                        <i class="menu-icon las la-file"></i>
                        <span class="menu-title">@lang('Gestion des Certifications')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)"
                        class="{{ menuActive(['admin.foretclassee*', 'admin.agro.deforestation*','admin.traca.parcelle.mapping','admin.traca.parcelle.mapping.polygone'], 3) }}">
                        <i class="menu-icon las la-code-branch"></i>
                        <span class="menu-title">@lang('Agroforesterie') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.foretclassee*', 'admin.agro.deforestation*','admin.traca.parcelle.mapping','admin.traca.parcelle.mapping.polygone'], 2) }} ">
                        <ul>
                        <li class="sidebar-menu-item  {{ menuActive(['admin.foretclassee*']) }}">
                            <a href="{{ route('admin.foretclassee.index') }}" class="nav-link">
                                <i class="menu-icon las la-tree"></i>
                                <span class="menu-title">@lang('Forêts Classées')</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item  {{ menuActive(['admin.traca.parcelle.mapping']) }}">
                            <a href="{{ route('admin.traca.parcelle.mapping') }}" class="nav-link">
                                <i class="menu-icon las la-tree"></i>
                                <span class="menu-title">@lang('Mapping Waypoints Parcelles')</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item  {{ menuActive(['admin.traca.parcelle.mapping.polygone']) }}">
                            <a href="{{ route('admin.traca.parcelle.mapping.polygone') }}" class="nav-link">
                                <i class="menu-icon las la-tree"></i>
                                <span class="menu-title">@lang('Mapping Polygones Parcelles')</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item  {{ menuActive(['admin.agro.deforestation*']) }}">
                            <a href="{{ route('admin.agro.deforestation.index') }}" class="nav-link">
                                <i class="menu-icon las la-tree"></i>
                                <span class="menu-title">@lang('Risques de déforestation')</span>
                            </a>
                        </li>
 

                        </ul>
                    </div>
                </li>
               
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)"
                        class="{{ menuActive(['admin.livraison.info*', 'admin.livraison.invoice', 'admin.livraison.usine*'], 3) }}">
                        <i class="menu-icon las la-fax"></i>
                        <span class="menu-title">@lang('Gestion des Livraisons')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.livraison.info*', 'admin.livraison.invoice', 'admin.livraison.usine*'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['admin.livraison.info*', 'admin.livraison.invoice']) }}">
                                <a href="{{ route('admin.livraison.info.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Livraisons')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.livraison.usine*') }}">
                                <a href="{{ route('admin.livraison.usine.connaissement') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Connaissement Usine')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item  {{ menuActive(['admin.staff.index']) }}">
                    <a href="{{ route('admin.staff.index') }}" class="nav-link">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Liste des Staffs')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.roles.index') }}">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link ">
                        <i class="menu-icon las la-university"></i>
                        <span class="menu-title">@lang('Gestion des Roles')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.permissions.index') }}">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link ">
                        <i class="menu-icon las la-university"></i>
                        <span class="menu-title">@lang('Gestion des Permissions')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.ticket*', 3) }}">
                        <i class="menu-icon la la-ticket"></i>
                        <span class="menu-title">@lang('Support Ticket') </span>
                        @if (0 < $pendingTicketCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }} ">
                                <a href="{{ route('admin.ticket.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Ticket en attente')</span>
                                    @if ($pendingTicketCount)
                                        <span
                                            class="menu-badge pill bg--danger ms-auto">{{ $pendingTicketCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.closed') }} ">
                                <a href="{{ route('admin.ticket.closed') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Ticket Fermé')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.answered') }} ">
                                <a href="{{ route('admin.ticket.answered') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Ticket Répondus')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.index') }} ">
                                <a href="{{ route('admin.ticket.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Toutes les Tickets')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.report*', 3) }}">
                        <i class="menu-icon la la-list"></i>
                        <span class="menu-title">@lang('Report') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }} ">
                        <ul>
                            <li
                                class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                <a href="{{ route('admin.report.login.history') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Historique de connexion')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                <a href="{{ route('admin.report.notification.history') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Historique de Notifications')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                
                <li class="sidebar-menu-item {{ menuActive('admin.setting.*') }}">
                        <a href="{{ route('admin.setting.system.setting') }}" class="nav-link ">
                            <i class="menu-icon las la-life-ring"></i>
                            <span class="menu-title">System Setting</span>
                        </a>
                    </li> 
                    
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.system*', 3) }}">
                        <i class="menu-icon la la-server"></i>
                        <span class="menu-title">@lang('Extra')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.system*', 2) }} ">
                        <ul>
                        <li class="sidebar-menu-item {{ menuActive('admin.system.permission') }} ">
                                <a href="{{ route('admin.system.permission') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Permission de routes')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.info') }} ">
                                <a href="{{ route('admin.system.info') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Application')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.server.info') }} ">
                                <a href="{{ route('admin.system.server.info') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Server')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.optimize') }} ">
                                <a href="{{ route('admin.system.optimize') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Cache')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
 
                <li class="sidebar-menu-item  {{ menuActive('admin.request.report') }}">
                    <a href="{{ route('admin.request.report') }}" class="nav-link">
                        <i class="menu-icon las la-bug"></i>
                        <span class="menu-title">@lang('Report & Request') </span>
                    </a>
                </li>
            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
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
