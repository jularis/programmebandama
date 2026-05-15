<?php
namespace App\Http\Controllers\Manager;
use App\Models\Team;

use App\Models\User;
use App\Models\BaseModel;
use App\Http\Helpers\Reply;
use Illuminate\Http\Request;
use App\Models\EmployeeDetail;
use App\Models\EmployeeDetails;
use App\DataTables\DepartmentDataTable;
use App\Http\Requests\Team\StoreDepartment;
use App\Http\Requests\Team\UpdateDepartment;
use App\Http\Controllers\AccountBaseController;

class DepartmentController extends AccountBaseController
{
    public $arr = [];

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.department');

        $this->middleware(function ($request, $next) {
             

            return $next($request);
        });
    }

    /**
     * @param DepartmentDataTable $dataTable
     * @return mixed|void
     */

    public function index(DepartmentDataTable $dataTable)
    {
         
        $this->departments = Team::with('childs')->get();

        return $dataTable->render('manager.departments.index', $this->data);
    }

    public function create()
    {
        $this->departments = Team::allDepartments();

        if (request()->ajax()) {
            $html = view('manager.departments.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.departments.ajax.create';

        return view('manager.departments.create', $this->data);
    }

    /**
     * @param StoreDepartment $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreDepartment $request)
    {

        $group = new Team();
        $group->department = $request->department;
        $group->cooperative_id = auth()->user()->cooperative_id;
        $group->parent_id = $request->parent_id;
        $group->save();

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('manager.departments.index');
        }
         
        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl]);
    }

    public function show($id)
    {
        $this->department = Team::find($id);
        $this->parent = Team::where('id', $this->department->parent_id)->first();

        if (request()->ajax()) {
            $html = view('manager.departments.ajax.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.departments.ajax.show';

        return view('manager.departments.create', $this->data);
    }

    public function edit($id)
    {
        $this->department = Team::findOrFail($id);
        $departments = Team::where('id', '!=', $this->department->id)->get();

        $childDepartments = $departments->where('parent_id', $this->department->id)->pluck('id')->toArray();

        $departments = $departments->where('parent_id', '!=', $this->department->id);

        // remove child departments
        $this->departments = $departments->filter(function ($value, $key) use ($childDepartments) {
            return !in_array($value->parent_id, $childDepartments);
        });

        if (request()->ajax()) {
            $html = view('manager.departments.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.departments.ajax.edit';

        return view('manager.departments.create', $this->data);
    }

    /**
     * @param UpdateDepartment $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(UpdateDepartment $request, $id)
    { 

        $group = Team::findOrFail($id);
        $group->department = strip_tags($request->department);
        $group->cooperative_id = auth()->user()->cooperative_id;
        $group->parent_id = $request->parent_id ?? null;
        $group->save();

        $redirectUrl = route('manager.departments.index');

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => $redirectUrl]);
    }

    public function destroy($id)
    { 

        EmployeeDetail::where('department_id', $id)->update(['department_id' => null]);
        $department = Team::where('parent_id', $id)->get();
        $parent = Team::findOrFail($id);

        if (count($department) > 0) {
            foreach ($department as $item) {
                $child = Team::findOrFail($item->id);
                $child->parent_id = $parent->parent_id;
                $child->save();
            }
        }

        Team::destroy($id);

        $redirectUrl = route('manager.departments.index');

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => $redirectUrl]);
    }

    public function applyQuickAction(Request $request)
    {
        if ($request->action_type == 'delete') {
            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));

    }

    protected function deleteRecords($request)
    {
        

        $item = explode(',', $request->row_ids);

        if (($key = array_search('on', $item)) !== false) {
            unset($item[$key]);
        }

        foreach($item as $id)
        {
            EmployeeDetail::where('department_id', $id)->update(['department_id' => null]);
            $department = Team::where('parent_id', $id)->get();
            $parent = Team::findOrFail( $id);

            if (count($department) > 0) {
                foreach ($department as $item) {
                    $child = Team::findOrFail($item->id);
                    $child->parent_id = $parent->parent_id;
                    $child->save();
                }
            }

            Team::where('id', $id)->delete();
        }

    }

    public function hierarchyData()
    {
         
        $this->pageTitle = 'Department Hierarchy';
        $this->chartDepartments = Team::get(['id', 'department', 'parent_id']);
        $this->departments = Team::with('childs', 'childs.childs')->where('parent_id', null)->get();

        if (request()->ajax()) {
            return Reply::dataOnly(['status' => 'success', 'departments' => $this->departments]);
        }

        return view('manager.departments-hierarchy.index', $this->data);
    }

    public function changeParent()
    {
         

        $childIds = request('values');
        $parentId = request('newParent') ? request('newParent') : request('parent_id');

        $department = Team::findOrFail($parentId);

        // Root node again
        if (request('newParent') && $department) {
            $department->parent_id = null;
            $department->save();
        }
        else if ($department && !is_null($childIds)) // update child Node
        {
            foreach ($childIds as $childId) {
                $child = Team::findOrFail($childId);

                if ($child) {
                    $child->parent_id = $parentId;
                    $child->save();
                }

            }
        }

        $this->chartDepartments = Team::get(['id', 'department', 'parent_id']);
        $this->departments = Team::with('childs')->where('parent_id', null)->get();
        $html = view('manager.departments-hierarchy.chart_tree', $this->data)->render();
        $organizational = view('manager.departments-hierarchy.chart_organization', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'organizational' => $organizational]);
    }

    // Search filter start

    public function searchDepartment(Request $request)
    {
        $text = $request->searchText;

        if ($text != '' && strlen($text) > 2) {
            $searchParent = Team::with('childs')->where('department', 'like', '%' . $text . '%')->get();

            $id = [];

            foreach ($searchParent as $item) {
                array_push($id, $item->parent_id);
            }

            $item = $searchParent->whereIn('id', $id)->pluck('id');
            $this->chartDepartments = $searchParent;

            if ($text != '' && !is_null($item)) {
                foreach ($this->chartDepartments as $item) {
                    $item['parent_id'] = null;
                }
            }

            $parent = array();

            foreach ($this->chartDepartments as $department) {
                array_push($parent, $department->id);

                if ($department->childs) {
                    $this->child($department->childs);
                }
            }

            $this->children = Team::whereIn('id', $this->arr)->get(['id', 'department', 'parent_id']);
            $this->parents = Team::whereIn('id', $parent)->get(['id', 'department']);
            $this->chartDepartments = $this->parents->merge($this->children);
        }
        else {
            $this->chartDepartments = Team::get(['id', 'department', 'parent_id']);

        }

        $this->departments = ($text != '') ? Team::with('childs')->where('department', 'like', '%' . $text . '%')->get() : Team::with('childs')->where('parent_id', null)->get();
        $html = view('manager.departments-hierarchy.chart_tree', $this->data)->render();
        $organizational = view('manager.departments-hierarchy.chart_organization', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'organizational' => $organizational]);
    }

    public function child($child)
    {
        foreach ($child as $item) {
            array_push($this->arr, $item->id);

            if ($item->childs) {
                $this->child($item->childs);
            }
        }
    }

    // Search filter end

    public function getMembers($id)
    {

        $options = '';
        $userData = [];

        if ($id == 0) {
            $members = User::allEmployees();

            foreach ($members as $item) {
                $self_select = (user() && user()->id == $item->id) ? '<span class=\'ml-2 badge badge-secondary\'>' . __('app.itsYou') . '</span>' : '';

                $options .= '<option  data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div></span>  ' . $item->name . '' . $self_select . '" value="' . $item->id . '"> ' . $item->name . '</option>';
            }
        }
        else {
            $members = User::departmentUsers($id);

            foreach ($members as $item) {

                $self_select = (user() && user()->id == $item->id) ? '<span class=\'ml-2 badge badge-secondary\'>' . __('app.itsYou') . '</span>' : '';

                $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div>  ' . $item->name . '' . $self_select . '" value="' . $item->id . '"> ' . $item->name . ' </option>';
                $url = route('employees.show', [$item->id]);

                $userData[] = ['id' => $item->id, 'value' => $item->name, 'image' => $item->image_url, 'link' => $url];

            }
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options, 'userData' => $userData]);
    }

}
