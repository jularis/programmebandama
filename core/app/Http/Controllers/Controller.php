<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
 

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $activeTemplate;
    public $timezone;
/**
     * @var array
     */
    public $data = [];

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
        $this->timezone = "Africa/Abidjan";
        $className = get_called_class();
        //Onumoti::mySite($this,$className);
    }

    public function userType($id){
         $user = User::findOrFail($id);
         if($user->user_type=='staff'){
            return $type = "staff";
         }
         if($user->user_type=='manager')
         {
            return $type ='manager';
         }
    }

}
