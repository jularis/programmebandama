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

            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="las la-bell text--primary @if($adminNotificationCount > 0) icon-left-right @endif"></i>
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Notification')</span>
                        @if($adminNotificationCount > 0)
                            <p>@lang('Vous avez') {{ $adminNotificationCount }} @lang('notification non lue')</p>
                        @else
                            <p>@lang('Pas de notifications non lues')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">
                        @foreach($adminNotifications as $notification)
                            <a href="{{ route('admin.notification.read',$notification->id) }}"
                                class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--green b-radius--rounded"><img
                                            src="{{ getImage(getFilePath('userProfile').'/'.@$notification->user->image,getFileSize('userProfile')) }}"
                                            alt="@lang('Profile Image')"></div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">{{ __($notification->title) }}</h6>
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div><!-- navbar-notifi end -->
                            </a>
                        @endforeach
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('admin.notifications') }}"
                            class="view-all-message">@lang('View all notification')</a>
                    </div>
                </div>
            </li>
            <li>
            @if($general->ln) 
            <x-forms.select fieldName="lang" fieldId="lang" class="lang-select ms-auto langChange">
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
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true"
                    aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage('assets/fcadmin/images/profile/'. auth()->guard('admin')->user()->image) }}"
                                alt="image"></span>
                        <span class="navbar-user__info">
                            <span
                                class="navbar-user__name">{{ auth()->guard('admin')->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-bars"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('admin.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{ route('admin.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang("Mot de passe")</span>
                    </a>

                    <a href="{{ route('admin.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
