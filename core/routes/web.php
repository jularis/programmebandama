<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Manager\ImageController;
use App\Http\Controllers\Manager\LeaveController;
use App\Http\Controllers\Manager\StaffController;
use App\Http\Controllers\Manager\ImportController;
use App\Http\Controllers\Manager\MenageController;
use App\Http\Controllers\Manager\HolidayController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\SettingController;
use App\Http\Controllers\Manager\TimelogController;
use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\Manager\ParcelleController;
use App\Http\Controllers\Manager\SettingsController;
use App\Http\Controllers\Manager\ArchivageController;
use App\Http\Controllers\Manager\FormationController;
use App\Http\Controllers\Manager\LeaveFileController;
use App\Http\Controllers\Manager\LeaveTypeController;
use App\Http\Controllers\Manager\LivraisonController;
use App\Http\Controllers\Manager\AttendanceController;
use App\Http\Controllers\Manager\CommunauteController;
use App\Http\Controllers\Manager\DepartmentController;
use App\Http\Controllers\Manager\EstimationController;
use App\Http\Controllers\Manager\InspectionController;
use App\Http\Controllers\Manager\ProducteurController;
use App\Http\Controllers\Manager\SsrteclmrsController;
use App\Http\Controllers\Manager\ApplicationController;
use App\Http\Controllers\Manager\DesignationController;
use App\Http\Controllers\Manager\EmployeeDocController;
use App\Http\Controllers\Manager\LeavesQuotaController;
use App\Http\Controllers\Manager\EmployeeFileController;
use App\Http\Controllers\Manager\LeaveSettingController;
use App\Http\Controllers\Manager\ActionSocialeController;
use App\Http\Controllers\Manager\EmployeeShiftController;
use App\Http\Controllers\Manager\ManagerTicketController;
use App\Http\Controllers\Manager\SuiviParcelleController;
use App\Http\Controllers\Manager\AgroevaluationController;
use App\Http\Controllers\Manager\FormationStaffController;
use App\Http\Controllers\Manager\SectionSettingController;
use App\Http\Controllers\Manager\LocaliteSettingController;
use App\Http\Controllers\Manager\TimelogCalendarController;
use App\Http\Controllers\Manager\AgrodistributionController;
use App\Http\Controllers\Manager\AgropostplantingController;
use App\Http\Controllers\Manager\EmergencyContactController;
use App\Http\Controllers\Manager\PresentationCoopController;
use App\Http\Controllers\Manager\ProgrammeSettingController;
use App\Http\Controllers\Manager\AgrodeforestationController;
use App\Http\Controllers\Manager\AttendanceSettingController;
use App\Http\Controllers\Manager\LivraisonCentraleController;
use App\Http\Controllers\Manager\CooperativeSettingController;
use App\Http\Controllers\Manager\ActiviteCommunautaireController;
use App\Http\Controllers\Manager\AgroapprovisionnementController;



Route::namespace('Manager\Auth')->group(function () {

   
    //Manager Login
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });
    //Manager Password Forgot
    Route::controller('ForgotPasswordController')->name('password.')->prefix('password')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
    //Manager Password Rest
    Route::controller('ResetPasswordController')->name('password.')->prefix('password')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('reset.form');
        Route::post('password/reset/change', 'reset')->name('change');
    });
});


Route::controller('SiteController')->group(function () {
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');
    Route::get('/', 'index')->name('home');
    Route::get('/privacy', 'politique')->name('politique');
    Route::get('/order/tracking', 'orderTracking')->name('order.tracking');
    Route::post('/order/tracking', 'findOrder')->name('order.tracking');
    Route::get('/page/{slug}', 'pages')->name('pages');
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('/blog', 'blog')->name('blog');
    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');
});

