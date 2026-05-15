@include('import.process-form', [
    'headingTitle' => __('app.importExcel') . ' ' . __('app.menu.attendance'),
    'processRoute' => route('manager.hr.attendances.import.process'),
    'backRoute' => route('manager.hr.attendances.index'),
    'backButtonText' => __('app.backToAttendance'),
])
