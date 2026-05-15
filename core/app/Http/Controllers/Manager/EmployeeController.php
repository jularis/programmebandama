<?php
namespace App\Http\Controllers\Manager;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Leave;
use App\Models\Skill;
use App\Models\Module;
use App\Models\Ticket;
use App\Models\Countrie;
use App\Models\Passport;
use App\Models\RoleUser;
use App\Models\UserAuth;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\VisaDetail;
use App\Http\Helpers\Files;
use App\Http\Helpers\Reply;
use App\Models\Cooperative;
use App\Models\Designation;
use App\Scopes\ActiveScope;
use App\Traits\ImportExcel;
use Illuminate\Support\Str;
use App\Models\Appreciation;
use App\Models\Notification;
use App\Models\UserActivity;
use App\Models\EmployeeSkill;
use App\Models\EmployeeDetail;
use App\Models\ProjectTimeLog;
use App\Models\UserInvitation;
use App\Imports\EmployeeImport;
use App\Jobs\ImportEmployeeJob;
use App\Models\EmployeeDetails;
use App\Models\LanguageSetting;
use App\Models\TaskboardColumn;
use App\Models\UniversalSearch;
use App\Scopes\CooperativeScope;
use App\DataTables\LeaveDataTable;
use App\DataTables\TasksDataTable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\DataTables\TicketDataTable;
use App\Models\EmployeeContratFile;
use App\Models\PackageUpdateNotify;
use App\Models\ProjectTimeLogBreak;
use Illuminate\Support\Facades\Hash;
use App\DataTables\ProjectsDataTable;
use App\DataTables\TimeLogsDataTable;
use App\Models\EmployeeAutredocument;
use App\DataTables\EmployeesDataTable;

use App\Models\EmployeeFicheposteFile;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\User\InviteEmailRequest;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\Admin\Employee\StoreRequest;
use App\Http\Requests\Admin\Employee\ImportRequest;
use App\Http\Requests\Admin\Employee\UpdateRequest;
use App\Http\Requests\User\CreateInviteLinkRequest;
use App\Http\Requests\Admin\Employee\ImportProcessRequest;
use Symfony\Component\Mailer\Exception\TransportException;

