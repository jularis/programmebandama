<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    }); 
    
    // Admin Password Reset
    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('reset');
        Route::post('reset', 'sendResetCodeEmail');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset/{token}', 'showResetForm')->name('reset.form');
        Route::post('reset/change', 'reset')->name('change');
    });
});

Route::middleware('admin')->group(function () {
    
    Route::controller('AdminController')->group(function () {
        Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Admin Create
        Route::get('all', 'allAdmin')->name('all');
        Route::post('store', 'adminStore')->name('store');
        Route::post('remove/{id}', 'adminRemove')->name('remove');
        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    //Manage Cooperative Controller
    Route::controller('CooperativeController')->name('cooperative.')->prefix('cooperative')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Manage Forets Classees Controller
    Route::controller('ForetclasseeController')->name('foretclassee.')->prefix('foretclassee')->group(function () {
        Route::get('list', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::post('store/tampon', 'storeTampon')->name('storeTampon');
        Route::get('create/tampon', 'createTampon')->name('createTampon');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Manage Agrodeforestations
    Route::controller('AgrodeforestationController')->name('agro.deforestation.')->prefix('agro/deforestation')->group(function () {
        Route::get('polygones', 'index')->name('index');
        Route::get('waypoints', 'waypoints')->name('waypoints');  
    });

    //Manage Parcelle
    Route::controller('ParcelleController')->name('traca.parcelle.')->prefix('parcelle')->group(function () { 
        Route::get('mapping', 'mapping')->name('mapping');
        Route::get('mapping/polygone', 'mappingPolygone')->name('mapping.polygone'); 
    });

    //Cooperative Manager
    Route::controller('CooperativeManagerController')->name('cooperative.manager.')->prefix('cooperative-manager')->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');

        Route::get('staff/{id}', 'staffList')->name('staff.list');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('dashboard/{id}', 'login')->name('dashboard'); 
        Route::get('manager/{id}', 'cooperativeManager')->name('list');
    });

     //Cooperative Localite
     Route::controller('CooperativeLocaliteController')->name('cooperative.localite.')->prefix('cooperative-localite')->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit'); 
        Route::post('status/{id}', 'status')->name('status');
        Route::post('/uploadcontent', 'uploadContent')->name('uploadcontent');
    });

    // Route::controller('CourierSettingController')->name('courier.')->prefix('courier')->group(function () {

    //     Route::name('unit.')->prefix('manage')->group(function () {
    //         Route::get('unit', 'unitIndex')->name('index');
    //         Route::post('unit/store', 'unitStore')->name('store');
    //         Route::post('status/{id}', 'status')->name('status');
    //         Route::get('type/', 'typeIndex')->name('type.index');
    //         Route::post('type/store', 'typeStore')->name('type.store');
    //         Route::post('type/status/{id}', 'typeStatus')->name('type.status');
    //     }); 
    // });

    Route::controller('SettingController')->name('config.')->prefix('setting')->group(function () {
 
            Route::get('campagne/', 'campagneIndex')->name('campagne.index');
            Route::post('campagne/store', 'campagneStore')->name('campagne.store');
            Route::post('campagne/status/{id}', 'campagneStatus')->name('campagne.status'); 

            Route::get('campagne/periode', 'periodeIndex')->name('campagne.periodeIndex');
            Route::post('campagne/periode/store', 'periodeStore')->name('campagne.periodeStore');
            Route::post('campagne/periode/status/{id}', 'periodeStatus')->name('campagne.periodeStatus'); 

            Route::get('programme/', 'programmeIndex')->name('programme.index');
            Route::post('programme/store', 'programmeStore')->name('programme.store');
            Route::post('programme/status/{id}', 'programmeStatus')->name('programme.status'); 
            Route::get('programme/prime', 'primeIndex')->name('programme.primeIndex');
            Route::post('programme/prime/store', 'primeStore')->name('programme.primeStore');
            Route::post('programme/prime/status/{id}', 'primeStatus')->name('programme.primeStatus');  

            Route::get('certification/', 'certificationIndex')->name('certification.index');
            Route::post('certification/store', 'certificationStore')->name('certification.store');
            Route::post('certification/status/{id}', 'certificationStatus')->name('certification.status'); 
            
            Route::get('travaux-dangereux/', 'travauxDangereuxIndex')->name('travauxDangereux.index');
            Route::post('travaux-dangereux/store', 'travauxDangereuxStore')->name('travauxDangereux.store');
            Route::post('travaux-dangereux/status/{id}', 'travauxDangereuxStatus')->name('travauxDangereux.status'); 
            Route::get('travaux-legers/', 'travauxLegersIndex')->name('travauxLegers.index');
            Route::post('travaux-legers/store', 'travauxLegersStore')->name('travauxLegers.store');
            Route::post('travaux-legers/status/{id}', 'travauxLegersStatus')->name('travauxLegers.status');
            Route::get('arret-ecole/', 'arretEcoleIndex')->name('arretEcole.index');
            Route::post('arret-ecole/store', 'arretEcoleStore')->name('arretEcole.store');
            Route::post('arret-ecole/status/{id}', 'arretEcoleStatus')->name('arretEcole.status');
            Route::get('type-formation/', 'typeFormationIndex')->name('typeFormation.index');
            Route::post('type-formation/store', 'typeFormationStore')->name('typeFormation.store');
            Route::post('type-formation/status/{id}', 'typeFormationStatus')->name('typeFormation.status');
            Route::get('theme-formation/', 'themeFormationIndex')->name('themeFormation.index');
            Route::post('theme-formation/store', 'themeFormationStore')->name('themeFormation.store');
            Route::post('theme-formation/status/{id}', 'themeFormationStatus')->name('themeFormation.status');
            Route::get('categorie-questionnaire/', 'categorieQuestionnaireIndex')->name('categorieQuestionnaire.index');
            Route::post('categorie-questionnaire/store', 'categorieQuestionnaireStore')->name('categorieQuestionnaire.store');
            Route::post('categorie-questionnaire/status/{id}', 'categorieQuestionnaireStatus')->name('categorieQuestionnaire.status');
            Route::get('questionnaire/', 'questionnaireIndex')->name('questionnaire.index');
            Route::post('questionnaire/store', 'questionnaireStore')->name('questionnaire.store');
            Route::post('questionnaire/status/{id}', 'questionnaireStatus')->name('questionnaire.status');
    });

    // Route::controller('CourierController')->name('courier.')->prefix('courier')->group(function () {
    //     Route::get('list', 'courierInfo')->name('info.index');
    //     Route::get('details/{id}', 'courierDetail')->name('info.details');
    //     Route::get('invoice/{id}', 'invoice')->name('invoice');
    // });

    Route::controller('LivraisonController')->name('livraison.')->prefix('livraison')->group(function () {
        Route::get('list', 'livraisonInfo')->name('info.index');
        Route::get('details/{id}', 'livraisonDetail')->name('info.details');
        Route::get('invoice/{id}', 'invoice')->name('invoice');
        Route::get('cooperative/income', 'cooperativeIncome')->name('income');
        Route::get('usine/connaissement', 'connaissementUsine')->name('usine.connaissement');
        Route::get('usine/connaissement/invoice/{id}', 'connaissementUsineInvoice')->name('usine.invoice');
        Route::get('usine/connaissement/suivi/{id}', 'connaissementUsineSuivi')->name('usine.suivi');
        Route::post('usine/connaissement/delivery', 'connaissementUsineDelivery')->name('usine.delivery');
        Route::post('usine/connaissement/refoule', 'connaissementUsineRefoule')->name('usine.refoule');
        Route::post('usine/connaissement/suivi/store', 'connaissementUsineSuiviStore')->name('usine.suivi.store');
    });

    //staff

    Route::controller('StaffController')->name('staff.')->prefix('staff')->group(function () {
        Route::get('/', 'list')->name('index');
    });



    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });

    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{ticket}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
    });

    Route::controller('GeneralSettingController')->group(function () {
        Route::get('system-setting', 'systemSetting')->name('setting.system.setting');
        // General Setting
        Route::get('general-setting', 'index')->name('setting.index');
        Route::post('general-setting', 'update')->name('setting.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::controller('App\Http\Controllers\Admin\SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('permission', 'permission')->name('permission');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
            Route::get('permission-routes', 'permissionRoutes')->name('permission.routes');
        });
});
