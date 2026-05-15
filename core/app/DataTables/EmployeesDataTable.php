<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BaseModel;
use App\Models\CustomField;
use App\Scopes\ActiveScope;
use App\Models\EmployeeDetail;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeesDataTable extends BaseDataTable
{
 

    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
       
            $datatables = datatables()->eloquent($query)->filter(function ($query) {
                if (request()->status != 'all' && request()->status != '') {
                $query->where('users.status', request()->status); 
                }
                if (request()->employee != 'all' && request()->employee != '') {
                $query->where('users.id', request()->employee);
                }
                if (request()->employee != 'all' && request()->employee != '') {
                    $query->where('users.id', request()->employee);
                }
        
                if (request()->designation != 'all' && request()->designation != '') {
                    $query->where('employee_details.designation_id', request()->designation);
                }
        
                if (request()->department != 'all' && request()->department != '') {
                    $query->where('employee_details.department_id', request()->department);
                }
        
                 
                if ((is_array(request()->skill) && request()->skill[0] != 'all') && request()->skill != '' && request()->skill != null && request()->skill != 'null') {
                    $query->join('employee_skills', 'employee_skills.user_id', '=', 'users.id')
                        ->whereIn('employee_skills.skill_id', request()->skill);
                }
         
        
                if (request()->startDate != '' && request()->endDate != '') {
                    $startDate = Carbon::createFromFormat('Y-m-d', request()->startDate)->toDateString();
                    $endDate = Carbon::createFromFormat('Y-m-d', request()->endDate)->toDateString();
        
                    $query->whereRaw('Date(employee_details.joining_date) >= ?', [$startDate])->whereRaw('Date(employee_details.joining_date) <= ?', [$endDate]);
                }
        
                if (request()->status == 'ex_employee' && isset(request()->lastStartDate) && isset(request()->lastEndDate) && request()->lastStartDate != '' && request()->lastEndDate != '') {
                    $startDate = Carbon::createFromFormat('Y-m-d', request()->lastStartDate)->toDateString();
                    $endDate = Carbon::createFromFormat('Y-m-d', request()->lastEndDate)->toDateString();
                    $query->whereNotNull('last_date')->whereRaw('Date(employee_details.last_date) >= ?', [$startDate])->whereRaw('Date(employee_details.last_date) <= ?', [$endDate]);
                }
        
                if (request()->searchText != '') {
                    $query->where(function ($query) {
                        $query->where('users.lastname', 'like', '%' . request()->searchText . '%')
                            ->orWhere('users.email', 'like', '%' . request()->searchText . '%')
                            ->orWhere('employee_details.employee_id', 'like', '%' . request()->searchText . '%');
                    });
                }
        
           }); 
            

        $datatables->addColumn('check', function ($row) {
           
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            
        });

        $datatables->addColumn('action', function ($row) {
             
            $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

            $action .= '<a href="' . route('manager.employees.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

           
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('manager.employees.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
               

           
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
               

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });
        $datatables->addColumn('employee_name', function ($row) {
            return $row->name;
        });

        $datatables->editColumn(
            'created_at',
            function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('Y-m-d');
            }
        );
        $datatables->editColumn(
            'status',
            function ($row) {
                if ($row->status == 1) {
                    return ' <i class="fa fa-circle mr-1 text-light-green f-10"></i>' . __('app.active');
                }

                return '<i class="fa fa-circle mr-1 text-red f-10"></i>' . __('app.inactive');

            }
        );
        $datatables->editColumn('name', function ($row) {
            return view('components.employee', [
                'user' => $row
            ]);
        });
        $datatables->editColumn('employee_id', function ($row) {
            return '<a href="' . route('manager.employees.show', [$row->id]) . '" class="text-darkest-grey">' . $row->employee_id . '</a>';
        });
        $datatables->editColumn('joining_date', function ($row) {
            return Carbon::parse($row->joining_date)->translatedFormat('Y-m-d');
        });
        $datatables->addIndexColumn();
        $datatables->setRowId(function ($row) {
            return 'row-' . $row->id;
        });
        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatables, EmployeeDetail::CUSTOM_FIELD_MODEL, 'employeeDetail');

        $datatables->rawColumns(array_merge(['name', 'action', 'status', 'check', 'employee_id'], $customFieldColumns));

        return $datatables;
    }

    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $request = $this->request();

        $users = $model->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->select('users.id', 'employee_details.added_by', 'users.lastname', 'users.firstname', 'users.email', 'users.created_at', 'users.image', 'users.genre', 'users.status',  'designations.name as designation_name', 'employee_details.employee_id', 'employee_details.joining_date')
            ->where('users.cooperative_id', auth()->user()->cooperative_id);
      
        return $users->groupBy('users.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('employees-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["employees-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $data = [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => false],
            __('modules.employees.employeeId') => ['data' => 'employee_id', 'name' => 'employee_id', 'title' => __('modules.employees.employeeId')],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'exportable' => false, 'title' => __('app.name')],
            __('app.employee') => ['data' => 'employee_name', 'name' => 'name', 'visible' => false, 'title' => __('app.employee')],
            __('app.email') => ['data' => 'email', 'name' => 'email', 'title' => __('app.email')],
            __('modules.employees.joiningDate') => ['data' => 'joining_date', 'name' => 'joining_date', 'visible' => false, 'title' => __('modules.employees.joiningDate')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('app.status')]
        ];

        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data, CustomFieldGroup::customFieldsDataMerge(new EmployeeDetail()), $action);

    }

}