class EmployeeController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    {
        $this->pageTitle = 'Gestion des employées'; 
    }

    /**
     * @param EmployeesDataTable $dataTable
     * @return mixed|void
     */
    public function index(EmployeesDataTable $dataTable)
    {  
       
        if(!request()->ajax()) {

            $this->employees = User::where('cooperative_id',auth()->user()->cooperative_id)->with('employeeDetail')->get();
            $this->skills = Skill::all();
            $this->departments = Department::where('cooperative_id',auth()->user()->cooperative_id)->get();
            $this->designations = Designation::where('cooperative_id',auth()->user()->cooperative_id)->get();
            
            $this->totalEmployees = 0; 
         
        }
        
        return $dataTable->render('manager.employees.index', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->pageTitle = __('app.addEmployee'); 

        $this->teams = Department::all();
        $this->designations = Designation::allDesignations();

        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->countries = Countrie::get();
        
        $this->lastEmployeeID = EmployeeDetail::count();
        $this->checkifExistEmployeeId =  EmployeeDetail::select('id')->where('employee_id', ($this->lastEmployeeID + 1))->first();
         
        $this->employees = User::allEmployees(null, true,null, cooperative()->id); 
   
        if (request()->ajax()) {
            $html = view('manager.employees.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]); 
        }

        return view('manager.employees.create', $this->data);

    }
 

    /**
     * @param StoreRequest $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreRequest $request)
    { 
         
        DB::beginTransaction();
        try { 

            $manager = auth()->user();
            $user = new User();
            $user->cooperative_id = $manager->cooperative_id;
            $user->lastname = $request->lastname;
            $user->firstname = $request->firstname; 
            $user->username  = Str::limit(Str::slug($request->firstname,""),12,'.').Str::limit(Str::slug($request->lastname,""),1,'');
            $user->email  = $request->email;
            $user->mobile = $request->mobile; 
            $user->adresse = $request->address; 
            $user->country_id = $request->country;
            $user->country_phonecode = $request->country_phonecode;
            $user->genre = $request->gender;
            $user->user_type = "staff"; 
            
            $user->type_compte = "web";  
            $user->password  =  Hash::make('azerty'); 
            // $user->user_auth_id = $userAuth->id;
            
            if ($request->hasFile('image')) {
                Files::deleteFile($user->image, 'avatar');
                $user->image = Files::uploadLocalOrS3($request->image, 'avatar', 300);
            }

            
            $lastEmployeeID = EmployeeDetail::where('cooperative_id', auth()->user()->cooperative_id)->count();
        // $this->checkifExistEmployeeId =  EmployeeDetail::select('id')->where('employee_id', ($lastEmployeeID + 1))->first();
        
            if($lastEmployeeID){
                $lastEmployeeID = $lastEmployeeID+1;
            $employeeid = 'EMP-'.$lastEmployeeID;
            }else{
                $employeeid ="EMP-1";
            }
  
            $user->save();
            

            if($user->id){
                 
                $role = DB::table('roles')->where('name',$request->designation)->first();
                if($role !=null)
                {
                    $user->syncRoles($role->id);
                }
                
            }
            
            if ($request->hasFile('contrat_travail')) {
               
                $files = $request->file('contrat_travail');
                foreach ($files as $fileData) {
                    $file = new EmployeeContratFile(); 
                    $file->fichier =  $fileData->store('public/contratsTravail'); 
                    $file->user_id = $user->id;
                    $file->save();
                }
            }
            if ($request->hasFile('fiche_poste')) {
                $files = $request->file('fiche_poste');
                foreach ($files as $fileData) {
                    $file = new EmployeeFicheposteFile(); 
                    $file->fichier =  $fileData->store('public/fichesPoste'); 
                    $file->user_id = $user->id;
                    $file->save();
                }
            }
            if ($request->hasFile('autre_document')) {
                $files = $request->file('autre_document');
                foreach ($files as $fileData) {
                    $file = new EmployeeAutredocument(); 
                    $file->fichier =  $fileData->store('public/autresDocument'); 
                    $file->user_id = $user->id;
                    $file->save();
                }
            }

            $tags = json_decode($request->tags);

            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    // check or store skills
                    $skillData = Skill::firstOrCreate(['name' => $tag->value]);

                    // Store user skills
                    $skill = new EmployeeSkill();
                    $skill->user_id = $user->id;
                    $skill->skill_id = $skillData->id;
                    $skill->save();
                }
            }
            
            if ($user->id) {
                $employee = new EmployeeDetail();
                $employee->user_id = $user->id; 
                $employee->employee_id =  $employeeid;
                $employee->cooperative_id = $manager->cooperative_id;
                $this->employeeData($request, $employee);
                $employee->save();
                
                // To add custom fields data
                if ($request->custom_fields_data) {
                    $employee->updateCustomFieldData($request->custom_fields_data);
                }
            } 

            // $this->logSearchEntry($user->id, $user->lastname.' '.$user->firstname, 'manager.employees.show', 'employee');
            
            // Commit Transaction
            DB::commit();
 

        }catch (\Exception $e) {
            logger($e->getMessage());
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support '. $e->getMessage());
        }


       return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('manager.employees.index')]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);
            // WORKSUITESAAS
            session()->forget('cooperative');
            return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            $cooperative = Cooperative::with(['employees'])->where('id', user()->cooperative_id)->first();

            $updateIds = explode(',', str_replace('on,', '', $request->row_ids));

            if ($request->status == 'active' && !is_null($cooperative->employees) && ($cooperative->employees->count() + count($updateIds)) > $cooperative->package->max_employees) {
                return Reply::error(__('superadmin.maxEmployeesLimitReached'));
            }

            $this->changeStatus($request);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    private function deleteEmployee(User $user)
    {
 
        $user->delete();

    }

    protected function deleteRecords($request)
    {
         

        $users = User::withoutGlobalScope(ActiveScope::class)->whereIn('id', explode(',', $request->row_ids))->get();

        // $users->each(function ($user) {
        //     $this->deleteEmployee($user);
        // });

    }

    protected function changeStatus($request)
    { 
        User::withoutGlobalScope(ActiveScope::class)->whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);
        clearCooperativeValidPackageCache(user()->cooperative_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = User::withoutGlobalScope(ActiveScope::class)->with('employeeDetail', 'reportingTeam')->findOrFail($id);
        $emailCountInCompanies = User::withoutGlobalScopes([ActiveScope::class, CooperativeScope::class])
            ->where('email', $employee->email)
            ->whereNotNull('email')
            ->count();
         

        $this->pageTitle = __('app.update') . ' ' . __('app.employee');
        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->teams = Team::allDepartments();
        $this->designations = Designation::allDesignations();
        $this->countries = Countrie::get(); 
        $exceptUsers = [$id]; 

        /** @phpstan-ignore-next-line */
        // if (count($employee->reportingTeam) > 0) { 
        //     $exceptUsers = array_merge($this->employee->reportingTeam->pluck('user_id')->toArray(), $exceptUsers);
        // }

        $this->employees = User::allEmployees($exceptUsers, true);
        $this->employee = $employee;
        $this->emailCountInCompanies = $emailCountInCompanies;
        if (!is_null($employee->employeeDetail)) {
            $this->employeeDetail = $employee->employeeDetail->withCustomFields();
 
        }
        
        if (request()->ajax()) {
            $html = view('manager.employees.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.employees.ajax.edit';

        return view('manager.employees.create', $this->data);

    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateRequest $request, $id)
    {
        
        $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($id);
        $emailCountInCompanies = User::withoutGlobalScopes([ActiveScope::class, CooperativeScope::class])
            ->where('email', $user->email)
            ->count();
        
        if ($emailCountInCompanies > 1 && $request->email != $user->email) {
            return Reply::error(__('messages.emailCannotChange'));
        }
        
        $userAuth = UserAuth::createUserAuthCredentials($user->username, $request->email, null, $user->email);
        
        if(!$userAuth){ 
            $user->userAuth->update(['email' => $request->email]);
        }
        
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;

        $user->mobile = $request->mobile;
        $user->country_id = $request->country;
        $user->country_phonecode = $request->country_phonecode;
        $user->genre = $request->gender; 

        if (request()->has('status')) {

            if (request()->status == 'active' && !checkCooperativeCanAddMoreEmployees(auth()->user()->cooperative_id) && $user->status != 'active') {
                return Reply::error(__('superadmin.maxEmployeesLimitReached'));
            }

            $user->status = $request->status;
            // PackageUpdateNotify::where('cooperative_id', user()->cooperative_id)->where('user_id', $user->id)->delete();
        }
 
        if ($request->image_delete == 'yes') {
            Files::deleteFile($user->image, 'avatar');
            $user->image = null;
        }

        if ($request->hasFile('image')) {

            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::uploadLocalOrS3($request->image, 'avatar', 300);
        }
 
        $user->save();

        cache()->forget('user_is_active_' . $user->id);

        

        $tags = json_decode($request->tags);

        if (!empty($tags)) {
            EmployeeSkill::where('user_id', $user->id)->delete();

            foreach ($tags as $tag) {
                // Check or store skills
                $skillData = Skill::firstOrCreate(['name' => $tag->value]);

                // Store user skills
                $skill = new EmployeeSkill();
                $skill->user_id = $user->id;
                $skill->skill_id = $skillData->id;
                $skill->save();
            }
        }

        $employee = EmployeeDetail::where('user_id', '=', $user->id)->first();

        if (empty($employee)) {
            $employee = new EmployeeDetail();
            $employee->user_id = $user->id;
        }

        $this->employeeData($request, $employee);

        $employee->last_date = null;

        if ($request->last_date != '') {
            $employee->last_date = Carbon::createFromFormat('Y-m-d', $request->last_date)->format('Y-m-d');
        }

        $employee->save();

        // To add custom fields data
        // if ($request->custom_fields_data) {
        //     $employee->updateCustomFieldData($request->custom_fields_data);
        // }

        if (user()->id == $user->id) {
            session()->forget('user');
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('employees.index')]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($id); 
  
        $this->deleteEmployee($user);


        return Reply::success(__('messages.deleteSuccess'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
   
        
        $employee = User::with(['employeeDetail.designation', 'employeeDetail.department', 'employeeDetail.reportingTo', 'country', 'emergencyContacts', 'reportingTeam' => function ($query) {
            $query->join('users', 'users.id', '=', 'employee_details.user_id');
            $query->where('users.status', '=', 'active');
        }, 'reportingTeam.user', 'leaveTypes', 'leaveTypes.leaveType'])
        ->withoutGlobalScope(ActiveScope::class)
        ->withOut('clientDetails')
        ->findOrFail($id);
 

        $tab = request('tab');

     

            if ($tab == '') {  // Works for profile

                $this->fromDate = now()->timezone(cooperative()->timezone)->startOfMonth()->toDateString();
                $this->toDate = now()->timezone(cooperative()->timezone)->endOfMonth()->toDateString();

                $this->lateAttendance = Attendance::whereBetween(DB::raw('DATE(`clock_in_time`)'), [$this->fromDate, $this->toDate])
                    ->where('late', 'yes')->where('user_id', $id)->count();

                $this->leavesTaken = Leave::selectRaw('count(*) as count, SUM(if(duration="half day", 1, 0)) AS halfday')
                    ->where('user_id', $id)
                    ->where('status', 'approved')
                    ->whereBetween(DB::raw('DATE(`leave_date`)'), [$this->fromDate, $this->toDate])
                    ->first();

                $this->leavesTaken = (!is_null($this->leavesTaken)) ? @$this->leavesTaken->count - (@$this->leavesTaken->halfday * 0.5) : 0;

                $this->taskChart = $this->taskChartData($id);
                $this->ticketChart = $this->ticketChartData($id);

                if (!is_null($employee->employeeDetail)) {
                    $employeeDetail = $employee->employeeDetail->withCustomFields();

                    $customFields = $employeeDetail->getCustomFieldGroupsWithFields();

                    if (!empty($customFields)) {
                        $this->fields = $customFields->fields;
                    }
                } 


            }

       

        $this->pageTitle = '';

        switch ($tab) {
        case 'leaves':
            $this->employee = $employee;
            return $this->leaves();
            break;
        case 'documents': 
            $this->employee = $employee;
             
            $this->view = 'manager.employees.ajax.documents';
            break;
        case 'emergency-contacts':
            $this->employee = $employee;
            $this->view = 'manager.employees.ajax.emergency-contacts';
            break;
        case 'leaves-quota':
            $this->leaveQuota($id);
            $this->employee = $employee;
            $this->leavesTakenByUser = Leave::byUserCount($employee);
            $this->leaveTypes = LeaveType::byUser($employee);
            $this->employeeLeavesQuotas = $employee->leaveTypes;
            $this->employeeLeavesQuota = clone $this->employeeLeavesQuotas;
            
            $totalLeaves = 0;

            foreach($this->leaveTypes as $key => $leavesCount)
            {
                $leavesCountCheck = $leavesCount->leaveTypeCodition($leavesCount, $this->userRole);

                if($leavesCountCheck && $this->employeeLeavesQuotas[$key]->leave_type_id == $leavesCount->id){
                    $totalLeaves += $this->employeeLeavesQuotas[$key]->no_of_leaves;
                }
            }

            $this->allowedLeaves = $totalLeaves;
            $this->view = 'manager.employees.ajax.leaves_quota';
            break;
        default:
            $this->view = 'manager.employees.ajax.profile';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';
        $this->employee = $employee;
        return view('manager.employees.show', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function taskChartData($id)
    {
        $taskStatus = TaskboardColumn::all();
        $data['labels'] = $taskStatus->pluck('column_name');
        $data['colors'] = $taskStatus->pluck('label_color');
        $data['values'] = [];

        foreach ($taskStatus as $label) {
            $data['values'][] = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')
                ->where('task_users.user_id', $id)->where('tasks.board_column_id', $label->id)->count();
        }

        return $data;
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function ticketChartData($id)
    {
        $labels = ['open', 'pending', 'resolved', 'closed'];
        $data['labels'] = [__('app.open'), __('app.pending'), __('app.resolved'), __('app.closed')];
        $data['colors'] = ['#D30000', '#FCBD01', '#2CB100', '#1d82f5'];
        $data['values'] = [];

        // foreach ($labels as $label) {
        //     $data['values'][] = Ticket::where('agent_id', $id)->where('status', $label)->count();
        // }

        return $data;
    }

    public function byDepartment($id)
    {
        $users = User::join('employee_details', 'employee_details.user_id', '=', 'users.id');

        if ($id != 0) {
            $users = $users->where('employee_details.department_id', $id);
        }

        $users = $users->select('users.*')->get();

        $options = '';

        foreach ($users as $item) {
            $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div>  ' . $item->lastname .' '.$item->firstname. '" value="' . $item->id . '"> ' . $item->lastname .' '.$item->firstname. ' </option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

    public function appreciation($employeeID)
    {
        
        $appreciations = Appreciation::with(['award','award.awardIcon', 'awardTo'])->select('id', 'award_id', 'award_to', 'award_date', 'image', 'summary', 'created_at');
        $appreciations->join('awards', 'awards.id', '=', 'appreciations.award_id'); 
        $appreciations = $appreciations->select('appreciations.*')->where('appreciations.award_to', $employeeID)->get();

        return $appreciations;
    }

    public function projects()
    { 

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'manager.employees.ajax.projects';

        $dataTable = new ProjectsDataTable();

        return $dataTable->render('manager.employees.show', $this->data);

    }

    public function tickets()
    { 
        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->tickets = Ticket::all();
        $this->view = 'manager.employees.ajax.tickets';
        $dataTable = new TicketDataTable();

        return $dataTable->render('manager.employees.show', $this->data);

    }

    public function tasks()
    { 

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->taskBoardStatus = TaskboardColumn::all();
        $this->view = 'manager.employees.ajax.tasks';

        $dataTable = new TasksDataTable();

        return $dataTable->render('manager.employees.show', $this->data);
    }

    public function leaves()
    { 

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->leaveTypes = LeaveType::all();
        $this->view = 'manager.employees.ajax.leaves';

      $dataTable = new LeaveDataTable();
       
        return $dataTable->render('manager.employees.show', $this->data);
    }

    public function timelogs()
    { 

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'manager.employees.ajax.timelogs';

        $dataTable = new TimeLogsDataTable();

        return $dataTable->render('manager.employees.show', $this->data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function inviteMember()
    { 

        return view('manager.employees.ajax.invite_member', $this->data);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function sendInvite(InviteEmailRequest $request)
    {
        $emails = json_decode($request->email);

        if (!empty($emails)) {
            foreach ($emails as $email) {
                $invite = new UserInvitation();
                $invite->user_id = user()->id;
                $invite->email = $email->value;
                $invite->message = $request->message;
                $invite->invitation_type = 'email';
                $invite->invitation_code = sha1(time() . user()->id);
                $invite->save();
            }
        }

        return Reply::success(__('messages.inviteEmailSuccess'));
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function createLink(CreateInviteLinkRequest $request)
    {
        $invite = new UserInvitation();
        $invite->user_id = user()->id;
        $invite->invitation_type = 'link';
        $invite->invitation_code = sha1(time() . user()->id);
        $invite->email_restriction = (($request->allow_email == 'selected') ? $request->email_domain : null);
        $invite->save();

        return Reply::successWithData(__('messages.inviteLinkSuccess'), ['link' => route('invitation', $invite->invitation_code)]);
    }

    /**
     * @param mixed $request
     * @param mixed $employee
     */
    public function employeeData($request, $employee): void
    {
        // $employee->employee_id = $request->employee_id;
        $employee->address = $request->address;
        $employee->hourly_rate = $request->hourly_rate;
        $employee->slack_username = $request->slack_username;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->reporting_to = $request->reporting_to;
        $employee->about_me = $request->about_me;
        $employee->joining_date = Carbon::createFromFormat('Y-m-d', $request->joining_date)->format('Y-m-d');
        $employee->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat('Y-m-d', $request->date_of_birth)->format('Y-m-d') : null;
        $employee->calendar_view = 'task,events,holiday,tickets,leaves';
        $employee->probation_end_date = $request->probation_end_date ? Carbon::createFromFormat('Y-m-d', $request->probation_end_date)->format('Y-m-d') : null;
        $employee->notice_period_start_date = $request->notice_period_start_date ? Carbon::createFromFormat('Y-m-d', $request->notice_period_start_date)->format('Y-m-d') : null;
        $employee->notice_period_end_date = $request->notice_period_end_date ? Carbon::createFromFormat('Y-m-d', $request->notice_period_end_date)->format('Y-m-d') : null;
        $employee->marital_status = $request->marital_status;
        $employee->marriage_anniversary_date = $request->marriage_anniversary_date ? Carbon::createFromFormat('Y-m-d', $request->marriage_anniversary_date)->format('Y-m-d') : null;
        $employee->employment_type = $request->employment_type;
        $employee->internship_end_date = $request->internship_end_date ? Carbon::createFromFormat('Y-m-d', $request->internship_end_date)->format('Y-m-d') : null;
        $employee->contract_end_date = $request->contract_end_date ? Carbon::createFromFormat('Y-m-d', $request->contract_end_date)->format('Y-m-d') : null;
    }

    public function importMember()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.employee'); 

        if (request()->ajax()) {
            $html = view('manager.employees.ajax.import', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.employees.ajax.import';

        return view('manager.employees.create', $this->data);
    }

    public function importStore(ImportRequest $request)
    {
        $this->importFileProcess($request, EmployeeImport::class);

        $view = view('manager.employees.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }

    public function importProcess(ImportProcessRequest $request)
    {
        $batch = $this->importJobProcess($request, EmployeeImport::class, ImportEmployeeJob::class);

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }

    public function leaveQuota($id)
    {
        $roles = User::with('roles')->findOrFail($id);
        $userRole = [];

        $userRoles = $roles->roles->count() > 1 ? $roles->roles->where('name', '!=', 'employee') : $roles->roles;

        foreach($userRoles as $role){
            $userRole[] = $role->id;
        }

        $this->userRole = $userRole;
    }

}
