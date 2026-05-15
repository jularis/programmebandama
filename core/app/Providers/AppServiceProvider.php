<?php

namespace App\Providers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $general                         = gs();
        $activeTemplate                  = activeTemplate();
        $viewShare['general']            = $general;
        $viewShare['activeTemplate']     = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language']           = Language::all();
        $viewShare['pages']              = Page::where('tempname', $activeTemplate)->where('slug', '!=', 'home')->where('is_default', Status::NO)->get();
        $viewShare['footerPages']        = Page::where('tempname', $activeTemplate)->where('slug', '!=', 'home')->orderby('id', 'DESC')->take(3)->get();
        $viewShare['emptyMessage']       = 'Aucune donnée';


        view()->share($viewShare);

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'pendingTicketCount' => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),

            ]);
        });
        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'     => AdminNotification::where('read_status', 0)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('read_status', 0)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if ($general->force_ssl) {
            \URL::forceScheme('https');
        }
       // Model::preventLazyLoading(!app()->isProduction()); 

        Paginator::useBootstrapFour();
        
    }
}