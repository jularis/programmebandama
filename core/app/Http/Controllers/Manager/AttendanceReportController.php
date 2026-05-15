<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;

use App\DataTables\AttendanceReportDataTable;
use App\Models\User;

class AttendanceReportController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.attendanceReport';
    }

    public function index(AttendanceReportDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->fromDate = now($this->cooperative->timezone)->startOfMonth();
            $this->toDate = now($this->cooperative->timezone);
            $this->employees = User::allEmployees();
        }

        return $dataTable->render('reports.attendance.index', $this->data);
    }

}
