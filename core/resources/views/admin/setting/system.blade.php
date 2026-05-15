@extends('admin.layouts.app')
@section('panel')

<div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group position-relative mb-0">
                                <div class="system-search-icon"><i class="las la-search"></i></div>
                                <input class="form-control searchInput" type="search" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-12">
                    <div class="emptyArea"></div>
                </div>
                                    <div class="col-xxl-4 col-md-6 general_setting searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.index') }}" class="item-link"></a>
                <i class="las la-cog overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-cog"></i>
    </div>

    <div class="widget-two__content">
        <h3>General Setting</h3>
        <p>Configure the fundamental information of the site.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 logo_favicon searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.logo.icon') }}" class="item-link"></a>
                <i class="las la-images overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-images"></i>
    </div>

    <div class="widget-two__content">
        <h3>Logo and Favicon</h3>
        <p>Upload your logo and favicon here.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 system_configuration searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.system.configuration') }}" class="item-link"></a>
                <i class="las la-cogs overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-cogs"></i>
    </div>

    <div class="widget-two__content">
        <h3>System Configuration</h3>
        <p>Control all of the basic modules of the system.</p>
    </div>

    </div>

                    </div>
   
    <div class="col-xxl-4 col-md-6 notification_setting searchItems">
            <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.notification.global') }}" class="item-link"></a>
                <i class="las la-bell overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-bell"></i>
    </div>

    <div class="widget-two__content">
        <h3>Notification Setting</h3>
        <p>Control and configure overall notification elements of the system.</p>
    </div>

    </div>

    </div>


    <div class="col-xxl-4 col-md-6 notification_setting searchItems">
            <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.notification.email') }}" class="item-link"></a>
                <i class="las la-bell overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5 bg--primary  ">
        <i class="las la-bell"></i>
    </div>

    <div class="widget-two__content">
        <h3>Email Setting</h3>
        <p>Control and configure overall Email elements of the system.</p>
    </div>

    </div>

    </div>


    <div class="col-xxl-4 col-md-6 notification_setting searchItems">
            <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.notification.sms') }}" class="item-link"></a>
                <i class="las la-bell overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-bell"></i>
    </div>

    <div class="widget-two__content">
        <h3>SMS Setting</h3>
        <p>Control and configure overall SMS elements of the system.</p>
    </div>

    </div>

    </div>


    <div class="col-xxl-4 col-md-6 notification_setting searchItems">
            <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.notification.templates') }}" class="item-link"></a>
                <i class="las la-bell overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-bell"></i>
    </div>

    <div class="widget-two__content">
        <h3>Notification Templates</h3>
        <p>Control and configure overall Notification Templates elements of the system.</p>
    </div>

    </div>

    </div>

                                    <div class="col-xxl-4 col-md-6 seo_configuration searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.seo') }}" class="item-link"></a>
                <i class="las la-globe overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-globe"></i>
    </div>

    <div class="widget-two__content">
        <h3>SEO Configuration</h3>
        <p>Configure proper meta title, meta description, meta keywords, etc to make the system SEO-friendly.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 manage_frontend searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="dashboard.html" class="item-link"></a>
                <i class="la la-html5 overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="la la-html5"></i>
    </div>

    <div class="widget-two__content">
        <h3>Manage Frontend</h3>
        <p>Control all of the frontend contents of the system.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 manage_pages searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="dashboard.html" class="item-link"></a>
                <i class="las la-list overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-list"></i>
    </div>

    <div class="widget-two__content">
        <h3>Manage Pages</h3>
        <p>Control dynamic and static pages of the system.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 manage_templates searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="dashboard.html" class="item-link"></a>
                <i class="la la-puzzle-piece overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="la la-puzzle-piece"></i>
    </div>

    <div class="widget-two__content">
        <h3>Manage Templates</h3>
        <p>Control frontend template of the system.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 language searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.language.manage') }}" class="item-link"></a>
                <i class="las la-language overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-language"></i>
    </div>

    <div class="widget-two__content">
        <h3>Language</h3>
        <p>Configure your required languages and keywords to localize the system.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 extensions searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.extensions.index') }}" class="item-link"></a>
                <i class="las la-puzzle-piece overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-puzzle-piece"></i>
    </div>

    <div class="widget-two__content">
        <h3>Extensions</h3>
        <p>Manage extensions of the system here to extend some extra features of the system.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 policy_pages searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.cookie') }}" class="item-link"></a>
                <i class="las la-shield-alt overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-shield-alt"></i>
    </div>

    <div class="widget-two__content">
        <h3>Policy Pages</h3>
        <p>Configure your policy and terms of the system here.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 maintenance_mode searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.maintenance.mode') }}" class="item-link"></a>
                <i class="las la-robot overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-robot"></i>
    </div>

    <div class="widget-two__content">
        <h3>Maintenance Mode</h3>
        <p>Enable or disable the maintenance mode of the system when required.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 gdpr_cookie searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.cookie') }}" class="item-link"></a>
                <i class="las la-cookie-bite overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="las la-cookie-bite"></i>
    </div>

    <div class="widget-two__content">
        <h3>GDPR Cookie</h3>
        <p>Set GDPR Cookie policy if required. It will ask visitor of the system to accept if enabled.</p>
    </div>

    </div>

                    </div>
                                    <div class="col-xxl-4 col-md-6 custom_css searchItems">
                                                <div class="widget-two box--shadow2 b-radius--5  has-link  bg--white">

        <a href="{{ route('admin.setting.custom.css') }}" class="item-link"></a>
                <i class="lab la-css3-alt overlay-icon text--primary"></i>
    
    <div class="widget-two__icon b-radius--5   bg--primary  ">
        <i class="lab la-css3-alt"></i>
    </div>

    <div class="widget-two__content">
        <h3>Custom CSS</h3>
        <p>Write custom css here to modify some styles of frontend of the system if you need to.</p>
    </div>

    </div>

                    </div>
                                   
                                 
                            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
      
        .system-search-icon {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            aspect-ratio: 1;
            padding: 5px;
            display: grid;
            place-items: center;
            color: #888;
        }

        .system-search-icon~.form-control {
            padding-left: 45px;
        }

        .widget-seven .widget-seven__content-amount {
            font-size: 22px;
        }

        .widget-seven .widget-seven__content-subheading {
            font-weight: normal;
        }
        .empty-search img{
            width: 120px;
            margin-bottom: 15px;
        }

        a.item-link:focus,a.item-link:hover {
            background: #4634ff38;
        }
    </style>
