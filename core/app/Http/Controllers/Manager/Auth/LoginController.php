<?php

namespace App\Http\Controllers\Manager\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use MakiDizajnerica\GeoLocation\Facades\GeoLocation;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $pageTitle = "Connexion Manager";
        
        return view('manager.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $request->session()->regenerateToken();
        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
 
        return $this->sendFailedLoginResponse($request);
    }

    public function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
    
        return $ip;
    } 
    public function username()
    {
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        $customRecaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        $validation_rule = [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ];

        // if ($customRecaptcha) {
        //     $validation_rule['captcha'] = 'required';
        // }

        $request->validate($validation_rule);
    }

    // public function logout()
    // {
    //     $this->guard()->logout();
    //     request()->session()->invalidate();
    //     $notify[] = ['success', 'Vous avez été déconnecté(e).'];
    //     return redirect()->route('login')->withNotify($notify);
    // }

    public function authenticated(Request $request, $user)
    {
        if ($user->status == Status::BAN_USER) {
            $this->guard()->logout();
            $notify[] = ['error', 'Votre compte a été désactivé.'];
            return redirect()->route('login')->withNotify($notify);
        }
        // if (auth()->user()->user_type != "manager") {
        //     $this->guard()->logout();
        //     $notify[] = ['error', 'Vous n\'arrivez pas à vous connecter à votre tableau de bord.'];
        //     return redirect()->route('manager.login')->withNotify($notify);
        // }
        $user     = auth()->user();

        $user->save();
        $ip        = $_SERVER["REMOTE_ADDR"];
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else { 
            if($this->getUserIP() !='::1')
            {
            $collection = GeoLocation::lookup($this->getUserIP());
            $userLogin->longitude    = $collection->get('longitude');
            $userLogin->latitude     = $collection->get('latitude');
            $userLogin->city         = $collection->get('city');
            $userLogin->country_code = $collection->get('countryCode');
            $userLogin->country      = $collection->get('country');
            }
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();
        return redirect()->route('manager.dashboard');
    }

    protected function logout(Request $request)
    {
        $this->guard('web')->logout();
        $request->session()->forget('guard.web');  
        $request->session()->forget('cooperative');   
        session()->flush();
        //$request->session()->regenerate();

        return redirect('/login');
    }
}