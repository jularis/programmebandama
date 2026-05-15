<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Support\Str;
use App\Traits\GlobalStatus;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens; 
use Kirschbaum\PowerJoins\PowerJoins;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    //ajout du use HasRole
    use Searchable,HasApiTokens, GlobalStatus, PowerJoins,HasRoles;
 
 
    public function userLocalites()
    {
        return $this->hasMany(User_localite::class,'user_id','id');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }


    // SCOPES
  

    public function scopeBanned()
    {
        return $this->where('status', Status::BAN_USER);
    }
    public function scopeManager($query)
    {
        $query->where('user_type', 'manager');
    }
    public function scopeStaff($query)
    {
        $query->where('user_type', '!=', 'manager');
    }
   

    
    public function getImageUrlAttribute()
    {
        $gravatarHash = md5(strtolower(trim($this->email)));

        return ($this->image) ? asset_url_local_s3('avatar/' . $this->image, true, 'image') : 'https://www.gravatar.com/avatar/' . $gravatarHash . '.png?s=200&d=mp';
    }

    public function hasGravatar($email)
    {
        // Craft a potential url and test its headers
        $hash = md5(strtolower(trim($email)));

        $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
        $headers = @get_headers($uri);

        $has_valid_avatar = true;

        try {
            if (!preg_match('|200|', $headers[0])) {
                $has_valid_avatar = false;
            }
        } catch (\Exception $e) {
            $has_valid_avatar = true;
        }

        return $has_valid_avatar;
    }

    public function getMobileWithPhoneCodeAttribute()
    {
        if (!is_null($this->mobile) && !is_null($this->country_phonecode)) {
            return '+' . $this->country_phonecode . $this->mobile;
        }

        return '--';
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        $slack = $this->cooperative->slackSetting;

        return $slack->slack_webhook;
    }

    public function routeNotificationForOneSignal()
    {
        return $this->onesignal_player_id;
    }

    public function routeNotificationForTwilio()
    {
        if (!is_null($this->mobile) && !is_null($this->country_phonecode)) {
            return '+' . $this->country_phonecode . $this->mobile;
        }

        return null;
    }

    // phpcs:ignore
    public function routeNotificationForEmail($notification = null)
    {
        $containsExample = Str::contains($this->email, 'example');

        if (\config('app.env') === 'demo' && $containsExample) {
            return null;
        }

        return $this->email;
    }

    // phpcs:ignore
    public function routeNotificationForNexmo($notification)
    {
        if (!is_null($this->mobile) && !is_null($this->country_phonecode)) {
            return $this->country_phonecode . $this->mobile;
        }

        return null;

    }

    // phpcs:ignore
    public function routeNotificationForMsg91($notification)
    {
        if (!is_null($this->mobile) && !is_null($this->country_phonecode)) {
            return $this->country_phonecode . $this->mobile;
        }

        return null;
    }

    public function clientDetails()
    {
        return $this->hasOne(ClientDetails::class, 'user_id');
    }

    public function userAuth()
    {
        return $this->belongsTo(UserAuth::class, 'user_auth_id');
    }

    public function lead()
    {
        return $this->hasOne(Lead::class, 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasMany(EmployeeDetail::class, 'user_id');
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class, 'user_id');
    }
    

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function member()
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    public function appreciations()
    {
        return $this->hasMany(Appreciation::class, 'award_to');
    }

    public function appreciationsGrouped()
    {
        return $this->hasMany(Appreciation::class, 'award_to')->select('appreciations.*', DB::raw('count("award_id") as no_of_awards'))->groupBy('award_id');
    }

    public function templateMember()
    {
        return $this->hasMany(ProjectTemplateMember::class, 'user_id');
    }

    public function role()
    {
        return $this->hasMany(RoleUser::class, 'user_id');
    }

    public function attendee()
    {
        return $this->hasMany(EventAttendee::class, 'user_id');
    }

    public function agent()
    {
        return $this->hasMany(TicketAgentGroups::class, 'agent_id');
    }

    public function agentGroup()
    {
        return $this->belongsToMany(TicketGroup::class, 'ticket_agent_groups', 'agent_id', 'group_id');
    }

    public function agents()
    {
        return $this->hasMany(Ticket::class, 'agent_id');
    }

    public function leadAgent()
    {
        return $this->hasMany(LeadAgent::class, 'user_id');
    }

    public function group()
    {
        return $this->hasMany(EmployeeTeam::class, 'user_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function skills(): array
    {
        return EmployeeSkill::select('skills.name')->join('skills', 'skills.id', 'employee_skills.skill_id')->where('user_id', $this->id)->pluck('name')->toArray();
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function leaveTypes()
    {
        return $this->hasMany(EmployeeLeaveQuota::class);
    }

    public function reportingTeam()
    {
        return $this->hasMany(EmployeeDetail::class, 'reporting_to');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_users');
    }

    public function openTasks()
    {
        $taskBoardColumn = TaskboardColumn::completeColumn();

        return $this->belongsToMany(Task::class, 'task_users')->where('tasks.board_column_id', '<>', @$taskBoardColumn->id);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id')->orderBy('id', 'desc');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id')->orderBy('id', 'desc');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'user_id');
    }

    public function clientDocuments()
    {
        return $this->hasMany(ClientDocument::class, 'user_id');
    }

    public function visa()
    {
        return $this->hasMany(VisaDetail::class, 'user_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ProjectTimeLog::class, 'user_id');
    }

    // WORKSUITESAAS
    public function approvedCooperative()
    {
        $cooperative = $this->belongsTo(Cooperative::class, 'cooperative_id');

        if (global_setting()->cooperative_need_approval) {
            $cooperative->where('companies.approved', 1);
        }

        return $cooperative;
    }

    public static function allClients($exceptId = null, $active = false, $overRidePermission = null, $cooperativeId = null)
    { 
 

        $clients = User::with('clientDetails')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->join('client_details', 'users.id', '=', 'client_details.user_id')
            ->select('users.id', 'users.name', 'users.created_at', 'client_details.cooperative_name', 'users.image', 'users.email_notifications', 'users.mobile', 'users.country_id')
            ->where('roles.name', 'client');

        if (!is_null($exceptId)) {
            if (is_array($exceptId)) {
                $clients->whereNotIn('users.id', $exceptId);

            }
            else {
                $clients->where('users.id', '<>', $exceptId);
            }
        }

        if (!$active) {

            $clients->withoutGlobalScope(ActiveScope::class);
        }

        if (!is_null($cooperativeId)) {
            $clients->where('users.cooperative_id', '<>', $cooperativeId);
        }
 

        if (!isRunningInConsoleOrSeeding() && in_array('client', user_roles())) {
            $clients->where('client_details.user_id', user()->id);
        }

        return $clients->orderBy('users.name', 'asc')->get();
    }

    public static function client()
    {
        return User::withoutGlobalScope(ActiveScope::class)
            ->with('clientDetails')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->join('client_details', 'users.id', '=', 'client_details.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'client_details.cooperative_name', 'users.image', 'users.email_notifications', 'users.mobile', 'users.country_id')
            ->where('roles.name', 'client')
            ->where('users.id', user()->id)
            ->orderBy('users.name', 'asc')
            ->get();
    }

    public static function allEmployees($exceptId = null, $active = false, $overRidePermission = null, $cooperativeId = null)
    {
        

        $users = User::join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->select('users.id', 'users.cooperative_id', 'users.firstname', 'users.lastname','users.created_at', 'users.image', 'designations.name as designation_name','designations.name', 'users.mobile', 'users.country_id');

        if (!is_null($exceptId)) {
            if (is_array($exceptId)) {
                $users->whereNotIn('users.id', $exceptId);

            }
            else {
                $users->where('users.id', '<>', $exceptId);
            }
        }

        if (!is_null($cooperativeId)) {
            $users->where('users.cooperative_id', $cooperativeId);
        }

        if (!$active) {
            $users->withoutGlobalScope(ActiveScope::class);
        }
 
 
        $users->orderBy('users.lastname');
        $users->groupBy('users.id');

        return $users->get();
    }

    public static function allAdmins($cooperativeId = null)
    {
        $users = User::withOut('clientDetails')->withRole('admin');

        if (!is_null($cooperativeId)) {
            return $users->where('users.cooperative_id', $cooperativeId)->get();
        }

        return $users->get();
    }

    public static function departmentUsers($teamId)
    {
        $users = User::join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('employee_details.department_id', $teamId);

        return $users->get();
    }

    public static function userListLatest($userID, $term)
    {
        $termCnd = '';

        if ($term) {
            $termCnd = 'and users.name like %' . $term . '%';
        }

        $messageSetting = message_setting();

        if (in_array('admin', user_roles())) {
            if ($messageSetting->allow_client_admin == 'no') {
                $termCnd .= "and roles.name != 'client'";
            }
        }
        elseif (in_array('employee', user_roles())) {
            if ($messageSetting->allow_client_employee == 'no') {
                $termCnd .= "and roles.name != 'client'";
            }
        }
        elseif (in_array('client', user_roles())) {
            if ($messageSetting->allow_client_admin == 'no') {
                $termCnd .= "and roles.name != 'admin'";
            }

            if ($messageSetting->allow_client_employee == 'no') {
                $termCnd .= "and roles.name != 'employee'";
            }
        }

        $query = DB::select('SELECT * FROM ( SELECT * FROM (
                    SELECT users.id,"0" AS groupId, users.name, users.image, users.email, users_chat.created_at as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.from = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.to = ' . $userID . ' ' . $termCnd . '
                    UNION
                    SELECT users.id,"0" AS groupId, users.name,users.image, users.email, users_chat.created_at  as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.to = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.from = ' . $userID . ' ' . $termCnd . '
                    ) AS allUsers
                    ORDER BY  last_message DESC
                    ) AS allUsersSorted
                    GROUP BY id
                    ORDER BY  last_message DESC');

        return $query;
    }

    public static function isAdmin($userId)
    {
        $user = User::find($userId);

        if ($user) {
            return $user->hasRole('admin');
        }

        return false;
    }

    public static function isClient($userId): bool
    {
        $user = User::find($userId);

        if ($user) {
            return $user->hasRole('client');
        }

        return false;
    }

    public static function isEmployee($userId): bool
    {
        $user = User::find($userId);

        if ($user) {
            return $user->hasRole('employee');
        }

        return false;
    }

    public static function firstSuperAdmin()
    {
        return User::withoutGlobalScopes(['active', CooperativeScope::class])
            ->where('is_superadmin', 1)
            ->whereNull('cooperative_id')
            ->orderBy('id')->first();
    }

    public static function allSuperAdmin()
    {
        return User::withoutGlobalScopes(['active', CooperativeScope::class])
            ->where('is_superadmin', 1)
            ->whereNull('cooperative_id')
            ->get();
    }

    public function getModulesAttribute()
    {
        return user_modules();
    }

    public function sticky()
    {
        return $this->hasMany(StickyNote::class, 'user_id')->orderBy('updated_at', 'desc');
    }

    public function userChat()
    {
        return $this->hasMany(UserChat::class, 'to')->where('message_seen', 'no');
    }

    public function employeeDetails()
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    public function getUnreadNotificationsAttribute()
    {
        return $this->unreadNotifications()->get();
    }
 

    /**
     * @return HasOne
     */
    public function session()
    {
        return $this->hasOne(Session::class, 'user_id')->select('user_id', 'ip_address', 'last_activity');
    }

    /**
     * @return HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'client_id', 'id');
    }
 

    public function unreadMessages()
    {
        return $this->hasMany(UserChat::class, 'from')->where('to', user()->id)->where('message_seen', 'no');
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShiftSchedule::class, 'user_id');
    }

    public function employeeShift()
    {
        return $this->belongsToMany(EmployeeShift::class, 'employee_shift_schedules');
    }

    public function userBadge()
    {
        $itsYou = ' <span class="ml-2 badge badge-secondary pr-1">' . __('app.itsYou') . '</span>';

        if (user() && user()->id == $this->id) {
            return $this->lastname.' '.$this->firstname.' ' . $itsYou;
        }

        return $this->lastname.' '.$this->firstname;
    }

    

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function scopeOnlyEmployee($query)
    {
        return $query;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */ 
}