@endpush
@push('script')
<script>
        (function($) {
            "use strict";
            var settingsData = {"general_setting":{"keyword":["general","fundamental","site information","site","website settings","basic settings","global settings","site color","timezone","site currency","pagination","currency format","site title","base color","paginate"],"title":"General Setting","subtitle":"Configure the fundamental information of the site.","icon":"las la-cog","route_name":"admin.setting.general"},"logo_favicon":{"keyword":["branding","identity","logo upload","site branding","brand identity","favicon","website icon","website favicon","website logo"],"title":"Logo and Favicon","subtitle":"Upload your logo and favicon here.","icon":"las la-images","route_name":"admin.setting.logo.icon"},"system_configuration":{"keyword":["basic modules","control","modules","system","configuration settings","system control","email control","sms control","language control","email notification","sms notification"],"title":"System Configuration","subtitle":"Control all of the basic modules of the system.","icon":"las la-cogs","route_name":"admin.setting.system.configuration"},"notification_setting":{"keyword":["email configuration","sms configure","email setting","sms setting","email template","sms template","notification template","smtp","sendgrid","send grid","mailjet","mail jet","php","nexmo","clickatell","click a tell","infobip","info bip","message bird","sms broadcast","twilio","text magic","custom api","template setting","global template","global notification"],"title":"Notification Setting","subtitle":"Control and configure overall notification elements of the system.","icon":"las la-bell","route_name":"admin.setting.notification.global.email"},"seo_configuration":{"keyword":["SEO","meta title","meta description","meta keywords","optimization","meta tags","SEO configuration"],"title":"SEO Configuration","subtitle":"Configure proper meta title, meta description, meta keywords, etc to make the system SEO-friendly.","icon":"las la-globe","route_name":"admin.seo"},"manage_frontend":{"keyword":["about section","banner section","blog section","branch section","breadcrumb","client section","contact info","contact us","counter section","faq section","feature section","footer section","order tracking section","partner section","service section","social icons section","team section","frontend","template","manage frontend","frontend contents","frontend settings","about us","banner","contact","faq","social icons","section settings","subscribe"],"title":"Manage Frontend","subtitle":"Control all of the frontend contents of the system.","icon":"la la-html5","route_name":"admin.frontend.index"},"manage_pages":{"keyword":["pages","manage pages","home page","contact page","blog page"],"title":"Manage Pages","subtitle":"Control dynamic and static pages of the system.","icon":"las la-list","route_name":"admin.frontend.manage.pages"},"manage_templates":{"keyword":["Templates","Manage Templates"],"title":"Manage Templates","subtitle":"Control frontend template of the system.","icon":"la la-puzzle-piece","route_name":"admin.frontend.templates"},"language":{"keyword":["language","localize","translation","translate","internationalization","language settings","localization settings","translation settings","configure languages","configure localization"],"title":"Language","subtitle":"Configure your required languages and keywords to localize the system.","icon":"las la-language","route_name":"admin.language.manage"},"extensions":{"keyword":["extensions","plugins","addons","extension settings","plugin settings","addon settings","captcha","custom captcha","google captcha","recaptcha","re-captcha","re captcha","tawk","tawk.to","tawk to","analytics","google analytics","facebook comment"],"title":"Extensions","subtitle":"Manage extensions of the system here to extend some extra features of the system.","icon":"las la-puzzle-piece","route_name":"admin.extensions.index"},"policy_pages":{"keyword":["privacy and policy","terms and condition","terms of service"],"title":"Policy Pages","subtitle":"Configure your policy and terms of the system here.","icon":"las la-shield-alt","route_name":"admin.frontend.sections","params":{"key":"policy_pages"}},"maintenance_mode":{"keyword":["maintenance mode","system maintenance","system health","maintenance settings","system health settings","enable maintenance","disable maintenance","maintenance configuration"],"title":"Maintenance Mode","subtitle":"Enable or disable the maintenance mode of the system when required.","icon":"las la-robot","route_name":"admin.maintenance.mode"},"gdpr_cookie":{"keyword":["GDPR cookie","cookie policy","data privacy","GDPR settings","cookie policy settings","data privacy settings"],"title":"GDPR Cookie","subtitle":"Set GDPR Cookie policy if required. It will ask visitor of the system to accept if enabled.","icon":"las la-cookie-bite","route_name":"admin.setting.cookie"},"custom_css":{"keyword":["custom CSS","modify styles","frontend","styling","design customization","CSS settings","style settings","frontend customization","design settings","customize CSS"],"title":"Custom CSS","subtitle":"Write custom css here to modify some styles of frontend of the system if you need to.","icon":"lab la-css3-alt","route_name":"admin.setting.custom.css"},"sitemap":{"keyword":["Site map","sitemap","xml","sitemap.xml"],"title":"Sitemap XML","subtitle":"Insert the sitemap XML here to enhance SEO performance.","icon":"las la-sitemap","route_name":"admin.setting.sitemap"},"robots":{"keyword":["Robots","txt","robots.txt","robot.txt"],"title":"Robots txt","subtitle":"Insert the robots.txt content here to enhance bot web crawlers and instruct them on how to interact with certain areas of the website.","icon":"las la-robot","route_name":"admin.setting.robot"}};
            // Function to filter settings based on search query
            function filterSettings(query) {
                let filteredSettings = [];
                for (var key in settingsData) {
                    if (settingsData.hasOwnProperty(key)) {
                        var setting = settingsData[key];
                        // Check if the query matches keyword, title, or subtitle
                        var keywordMatch = setting.keyword.some(function(keyword) {
                            return keyword.toLowerCase().includes(query.toLowerCase());
                        });
                        var titleMatch = setting.title.toLowerCase().includes(query.toLowerCase());
                        var subtitleMatch = setting.subtitle.toLowerCase().includes(query.toLowerCase());

                        // If any match is found, add the setting to filtered settings
                        if (keywordMatch || titleMatch || subtitleMatch) {
                            filteredSettings[key] = setting;
                        }
                    }
                }
                return filteredSettings;
            }

            function isEmpty(obj) {
                return Object.keys(obj).length === 0;
            }

            // Function to render filtered settings
            function renderSettings(filteredSettings, query) {
                $('.searchItems').addClass('d-none');
                $('.emptyArea').html('');
                if (isEmpty(filteredSettings)) {
                    $('.emptyArea').html(`<div class="col-12 searchItems text-center mt-4"><div class="card">
                                <div class="card-body">
                                    <div class="empty-search text-center">
                                        <img src="https://programmebandama.com/assets/images/empty_list.png" alt="empty">
                                        <h5 class="text-muted">No search result found.</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                } else {
                    for (const key in filteredSettings) {
                        if (Object.hasOwnProperty.call(filteredSettings, key)) {
                            const element = filteredSettings[key];
                            var setting = element;
                            $(`.searchItems.${key}`).removeClass('d-none');
                        }
                    }
                }
            }


            $('.searchInput').on('input', function() {
                var query = $(this).val().trim();
                var filteredData = filterSettings(query);
                renderSettings(filteredData, query);
            });

            $('.searchInput').highlighter22({
                targets: [".widget-two__content h3", ".widget-two__content p"],
            });

        })(jQuery);
    </script>
@endpush