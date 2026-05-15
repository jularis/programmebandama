<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Scopes\ActiveScope;
use App\Scopes\CooperativeScope;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail as AuthMustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Trebol\Entrust\Traits\EntrustUserTrait;
use App\Notifications\ResetPassword;

 
class UserAuth extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, MustVerifyEmail
{

    use Authenticatable, Authorizable, CanResetPassword, HasFactory, AuthMustVerifyEmail, Notifiable;

    protected $fillable = ['username', 'email', 'password', 'remember_token', 'email_verification_code', 'email_verified_at', 'email_code_expires_at'];
    protected $hidden = ['password'];
    public $dates = ['email_code_expires_at'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

    public function userWithoutCooperative(): HasOne
    {
        return $this->hasOne(User::class, 'user_id')->withoutGlobalScope(CooperativeScope::class);
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function confirmTwoFactorAuth($code)
    {
        $codeIsValid = app(TwoFactorAuthenticationProvider::class)
            ->verify(decrypt($this->two_factor_secret), $code);

        if ($codeIsValid) {
            $this->two_factor_confirmed = true;
            $this->save();

            return true;
        }

        return false;
    }

    public static function createUserAuthCredentials($username, $email = null, $password = null, $oldEmail = null)
    {
        $checkAuth = User::where('username', $username)->first();
         
        if (is_null($checkAuth)) {
            if (is_null($password)) {
                $string = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
                $password = substr(str_shuffle($string), 0, 8);
            }

            if (!is_null($oldEmail)) {
                UserAuth::where('email', $oldEmail)->update(['email' => $email]);
                return $checkAuth;
            }

            $verifiedAt = user() ? now() : null;
            $checkAuth = UserAuth::create(['username' => $username, 'email' => $email, 'password' => bcrypt($password), 'email_verified_at' => $verifiedAt]);
            

        }

        return $checkAuth;
    }

    /**
     * @throws ValidationException
     */
    public static function validateLoginActiveDisabled($userAuth)
    {

        self::restrictUserLoginFromOtherSubdomain($userAuth);

        $globalSetting = GlobalSetting::first();
        $userCompanies = DB::select('Select count(companies.id) as cooperative_count from companies left join users on users.cooperative_id = companies.id where users.email = "' . $userAuth->email . '"');
        $userInactiveCompanies = DB::select('Select count(companies.id) as cooperative_count from companies left join users on users.cooperative_id = companies.id where users.email = "' . $userAuth->email . '" and companies.status = "inactive"');

        if ($globalSetting->cooperative_need_approval) {
            $userUnapprovedCompanies = DB::select('Select count(companies.id) as cooperative_count from companies left join users on users.cooperative_id = companies.id where users.email = "' . $userAuth->email . '" and companies.approved = 0');

            // Check count of all user companies and match with total unapproved companies
            if ($userCompanies[0]->cooperative_count > 0 && $userCompanies[0]->cooperative_count == $userUnapprovedCompanies[0]->cooperative_count) {
                throw ValidationException::withMessages([
                    'email' => __('auth.failedCooperativeUnapproved')
                ]);
            }

        }

        // Check count of all user companies and match with total inactive companies
        if ($userCompanies[0]->cooperative_count > 0 && $userCompanies[0]->cooperative_count == $userInactiveCompanies[0]->cooperative_count) {
            throw ValidationException::withMessages([
                'email' => __('auth.cooperativeAccountDisabled')
            ]);
        }


        // Check count of all user status and match with total user
        if ($userAuth->users->where('status', 'deactive')->count() == $userAuth->users->count()) {
            throw ValidationException::withMessages([
                'email' => __('auth.failedBlocked')
            ]);
        }

        // Check count of all user login and match with total user
        if ($userAuth->users->where('login', 'disable')->count() == $userAuth->users->count()) {
            throw ValidationException::withMessages([
                'email' => __('auth.failedLoginDisabled')
            ]);
        }
    }

    public function sendEmailVerificationNotification()
    {
        $id = (user() ? user()->id : $this->id);

        UserAuth::where('id', $id)
            ->update([
                'email_verification_code' => random_int(100000, 999999),
                'email_code_expires_at' => now()->addMinutes(30),
                'email_verified_at' => null
            ]);
        $this->notify(new VerifyEmail()); // my notification
    }

    private static function restrictUserLoginFromOtherSubdomain($userAuth)
    {
        if (!module_enabled('Subdomain')) {
            return true;
        }

        $cooperative = getCooperativeBySubDomain();

        // Check if superadmin is trying to login
        if (!$cooperative) {
            $userCount = $userAuth->users->whereNull('cooperative_id')->count();
        }
        else {
            $userCount = $userAuth->users->where('cooperative_id', $cooperative->id)->count();
        }

        if (!$userCount) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        return true;
    }

    public static function multipleUserLoginSubdomain()
    {
        $cooperative = getCooperativeBySubDomain();

        if ($cooperative) {
            $user = DB::table('users')
                ->where('email', user()->email)
                ->where('cooperative_id', $cooperative->id)
                ->first();

            session(['cooperative' => $cooperative]);
            session(['user' => $user]);
            session()->forget('user_roles');
            session()->forget('sidebar_user_perms');

            flushCooperativeSpecificSessions();
            Auth::loginUsingId($user->user_auth_id);
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

}
