<!-- navbar-wrapper start -->
<nav class="navbar-wrapper bg--dark">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Rechercher ici...')">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
        <li > 
             @if($general->ln) 
            <x-forms.select fieldName="lang" fieldId="lang" class="lang-select ms-auto langChanage">
                            @foreach ($language as $item)
                                <option  data-content="<span class='flag-icon flag-icon-{{ strtolower($item->code) }} flag-icon-squared'></span> {{ $item->name }}"
                                    value="{{ $item->code }}"
                                    @if(session('lang') == $item->code) selected @endif
                                    >{{ $item->name }}</option>
                            @endforeach
             </x-forms.select>
            @endif
                         
            </li>
            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}"
                                alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-bars"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('manager.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{ route('manager.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang("Mot de passe")</span>
                    </a>

                    <a href="{{ route('logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Déconnexion')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
