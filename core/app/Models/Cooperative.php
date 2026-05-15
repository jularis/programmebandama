<?php

namespace App\Models;
 
use App\Scopes\ActiveScope;
use App\Scopes\CooperativeScope;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
// use Laravel\Cashier\Billable;

 
class Cooperative extends BaseModel
{

    const CUSTOM_FIELD_MODEL = 'App\Models\Cooperative';

    use Searchable, GlobalStatus, PowerJoins, HasFactory, CustomFieldsTrait;

    // WORKSUITESAAS 

    protected $table = 'cooperatives';
 
    const DATE_FORMATS = GlobalSetting::DATE_FORMATS;

    public function cooperativeUsers()
    {
        return $this->hasMany(User::class, 'cooperative_id', 'id');
    }
    public function sections()
    {
        return $this->hasMany(Section::class, 'cooperative_id', 'id');
    }
    // Dans le modèle Cooperative
    public function producteurs()
    {
        return $this->hasMany(Producteur::class);
    }
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
 
    public function users()
    {
        return $this->hasMany(User::class)->withoutGlobalScope(CooperativeScope::class)->withoutGlobalScope('active');
    }

    public function user()
    {
        return $this->hasOne(User::class)->withoutGlobalScopes([CooperativeScope::class, ActiveScope::class])->setEagerLoads([]);
    }
 
    public function employees()
    {
        return $this->hasMany(User::class)->whereHas('employeeDetail');
    }

    public function getLogoUrlAttribute()
    {
        if (user()) {
            if (user()->dark_theme) {
                return $this->defaultLogo();
            }
        }

        if (cooperative() && cooperative()->auth_theme == 'dark') {
            return $this->defaultLogo();

        }

        if (is_null($this->light_logo)) {
            return global_setting()->light_logo_url;
        }

        return asset_url_local_s3('app-logo/' . $this->light_logo, true, 'image');

    }

    public function defaultLogo()
    {
        if (is_null($this->logo)) {
            return global_setting()->dark_logo_url;
        }

        return asset_url_local_s3('app-logo/' . $this->logo, true, 'image');
    }

    public function getLightLogoUrlAttribute()
    {
        if (is_null($this->light_logo)) {
            return global_setting()->light_logo_url;
        }

        return asset_url_local_s3('app-logo/' . $this->light_logo, true, 'image');
    }

    public function getDarkLogoUrlAttribute()
    {

        if (is_null($this->logo)) {
            return asset('img/worksuite-logo.png');
        }

        return asset_url_local_s3('app-logo/' . $this->logo, true, 'image');
    }

    public function getLoginBackgroundUrlAttribute()
    {

        if (is_null($this->login_background) || $this->login_background == 'login-background.jpg') {
            return null;
        }

        return asset_url_local_s3('login-background/' . $this->login_background);
    }

    public function getMomentDateFormatAttribute()
    {

        return isset($this->date_format) ? self::DATE_FORMATS[$this->date_format] : null;
    }

    public function getFaviconUrlAttribute()
    {
        if (is_null($this->favicon)) {
            return global_setting()->favicon_url;
        }

        return asset_url_local_s3('favicon/' . $this->favicon);
    }

    public function paymentGatewayCredentials(): HasOne
    {
        return $this->hasOne(PaymentGatewayCredentials::class);
    }

    public function invoiceSetting(): HasOne
    {
        return $this->hasOne(InvoiceSetting::class);
    }

    public function offlinePaymentMethod(): HasMany
    {
        return $this->hasMany(OfflinePaymentMethod::class);
    }

    public function leaveTypes()
    {
        return $this->hasMany(LeaveType::class);
    }

    public function cooperativeAddress(): HasMany
    {
        return $this->hasMany(CooperativeAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(CooperativeAddress::class)->where('is_default', 1);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function ticketChannels(): HasMany
    {
        return $this->hasMany(TicketChannel::class);
    }

    public function projectSetting(): HasOne
    {
        return $this->hasOne(ProjectSetting::class);
    }

    public function projectStatusSettings(): HasMany
    {
        return $this->HasMany(ProjectStatusSetting::class);
    }

    public function attendanceSetting(): HasOne
    {
        return $this->HasOne(AttendanceSetting::class);
    }

    public function messageSetting(): HasOne
    {
        return $this->HasOne(MessageSetting::class);
    }

    public function leadSources(): HasMany
    {
        return $this->HasMany(LeadSource::class);
    }

    public function leadStats(): HasMany
    {
        return $this->HasMany(LeadStatus::class);
    }

    public function leadAgents(): HasMany
    {
        return $this->HasMany(LeadAgent::class);
    }

    public function leadCategories(): HasMany
    {
        return $this->HasMany(LeadCategory::class);
    }

    public function moduleSetting(): HasMany
    {
        return $this->HasMany(ModuleSetting::class);
    }

    public function currencies(): HasMany
    {
        return $this->HasMany(Currency::class);
    }

    public function timeLogSetting(): HasOne
    {
        return $this->HasOne(ProjectTimeLog::class);
    }

    public function taskSetting(): HasOne
    {
        return $this->HasOne(TaskSetting::class);
    }

    public function leaveSetting(): HasOne
    {
        return $this->HasOne(LeaveSetting::class);
    }

    public function slackSetting(): HasOne
    {
        return $this->HasOne(SlackSetting::class);
    }

    public function fileStorage()
    {
        return $this->hasMany(FileStorage::class);
    }

    public static function renameOrganisationTableToCooperativeTable()
    {
        if (Schema::hasTable('organisation_settings')) {
            Schema::rename('organisation_settings', 'companies');
        }
    }

    public function clients()
    {
        return $this->hasMany(User::class)->whereHas('ClientDetails');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    // WORKSUITESAAS
    public function approvalBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