Route::middleware('auth')->group(function () {
    Route::middleware('manager')->group(function () {

    
        //Home Controller
        Route::name('')->group(function () {
            Route::get('dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
            Route::get('/change/{lang?}', [ManagerController::class, 'changeLanguage'])->name('lang');
            //Manage Profile
            Route::get('password', [ManagerController::class, 'password'])->name('password');
            Route::get('profile', [ManagerController::class, 'profile'])->name('profile');
            Route::post('profile/update', [ManagerController::class, 'profileUpdate'])->name('profile.update.data');
            Route::post('password/update', [ManagerController::class, 'passwordUpdate'])->name('password.update.data');

            //Manage Cooperative
            Route::name('cooperative.')->prefix('cooperative')->group(function () {
                Route::get('list', [ManagerController::class, 'cooperativeList'])->name('index');
                Route::get('income', [ManagerController::class, 'cooperativeIncome'])->name('income');
            });
        });
        //Présentation cooperative
        Route::name('presentation-coop.')->prefix('presentation-coop')->group(function () {
            Route::get('index', [PresentationCoopController::class, 'index'])->name('index');
            Route::get('create', [PresentationCoopController::class, 'create'])->name('create');
            Route::post('store', [PresentationCoopController::class, 'store'])->name('store');
            Route::post('chiffreAffairePartenaire', [PresentationCoopController::class, 'chiffre_affaire_partenaire'])->name('chiffreAffairePartenaire');
        });
        //Manage Staff
        Route::name('staff.')->prefix('staff')->group(function () {
            Route::get('create', [StaffController::class, 'create'])->name('create');
            Route::get('list', [StaffController::class, 'index'])->name('index');
            Route::post('store', [StaffController::class, 'store'])->name('store');
            Route::get('edit/{id}', [StaffController::class, 'edit'])->name('edit');
            Route::post('delete/{id}', [StaffController::class, 'delete'])->name('delete');
            Route::post('status/{id}', [StaffController::class, 'status'])->name('status');
            Route::get('staff/get/localite', [StaffController::class, 'getLocalite'])->name('getLocalite');
            Route::get('magasin/{id}', [StaffController::class, 'magasinIndex'])->name('magasin.index');
            Route::post('magasin/store', [StaffController::class, 'magasinStore'])->name('magasin.store');
            Route::post('magasin/status/{id}', [StaffController::class, 'magasinStatus'])->name('magasin.status');
            Route::get('/exportStaffsExcel', [StaffController::class, 'exportExcel'])->name('exportExcel.staffAll');
            Route::get('staff/dashboard/{id}', [StaffController::class, 'staffLogin'])->name('stafflogin');
        });

        // employee routes
 

        // Holidays
        Route::get('holidays/mark-holiday', [HolidayController::class, 'markHoliday'])->name('holidays.mark_holiday');
        Route::post('holidays/mark-holiday-store', [HolidayController::class, 'markDayHoliday'])->name('holidays.mark_holiday_store');
        Route::get('holidays/table-view', [HolidayController::class, 'tableView'])->name('holidays.table_view');
        Route::post('holidays/apply-quick-action', [HolidayController::class, 'applyQuickAction'])->name('holidays.apply_quick_action');
        Route::resource('holidays', HolidayController::class);

        Route::get('designations/designation-hierarchy', [DesignationController::class, 'hierarchyData'])->name('designation.hierarchy');
        Route::post('designations/changeParent', [DesignationController::class, 'changeParent'])->name('designation.changeParent');
        Route::post('designations/search-filter', [DesignationController::class, 'searchFilter'])->name('designation.srchFilter');
        Route::post('designations/apply-quick-action', [DesignationController::class, 'applyQuickAction'])->name('designations.apply_quick_action');
        Route::resource('designations', DesignationController::class);

        Route::post('departments/apply-quick-action', [DepartmentController::class, 'applyQuickAction'])->name('departments.apply_quick_action');
        Route::get('departments/department-hierarchy', [DepartmentController::class, 'hierarchyData'])->name('department.hierarchy');
        Route::post('department/changeParent', [DepartmentController::class, 'changeParent'])->name('department.changeParent');
        Route::get('department/search', [DepartmentController::class, 'searchDepartment'])->name('departments.search');
        Route::get('department/{id}', [DepartmentController::class, 'getMembers'])->name('departments.members');
        Route::resource('departments', DepartmentController::class);
        // Get quill image uploaded
        Route::get('quill-image/{image}', [ImageController::class, 'getImage'])->name('image.getImage');
        // Cropper Model
        Route::get('cropper/{element}', [ImageController::class, 'cropper'])->name('cropper');

        Route::post('formation-staff/status/{id}', [FormationStaffController::class, 'status'])->name('formation-staff.status');
        Route::post('formation-staff/exportFormationsExcel', [FormationStaffController::class, 'exportExcel'])->name('formation-staff.exportExcel.formationAll');
        Route::resource('formation-staff', FormationStaffController::class);
 

        Route::name('settings.')->prefix('settings')->group(function () {
            Route::resource('attendance-settings', AttendanceSettingController::class); 
            Route::resource('leaves-settings', LeaveSettingController::class); 
            Route::resource('cooperative-settings', CooperativeSettingController::class);
            Route::resource('durabilite-settings', ProgrammeSettingController::class);
            Route::post('/uploadcontent/section', [SectionSettingController::class,'uploadContent'])->name('section-settings.uploadcontent');
            Route::resource('section-settings', SectionSettingController::class);
            Route::resource('localite-settings', LocaliteSettingController::class);
            Route::post('localite-settings/status/{id}', [LocaliteSettingController::class, 'status'])->name('localite-settings.status');
            Route::post('localite-settings/uploadcontent', [LocaliteSettingController::class, 'uploadContent'])->name('localite-settings.uploadcontent');
            Route::post('leaves-settings/change-permission', [LeaveSettingController::class, 'changePermission'])->name('leaves-settings.changePermission');
            Route::get('campagne/', [SettingController::class, 'campagneIndex'])->name('campagne.index');
            Route::post('campagne/store', [SettingController::class, 'campagneStore'])->name('campagne.store');
            Route::post('campagne/status/{id}', [SettingController::class, 'campagneStatus'])->name('campagne.status');
            Route::get('travaux-dangereux/', [SettingController::class, 'travauxDangereuxIndex'])->name('travauxDangereux.index');
            Route::post('travaux-dangereux/store', [SettingController::class, 'travauxDangereuxStore'])->name('travauxDangereux.store');
            Route::post('travaux-dangereux/status/{id}', [SettingController::class, 'travauxDangereuxStatus'])->name('travauxDangereux.status');
            Route::get('travaux-legers/', [SettingController::class, 'travauxLegersIndex'])->name('travauxLegers.index');
            Route::post('travaux-legers/store', [SettingController::class, 'travauxLegersStore'])->name('travauxLegers.store');
            Route::post('travaux-legers/status/{id}', [SettingController::class, 'travauxLegersStatus'])->name('travauxLegers.status');
            Route::get('arret-ecole/', [SettingController::class, 'arretEcoleIndex'])->name('arretEcole.index');
            Route::post('arret-ecole/store', [SettingController::class, 'arretEcoleStore'])->name('arretEcole.store');
            Route::post('arret-ecole/status/{id}', [SettingController::class, 'arretEcoleStatus'])->name('arretEcole.status');
            Route::get('type-formation/', [SettingController::class, 'typeFormationIndex'])->name('typeFormation.index');
            Route::post('type-formation/store', [SettingController::class, 'typeFormationStore'])->name('typeFormation.store');
            Route::post('type-formation/status/{id}', [SettingController::class, 'typeFormationStatus'])->name('typeFormation.status');
            Route::get('theme-formation/', [SettingController::class, 'themeFormationIndex'])->name('themeFormation.index');
            Route::post('theme-formation/store', [SettingController::class, 'themeFormationStore'])->name('themeFormation.store');
            Route::post('theme-formation/status/{id}', [SettingController::class, 'themeFormationStatus'])->name('themeFormation.status');
            Route::get('sous-theme-formation/', [SettingController::class, 'sousThemeFormationIndex'])->name('sousThemeFormation.index');
            Route::post('sous-theme-formation/store', [SettingController::class, 'sousThemeFormationStore'])->name('sousThemeFormation.store');
            Route::post('sous-theme-formation/status/{id}', [SettingController::class, 'sousThemeFormationStatus'])->name('sousThemeFormation.status');
            Route::get('module-formation-staff/', [SettingController::class, 'moduleFormationStaffIndex'])->name('moduleFormationStaff.index');
            Route::post('module-formation-staff/store', [SettingController::class, 'moduleFormationStaffStore'])->name('moduleFormationStaff.store');
            Route::post('module-formation-staff/status/{id}', [SettingController::class, 'moduleFormationStaffStatus'])->name('moduleFormationStaff.status');
            Route::get('theme-formation-staff/', [SettingController::class, 'themeFormationStaffIndex'])->name('themeFormationStaff.index');
            Route::post('theme-formation-staff/store', [SettingController::class, 'themeFormationStaffStore'])->name('themeFormationStaff.store');
            Route::post('theme-formation-staff/status/{id}', [SettingController::class, 'themeFormationStaffStatus'])->name('themeFormationStaff.status');
            Route::get('categorie-questionnaire/', [SettingController::class, 'categorieQuestionnaireIndex'])->name('categorieQuestionnaire.index');
            Route::post('categorie-questionnaire/store', [SettingController::class, 'categorieQuestionnaireStore'])->name('categorieQuestionnaire.store');
            Route::post('categorie-questionnaire/status/{id}', [SettingController::class, 'categorieQuestionnaireStatus'])->name('categorieQuestionnaire.status');
            Route::get('questionnaire/', [SettingController::class, 'questionnaireIndex'])->name('questionnaire.index');
            Route::post('questionnaire/store', [SettingController::class, 'questionnaireStore'])->name('questionnaire.store');
            Route::post('questionnaire/status/{id}', [SettingController::class, 'questionnaireStatus'])->name('questionnaire.status');
            Route::get('espece-arbre/', [SettingController::class, 'especeArbreIndex'])->name('especeArbre.index');
            Route::post('espece-arbre/store', [SettingController::class, 'especeArbreStore'])->name('especeArbre.store');
            Route::post('espece-arbre/status/{id}', [SettingController::class, 'especeArbreStatus'])->name('especeArbre.status');
            Route::get('type-archive/', [SettingController::class, 'typeArchiveIndex'])->name('typeArchive.index');
            Route::post('type-archive/store', [SettingController::class, 'typeArchiveStore'])->name('typeArchive.store');
            Route::post('type-archive/status/{id}', [SettingController::class, 'typeArchiveStatus'])->name('typeArchive.status');

            Route::get('departement/', [SettingController::class, 'departementIndex'])->name('departements.index');
            Route::post('departement/store', [SettingController::class, 'departementStore'])->name('departements.store');
            Route::post('departement/status/{id}', [SettingController::class, 'departementStatus'])->name('departements.status');

            Route::get('designation/', [SettingController::class, 'designationIndex'])->name('designations.index');
            Route::post('designation/store', [SettingController::class, 'designationStore'])->name('designations.store');
            Route::post('designation/status/{id}', [SettingController::class, 'designationStatus'])->name('designations.status');

            Route::get('instance/', [SettingController::class, 'instanceIndex'])->name('instance.index');
            Route::post('instance/store', [SettingController::class, 'instanceStore'])->name('instance.store');
            Route::get('document-ad/', [SettingController::class, 'documentadIndex'])->name('documentad.index');
            Route::post('document-ad/store', [SettingController::class, 'documentadStore'])->name('documentad.store');

            Route::get('magasin-section/', [SettingController::class, 'magasinSectionIndex'])->name('magasinSection.index');
            Route::post('magasin-section/store', [SettingController::class, 'magasinSectionStore'])->name('magasinSection.store');
            Route::post('magasin-section/status/{id}', [SettingController::class, 'magasinSectionStatus'])->name('magasinSection.status');

            Route::get('magasin-central/', [SettingController::class, 'magasinCentralIndex'])->name('magasinCentral.index');
            Route::post('magasin-central/store', [SettingController::class, 'magasinCentralStore'])->name('magasinCentral.store');
            Route::post('magasin-central/status/{id}', [SettingController::class, 'magasinCentralStatus'])->name('magasinCentral.status');

            Route::get('formateur-staff/', [SettingController::class, 'formateurStaffIndex'])->name('formateurStaff.index');
            Route::get('formateur-staff-list/', [SettingController::class, 'formateurList'])->name('formateurStaff.list');
            Route::post('formateur-staff/status/{id}', [SettingController::class, 'formateurStaffStatus'])->name('formateurStaff.status');
            Route::post('formateur-staff/store', [SettingController::class, 'formateurStaffStore'])->name('formateurStaff.store');
            Route::get('vehicule/', [SettingController::class, 'vehiculeIndex'])->name('vehicule.index');
            Route::post('vehicule/store', [SettingController::class, 'vehiculeStore'])->name('vehicule.store');
            Route::post('vehicule/status/{id}', [SettingController::class, 'vehiculeStatus'])->name('vehicule.status');

            //route pour les remorques

            Route::get('remorque/', [SettingController::class, 'remorqueIndex'])->name('remorque.index');
            Route::post('remorque/store', [SettingController::class, 'remorqueStore'])->name('remorque.store');
            Route::post('remorque/status/{id}', [SettingController::class, 'remorqueStatus'])->name('remorque.status');

            Route::get('transporteur/modal', [SettingController::class, 'transporteurModalIndex'])->name('transporteurModal.index');
            Route::get('transporteur/', [SettingController::class, 'transporteurIndex'])->name('transporteur.index');
            Route::post('transporteur/store', [SettingController::class, 'transporteurStore'])->name('transporteur.store');
            Route::post('transporteur/modal/store', [SettingController::class, 'transporteurModalStore'])->name('transporteurModal.store');
            Route::post('transporteur/status/{id}', [SettingController::class, 'transporteurStatus'])->name('transporteur.status');
            
            Route::get('entreprise/modal', [SettingController::class, 'entrepriseModalIndex'])->name('entrepriseModal.index');
            Route::get('entreprises/',[SettingController::class, 'entrepriseIndex'])->name('entreprise.index');
            Route::post('entreprise/store', [SettingController::class, 'entrepriseStore'])->name('entreprise.store');
            Route::post('entreprise/status/{id}', [SettingController::class, 'entrepriseStatus'])->name('entreprise.status');
        });

        Route::resource('employee-files', EmployeeFileController::class);
        Route::resource('leaveType', LeaveTypeController::class);
        Route::post('employee-shifts/set-default', [EmployeeShiftController::class, 'setDefaultShift'])->name('employee-shifts.set_default');
        Route::resource('employee-shifts', EmployeeShiftController::class);
        Route::resource('settings', SettingsController::class)->only(['edit', 'update', 'index']);

        Route::resource('employees', EmployeeController::class);
        Route::resource('emergency-contacts', EmergencyContactController::class);
        Route::get('employee-docs/download/{id}', [EmployeeDocController::class, 'download'])->name('employee-docs.download');
        Route::resource('employee-docs', EmployeeDocController::class);

        Route::get('employee-leaves/employeeLeaveTypes/{id}', [LeavesQuotaController::class, 'employeeLeaveTypes'])->name('employee-leaves.employee_leave_types');
        Route::resource('employee-leaves', LeavesQuotaController::class);
        // ----------------------------- form leaves ------------------------------//

        Route::get('leaves/leaves-date', [LeaveController::class, 'getDate'])->name('leaves.date');
        Route::get('leaves/personal', [LeaveController::class, 'personalLeaves'])->name('leaves.personal');
        Route::get('leaves/calendar', [LeaveController::class, 'leaveCalendar'])->name('leaves.calendar');
        Route::post('leaves/data', [LeaveController::class, 'data'])->name('leaves.data');
        Route::post('leaves/leaveAction', [LeaveController::class, 'leaveAction'])->name('leaves.leave_action');
        Route::get('leaves/show-reject-modal', [LeaveController::class, 'rejectLeave'])->name('leaves.show_reject_modal');
        Route::get('leaves/show-approved-modal', [LeaveController::class, 'approveLeave'])->name('leaves.show_approved_modal');
        Route::post('leaves/pre-approve-leave', [LeaveController::class, 'preApprove'])->name('leaves.pre_approve_leave');
        Route::post('leaves/apply-quick-action', [LeaveController::class, 'applyQuickAction'])->name('leaves.apply_quick_action');
        Route::get('leaves/view-related-leave/{id}', [LeaveController::class, 'viewRelatedLeave'])->name('leaves.view_related_leave');
        Route::resource('leaves', LeaveController::class);

        // leaves files routes
        Route::get('leave-files/download/{id}', [LeaveFileController::class, 'download'])->name('leave-files.download');
        Route::resource('leave-files', LeaveFileController::class);

        Route::match(['GET', 'POST'], '/archivages/export', [ArchivageController::class, 'export'])->name('archivages.export');
        Route::match(['GET', 'POST'], '/archivages/status/{id}', [ArchivageController::class, 'status'])->name('archivages.status');
        Route::resource('archivages', ArchivageController::class);

        Route::name('hr.')->prefix('hr')->group(function () {
            Route::post('employees/apply-quick-action', [EmployeeController::class, 'applyQuickAction'])->name('employees.apply_quick_action');
            Route::post('employees/assignRole', [EmployeeController::class, 'assignRole'])->name('employees.assign_role');
            Route::get('employees/byDepartment/{id}', [EmployeeController::class, 'byDepartment'])->name('employees.by_department');
            Route::get('employees/invite-member', [EmployeeController::class, 'inviteMember'])->name('employees.invite_member');
            Route::get('employees/import', [EmployeeController::class, 'importMember'])->name('employees.import');
            Route::post('employees/import', [EmployeeController::class, 'importStore'])->name('employees.import.store');
            Route::post('employees/import/process', [EmployeeController::class, 'importProcess'])->name('employees.import.process');
            Route::post('employees/send-invite', [EmployeeController::class, 'sendInvite'])->name('employees.send_invite');
            Route::post('employees/create-link', [EmployeeController::class, 'createLink'])->name('employees.create_link');
            // Attendance
            Route::get('attendances/export-attendance/{year}/{month}/{id}', [AttendanceController::class, 'exportAttendanceByMember'])->name('attendances.export_attendance');
            Route::get('attendances/export-all-attendance/{year}/{month}/{id}/{department}/{designation}', [AttendanceController::class, 'exportAllAttendance'])->name('attendances.export_all_attendance');
            Route::post('attendances/employee-data', [AttendanceController::class, 'employeeData'])->name('attendances.employee_data');
            Route::get('attendances/mark/{id}/{day}/{month}/{year}', [AttendanceController::class, 'mark'])->name('attendances.mark');
            Route::get('attendances/by-member', [AttendanceController::class, 'byMember'])->name('attendances.by_member');
            Route::get('attendances/by-hour', [AttendanceController::class, 'byHour'])->name('attendances.by_hour');
            Route::post('attendances/bulk-mark', [AttendanceController::class, 'bulkMark'])->name('attendances.bulk_mark');
            Route::get('attendances/import', [AttendanceController::class, 'importAttendance'])->name('attendances.import');
            Route::post('attendances/import', [AttendanceController::class, 'importStore'])->name('attendances.import.store');
            Route::post('attendances/import/process', [AttendanceController::class, 'importProcess'])->name('attendances.import.process');
            Route::get('attendances/by-map-location', [AttendanceController::class, 'byMapLocation'])->name('attendances.by_map_location');
            Route::resource('attendances', AttendanceController::class);
            Route::get('attendance/{id}/{day}/{month}/{year}', [AttendanceController::class, 'addAttendance'])->name('attendances.add-user-attendance');
            Route::get('import/process/{name}/{id}', [ImportController::class, 'getImportProgress'])->name('import.process.progress');
            Route::get('employees/import/exception/{name}', [ImportController::class, 'getQueueException'])->name('import.process.exception');
        });

        // Timelogs

        Route::name('hr.')->prefix('hr')->group(function () {

            Route::get('by-employee', [TimelogController::class, 'byEmployee'])->name('timelogs.by_employee');
            Route::get('export', [TimelogController::class, 'export'])->name('timelogs.export');
            Route::get('show-active-timer', [TimelogController::class, 'showActiveTimer'])->name('timelogs.show_active_timer');
            Route::get('show-timer', [TimelogController::class, 'showTimer'])->name('timelogs.show_timer');
            Route::post('start-timer', [TimelogController::class, 'startTimer'])->name('timelogs.start_timer');
            Route::get('timer-data', [TimelogController::class, 'timerData'])->name('timelogs.timer_data');
            Route::post('stop-timer', [TimelogController::class, 'stopTimer'])->name('timelogs.stop_timer');
            Route::post('pause-timer', [TimelogController::class, 'pauseTimer'])->name('timelogs.pause_timer');
            Route::post('resume-timer', [TimelogController::class, 'resumeTimer'])->name('timelogs.resume_timer');
            Route::post('apply-quick-action', [TimelogController::class, 'applyQuickAction'])->name('timelogs.apply_quick_action');

            Route::post('employee_data', [TimelogController::class, 'employeeData'])->name('timelogs.employee_data');
            Route::post('user_time_logs', [TimelogController::class, 'userTimelogs'])->name('timelogs.user_time_logs');
            Route::post('approve_timelog', [TimelogController::class, 'approveTimelog'])->name('timelogs.approve_timelog');
        });
        Route::resource('timelog-calendar', TimelogCalendarController::class);
        Route::resource('timelogs', TimelogController::class);

        //Manage Producteur
        Route::name('traca.producteur.')->prefix('producteur')->group(function () {
            Route::get('list', [ProducteurController::class,'index'])->name('index');
            Route::get('infos/{id}', [ProducteurController::class,'infos'])->name('infos');
            Route::get('show/{id}', [ProducteurController::class,'showinfosproducteur'])->name('showinfosproducteur');
            Route::get('showproducteur/{id}', [ProducteurController::class,'showproducteur'])->name('showproducteur');
            Route::get('create', [ProducteurController::class,'create'])->name('create');
            Route::post('store', [ProducteurController::class,'store'])->name('store');
            Route::post('update/{id}', [ProducteurController::class,'update'])->name('update');
            Route::post('info/store', [ProducteurController::class,'storeinfo'])->name('storeinfo');
            Route::get('infos/edit/{id}', [ProducteurController::class,'editinfo'])->name('editinfo');
            Route::get('edit/{id}', [ProducteurController::class,'edit'])->name('edit');
            Route::post('status/{id}', [ProducteurController::class,'status'])->name('status');
            Route::post('delete/{id}', [ProducteurController::class, 'delete'])->name('delete');
            Route::get('/export/producteurs/excel', [ProducteurController::class,'exportExcel'])->name('exportExcel.producteurAll');
            Route::get('/export/producteurs/excel/all/liste', [ProducteurController::class,'exportExcelAllList'])->name('exportExcel.producteurAllList');
            Route::post('/uploadcontent', [ProducteurController::class,'uploadContent'])->name('uploadcontent');
            Route::post('/update/upload/content', [ProducteurController::class,'updateUploadContent'])->name('update.uploadcontent');
        });

        //Manage Parcelle
        Route::name('traca.parcelle.')->prefix('parcelle')->group(function () {
            Route::get('list', [ParcelleController::class,'index'])->name('index');
            Route::get('create', [ParcelleController::class,'create'])->name('create');
            Route::post('store', [ParcelleController::class,'store'])->name('store');
            Route::get('edit/{id}', [ParcelleController::class,'edit'])->name('edit');
            Route::get('show/{id}', [ParcelleController::class,'show'])->name('show');
            Route::post('status/{id}', [ParcelleController::class,'status'])->name('status');
            Route::post('delete/{id}', [ParcelleController::class, 'delete'])->name('delete');
            Route::get('/exportParcellesExcel', [ParcelleController::class,'exportExcel'])->name('exportExcel.parcelleAll');
            Route::get('mapping', [ParcelleController::class,'mapping'])->name('mapping');
            Route::get('mapping/polygone', [ParcelleController::class,'mappingPolygone'])->name('mapping.polygone');
            Route::post('/uploadcontent', [ParcelleController::class,'uploadContent'])->name('uploadcontent');
            Route::get('/upload/kml', [ParcelleController::class,'uploadKML'])->name('uploadkml');
            Route::post('/upload/kml', [ParcelleController::class,'uploadKML'])->name('uploadkml');
        });

        //Manage Estimation
        Route::name('traca.estimation.')->prefix('estimation')->group(function () {
            Route::get('list', [EstimationController::class,'index'])->name('index');
            Route::get('create', [EstimationController::class,'create'])->name('create');
            Route::post('store', [EstimationController::class,'store'])->name('store');
            Route::get('edit/{id}', [EstimationController::class,'edit'])->name('edit');
            Route::get('show/{id}', [EstimationController::class,'show'])->name('show');
            Route::post('status/{id}', [EstimationController::class,'status'])->name('status');
            Route::post('delete/{id}', [EstimationController::class, 'delete'])->name('delete');
            Route::get('/exportEstimationsExcel', [EstimationController::class,'exportExcel'])->name('exportExcel.estimationAll');
            Route::post('/uploadcontent', [EstimationController::class,'uploadContent'])->name('uploadcontent');
        });

        //Manage Suivi Menage
        Route::name('suivi.menage.')->prefix('menage')->group(function () {
            Route::get('list', [MenageController::class,'index'])->name('index');
            Route::get('create', [MenageController::class,'create'])->name('create');
            Route::post('store', [MenageController::class,'store'])->name('store');
            Route::get('edit/{id}', [MenageController::class,'edit'])->name('edit');
            Route::get('show/{id}', [MenageController::class,'show'])->name('show');
            Route::post('status/{id}', [MenageController::class,'status'])->name('status');
            Route::post('delete/{id}', [MenageController::class, 'delete'])->name('delete');
            Route::get('/exportMenagesExcel', [MenageController::class,'exportExcel'])->name('exportExcel.menageAll');
        });


        //Manage Suivi Parcelle
        Route::name('suivi.parcelles.')->prefix('suivi/parcelles')->group(function () {
            Route::get('list', [SuiviParcelleController::class,'index'])->name('index');
            Route::get('create', [SuiviParcelleController::class,'create'])->name('create');
            Route::post('store', [SuiviParcelleController::class,'store'])->name('store');
            Route::get('edit/{id}', [SuiviParcelleController::class,'edit'])->name('edit');
            Route::get('show/{id}', [SuiviParcelleController::class,'show'])->name('show');
            Route::post('status/{id}', [SuiviParcelleController::class,'status'])->name('status');
            Route::post('delete/{id}', [SuiviParcelleController::class, 'delete'])->name('delete');
            Route::get('/exportSuiviParcellesExcel', [SuiviParcelleController::class,'exportExcel'])->name('exportExcel.suiviParcelleAll');
        });

        //Manage Suivi Formation
        Route::name('suivi.formation.')->prefix('formation')->group(function () {
            Route::get('list', [FormationController::class,'index'])->name('index');
            Route::get('create', [FormationController::class,'create'])->name('create');
            Route::post('store', [FormationController::class,'store'])->name('store');
            Route::get('edit/{id}', [FormationController::class,'edit'])->name('edit');
            Route::get('show/{id}', [FormationController::class,'show'])->name('show');
            Route::post('status/{id}', [FormationController::class,'status'])->name('status');
            Route::get('/exportFormationsExcel', [FormationController::class,'exportExcel'])->name('exportExcel.formationAll');
            Route::post('delete/{id}', [FormationController::class, 'delete'])->name('delete');

            Route::get('visiteur/{id}', [FormationController::class,'visiteur'])->name('visiteur.visiteurs');
            Route::get('visiteur/create/{id}', [FormationController::class,'createvisiteur'])->name('visiteur.createvisiteur');
            Route::post('visiteur/store', [FormationController::class,'storevisiteur'])->name('visiteur.storevisiteur');
            Route::get('visiteur/edit/{id}', [FormationController::class,'editvisiteur'])->name('visiteur.editvisiteur');
        });



        //Manage Suivi Inspection
        Route::name('suivi.inspection.')->prefix('inspection')->group(function () {
            Route::get('list', [InspectionController::class,'index'])->name('index');
            Route::get('create', [InspectionController::class,'create'])->name('create');
            Route::post('store', [InspectionController::class,'store'])->name('store');
            Route::get('edit/{id}', [InspectionController::class,'edit'])->name('edit');
            Route::get('show/{id}', [InspectionController::class,'show'])->name('show');
            Route::post('status/{id}', [InspectionController::class,'status'])->name('status');
            Route::get('approbation', [InspectionController::class,'approbation'])->name('approbation');
            Route::get('certificat', [InspectionController::class,'getCertificat'])->name('getcertificat');
            Route::get('questionnaire', [InspectionController::class,'getQuestionnaire'])->name('getquestionnaire');
            Route::post('questionnaire/suivi', [InspectionController::class,'suiviStore'])->name('suiviStore');
            Route::post('delete/{id}', [InspectionController::class, 'delete'])->name('delete');
            Route::get('/exportInspectionsExcel', [InspectionController::class,'exportExcel'])->name('exportExcel.inspectionAll');
        });
        //Manage Suivi Application
        Route::name('suivi.application.')->prefix('application')->group(function () {
            Route::get('list', [ApplicationController::class,'index'])->name('index');
            Route::get('create', [ApplicationController::class,'create'])->name('create');
            Route::post('store', [ApplicationController::class,'store'])->name('store');
            Route::get('edit/{id}', [ApplicationController::class,'edit'])->name('edit');
            Route::get('show/{id}', [ApplicationController::class,'show'])->name('show');
            Route::post('status/{id}', [ApplicationController::class,'status'])->name('status');
            Route::post('/uploadcontent', [ApplicationController::class,'uploadContent'])->name('uploadcontent');
            Route::post('delete/{id}', [ApplicationController::class, 'delete'])->name('delete');
            Route::get('/exportApplicationsExcel', [ApplicationController::class,'exportExcel'])->name('exportExcel.applicationAll');
        });

        //Manage Suivi Ssrteclmrs
        Route::name('suivi.ssrteclmrs.')->prefix('ssrteclmrs')->group(function () {
            Route::get('list', [SsrteclmrsController::class,'index'])->name('index');
            Route::get('create', [SsrteclmrsController::class,'create'])->name('create');
            Route::post('store', [SsrteclmrsController::class,'store'])->name('store');
            Route::get('edit/{id}', [SsrteclmrsController::class,'edit'])->name('edit');
            Route::get('show/{id}', [SsrteclmrsController::class,'show'])->name('show');
            Route::post('status/{id}', [SsrteclmrsController::class,'status'])->name('status');
            Route::post('delete/{id}', [SsrteclmrsController::class, 'delete'])->name('delete');
            Route::get('/exportSsrteclmrsExcel', [SsrteclmrsController::class,'exportExcel'])->name('exportExcel.ssrteclmrsAll');
        });

        //Manage Agroapprovisionnements
        Route::name('agro.approvisionnement.')->prefix('agro/approvisionnement')->group(function () {
            Route::get('', [AgroapprovisionnementController::class,'index'])->name('index');
            Route::get('section', [AgroapprovisionnementController::class,'section'])->name('section');
            Route::get('create', [AgroapprovisionnementController::class,'create'])->name('create');
            Route::post('store', [AgroapprovisionnementController::class,'store'])->name('store');
            Route::get('edit/{id}', [AgroapprovisionnementController::class,'edit'])->name('edit');
            Route::get('create-section', [AgroapprovisionnementController::class,'create_section'])->name('create-section');
            Route::post('store-section', [AgroapprovisionnementController::class,'store_section'])->name('store-section');
            Route::post('update-section', [AgroapprovisionnementController::class,'update_section'])->name('update-section');
            Route::get('detail-section/{id}', [AgroapprovisionnementController::class,'show_section'])->name('show-section');
            Route::get('edit-section/{id}', [AgroapprovisionnementController::class,'edit_section'])->name('edit-section');
            Route::post('status/{id}', [AgroapprovisionnementController::class,'status'])->name('status');
            Route::post('delete/{id}', [AgroapprovisionnementController::class, 'delete'])->name('delete');
            Route::get('/exportApprovisionnementExcel', [AgroapprovisionnementController::class,'exportExcel'])->name('exportExcel.approvisionnementAll');
        });

        //Manage Agrodistributions
        Route::name('agro.distribution.')->prefix('agro/distribution')->group(function () {
            Route::get('list', [AgrodistributionController::class,'index'])->name('index');
            Route::get('create', [AgrodistributionController::class,'create'])->name('create');
            Route::post('store', [AgrodistributionController::class,'store'])->name('store');
            Route::post('update', [AgrodistributionController::class,'update'])->name('update');
            Route::get('edit/{id}', [AgrodistributionController::class,'edit'])->name('edit');
            Route::post('status/{id}', [AgrodistributionController::class,'status'])->name('status');
            Route::post('delete/{id}', [AgrodistributionController::class, 'delete'])->name('delete');
            Route::get('/exportDistributionsExcel', [AgrodistributionController::class,'exportExcel'])->name('exportExcel.distributionAll');
            Route::post('/get/agroparcelles/arbres', [AgrodistributionController::class,'getAgroParcellesArbres'])->name('getAgroParcellesArbres');
        });

         //Manage Agrodistributions
         Route::name('agro.postplanting.')->prefix('agro/postplanting')->group(function () {
            Route::get('list', [AgropostplantingController::class,'index'])->name('index');
            Route::get('create', [AgropostplantingController::class,'create'])->name('create');
            Route::post('store', [AgropostplantingController::class,'store'])->name('store');
            Route::post('update', [AgropostplantingController::class,'update'])->name('update');
            Route::get('edit/{id}', [AgropostplantingController::class,'edit'])->name('edit');
            Route::post('status/{id}', [AgropostplantingController::class,'status'])->name('status');
            Route::post('delete/{id}', [AgropostplantingController::class, 'delete'])->name('delete');
            Route::get('/exportPostplantingsExcel', [AgropostplantingController::class,'exportExcel'])->name('exportExcel.postplantingAll');
            Route::get('/get/agroparcelles/arbres', [AgropostplantingController::class,'getAgroParcellesArbres'])->name('getAgroParcellesArbres');
        });

        //Manage Agroevaluations
        Route::name('agro.evaluation.')->prefix('agro/evaluation')->group(function () {
            Route::get('list', [AgroevaluationController::class,'index'])->name('index');
            Route::get('create', [AgroevaluationController::class,'create'])->name('create');
            Route::post('store', [AgroevaluationController::class,'store'])->name('store');
            Route::get('destroy/{id}', [AgroevaluationController::class,'destroy'])->name('destroy');
            Route::get('edit/{id}', [AgroevaluationController::class,'edit'])->name('edit');
            Route::get('show/{id}', [AgroevaluationController::class,'show'])->name('show');
            Route::post('status/{id}', [AgroevaluationController::class,'status'])->name('status');
            Route::post('delete/{id}', [AgroevaluationController::class, 'delete'])->name('delete');
            Route::get('/exportEvaluationsExcel', [AgroevaluationController::class,'exportExcel'])->name('exportExcel.evaluationsAll');
        });

                //Manage Communauté résiliente
        Route::name('communaute.')->prefix('communaute/resiliente')->group(function () {

            // Action communautaire
            Route::get('action/sociale/list', [ActionSocialeController::class,'index'])->name('action.sociale.index');
            Route::get('action/sociale/create', [ActionSocialeController::class,'create'])->name('action.sociale.create');
            Route::post('action/sociale/store', [ActionSocialeController::class,'store'])->name('action.sociale.store');
            Route::get('action/sociale/destroy/{id}', [ActionSocialeController::class,'destroy'])->name('action.sociale.destroy');
            Route::get('action/sociale/edit/{id}', [ActionSocialeController::class,'edit'])->name('action.sociale.edit');
            Route::get('action/sociale/show/{id}', [ActionSocialeController::class,'show'])->name('action.sociale.show');
            Route::post('action/sociale/status/{id}', [ActionSocialeController::class,'status'])->name('action.sociale.status');
            Route::get('action/sociale/exportActionSocialeExcel', [ActionSocialeController::class,'exportExcel'])->name('action.sociale.exportExcel.actionSocialeAll');
            Route::post('delete/{id}', [ActionSocialeController::class, 'delete'])->name('delete');
            Route::post('action/sociale/generCode', [ActionSocialeController::class,'generCode'])->name('action.sociale.generCode');

        // Activite communautaire
            Route::get('activite/communautaire/list', [ActiviteCommunautaireController::class,'index'])->name('activite.communautaire.index');
            Route::get('activite/communautaire/create', [ActiviteCommunautaireController::class,'create'])->name('activite.communautaire.create');
            Route::post('activite/communautaire/store', [ActiviteCommunautaireController::class,'store'])->name('activite.communautaire.store');
            Route::get('activite/communautaire/destroy/{id}', [ActiviteCommunautaireController::class,'destroy'])->name('activite.communautaire.destroy');
            Route::get('activite/communautaire/edit/{id}', [ActiviteCommunautaireController::class,'edit'])->name('activite.communautaire.edit');
            Route::get('activite/communautaire/show/{id}', [ActiviteCommunautaireController::class,'show'])->name('activite.communautaire.show');
            Route::post('activite/communautaire/status/{id}', [ActiviteCommunautaireController::class,'status'])->name('activite.communautaire.status');
            Route::get('non-membre/{id}', [ActiviteCommunautaireController::class,'nonmembre'])->name('nonmembre.nonmembre');
            Route::get('non-membre/create/{id}', [ActiviteCommunautaireController::class,'createnonmembre'])->name('nonmembre.createnonmembre');
            Route::post('non-membre/store', [ActiviteCommunautaireController::class,'storenonmembre'])->name('nonmembre.storenonmembre');
            Route::get('non-membre/edit/{id}', [ActiviteCommunautaireController::class,'editnonmembre'])->name('nonmembre.editnonmembre');
            Route::get('activite/communautaire/exportActiviteCommunautaireExcel', [ActiviteCommunautaireController::class,'exportExcel'])->name('activite.communautaire.exportExcel.activiteCommunautaireAll');
            Route::post('delete/{id}', [ActiviteCommunautaireController::class, 'delete'])->name('delete');
        });

        //Manage Agrodeforestations
        Route::name('agro.deforestation.')->prefix('agro/deforestation')->group(function () {
            Route::get('polygones', [AgrodeforestationController::class,'index'])->name('index');
            Route::get('waypoints', [AgrodeforestationController::class,'waypoints'])->name('waypoints');  
        });

        //Manage Livraison
        Route::name('livraison.')->prefix('livraison')->group(function () {
            Route::get('magcentral/stock', [LivraisonCentraleController::class,'stock'])->name('magcentral.stock');
            Route::post('magcentral/delivery', [LivraisonCentraleController::class,'deliveryStore'])->name('magcentral.delivery');
            Route::get('magcentral/invoice/{id}', [LivraisonCentraleController::class,'invoice'])->name('magcentral.invoice');
            Route::get('magcentral/producteur', [LivraisonCentraleController::class,'getProducteur'])->name('magcentral.get.producteur');
            Route::get('magcentral/producteur/liste', [LivraisonCentraleController::class,'getListeProducteurConnaiss'])->name('magcentral.get.listeproducteur');
            Route::get('magcentral/connaissement', [LivraisonCentraleController::class,'connaissement'])->name('usine.connaissement');
            Route::post('magcentral/usine/delivery', [LivraisonCentraleController::class,'deliveryUsineStore'])->name('usine.delivery');
            Route::post('magcentral/usine/refoule', [LivraisonCentraleController::class,'refouleUsineStore'])->name('usine.refoule');
            Route::get('magcentral/usine/invoice/{id}', [LivraisonCentraleController::class,'usineInvoice'])->name('usine.invoice');
            Route::get('magcentral/prime', [LivraisonCentraleController::class,'prime'])->name('prime.producteur');
            Route::post('magcentral/prime', [LivraisonCentraleController::class,'deliveryPrimeStore'])->name('prime.delivery');
            Route::get('magcentral/prime/invoice', [LivraisonCentraleController::class,'primeInvoice'])->name('prime.invoice');
            Route::get('magcentral/usine/suivi/{id}', [LivraisonCentraleController::class,'suiviLivraison'])->name('usine.suivi');
            Route::post('magcentral/suivi/store', [LivraisonCentraleController::class,'suiviStore'])->name('magcentral.suivi.store');
            Route::get('/export/stock/magasin/central', [LivraisonCentraleController::class,'exportExcel'])->name('exportExcel.magcentralAll');
            Route::resource('magcentral', LivraisonCentraleController::class); 
            Route::get('send', [LivraisonController::class,'create'])->name('create');
            Route::post('store', [LivraisonController::class,'store'])->name('store');
            Route::post('update/{id}', [LivraisonController::class,'update'])->name('update');
            Route::get('edit/{id}', [LivraisonController::class,'edit'])->name('edit');
            Route::get('list', [LivraisonController::class,'livraisonInfo'])->name('index');
            Route::get('stock', [LivraisonController::class,'stockSection'])->name('stock.section');
            Route::post('stock/store', [LivraisonController::class,'sectionStore'])->name('section.store');
            Route::get('stock/create', [LivraisonController::class,'stockSectionCreate'])->name('stock.section.create');
            Route::get('parcelle', [LivraisonController::class,'getParcelle'])->name('get.parcelle');
            Route::get('producteur', [LivraisonController::class,'getProducteur'])->name('get.producteur');
            Route::get('certificat', [LivraisonController::class,'getCertificat'])->name('get.certificat');
            Route::get('producteur/liste', [LivraisonController::class,'getListeProducteurConnaiss'])->name('get.listeproducteur');
            Route::get('dispatch/list', [LivraisonController::class,'dispatchLivraison'])->name('dispatch');
            Route::get('upcoming/list', [LivraisonController::class,'upcoming'])->name('upcoming');
            Route::get('sent-queue/list', [LivraisonController::class,'sentInQueue'])->name('sentQueue');
            Route::get('delivery-queue/list', [LivraisonController::class,'deliveryInQueue'])->name('deliveryInQueue');
            Route::get('delivered', [LivraisonController::class,'delivered'])->name('delivered');
            Route::get('search', [LivraisonController::class,'livraisonSearch'])->name('search');
            Route::get('invoice/{id}', [LivraisonController::class,'invoice'])->name('invoice');
            Route::get('sent', [LivraisonController::class,'sentLivraison'])->name('sent');
            Route::get('/export/stock/magasin/section', [LivraisonController::class,'exportExcel'])->name('exportExcel.livraisonAll');
            
        });
 

        Route::name('ticket.')->prefix('ticket')->group(function () {
            Route::get('/', [ManagerTicketController::class,'supportTicket'])->name('index');
            Route::get('/new', [ManagerTicketController::class,'openSupportTicket'])->name('open');
            Route::post('/create', [ManagerTicketController::class,'storeSupportTicket'])->name('store');
            Route::get('/view/{ticket}', [ManagerTicketController::class,'viewTicket'])->name('view');
            Route::post('/reply/{ticket}', [ManagerTicketController::class,'replyTicket'])->name('reply');
            Route::post('/close/{ticket}', [ManagerTicketController::class,'closeTicket'])->name('close');
            Route::get('/download/{ticket}', [ManagerTicketController::class,'ticketDownload'])->name('download');
        });
    });
});
