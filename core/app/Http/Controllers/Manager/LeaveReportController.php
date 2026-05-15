<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;
use App\DataTables\LeaveReportDataTable;
use App\Http\Helpers\Reply;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveReportController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaveReport';
    }

    public function index(LeaveReportDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->employees = User::allEmployees(null, true);
            $this->fromDate = now($this->cooperative->timezone)->startOfMonth();
            $this->toDate = now($this->cooperative->timezone)->endOfMonth();
        }

        return $dataTable->render('reports.leave.index', $this->data);
    }

    public function show(Request $request, $id)
    {
        $this->userId = $id;
        $view = $request->view;

        $this->leave_types = LeaveType::with(['leaves' => function ($query) use ($request, $id, $view) {
            if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
                $this->startDate = $request->startDate;
                $startDate = Carbon::createFromFormat($this->cooperative->date_format, $request->startDate)->toDateString();
                $query->where(DB::raw('DATE(leaves.`leave_date`)'), '>=', $startDate);
            }

            if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
                $this->endDate = $request->endDate;
                $endDate = Carbon::createFromFormat($this->cooperative->date_format, $request->endDate)->toDateString();
                $query->where(DB::raw('DATE(leaves.`leave_date`)'), '<=', $endDate);
            }

            switch ($view) {
            case 'pending':
                $query->where('status', 'pending')->where('user_id', $id);
                    break;
            case 'upcoming':
                $query->where('leave_date', '>', now($this->cooperative->timezone)->format('Y-m-d'));
                $query->where('status', '<>', 'rejected')->where('user_id', $id);
                    break;
            default:
                $query->where('status', 'approved')->where('user_id', $id);
                    break;
            }
        }, 'leaves.type'])->get();

        if (request()->ajax() && $view != '') {
            $html = view('reports.leave.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('reports.leave.show', $this->data);
    }

}
