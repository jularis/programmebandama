@include('import.process-form', [
    'headingTitle' => __('app.importExcel') . ' ' . __('app.employee'),
    'processRoute' => route('manager.hr.employees.import.process'),
    'backRoute' => route('manager.hr.employees.index'),
    'backButtonText' => __('app.backToEmployees'),
])
