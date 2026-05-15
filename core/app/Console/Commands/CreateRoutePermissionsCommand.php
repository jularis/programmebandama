<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class CreateRoutePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-permission-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a permission routes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();
        

        foreach ($routes as $route) {
            
            if ($route->getName() != '') {
                $nameRoute = Str::before($route->getName(), '.');
                if($nameRoute =='manager'){
                    
                $permission = Permission::where('name', $route->getName())->first(); 
                 
                if ($permission ==null) {
                    $permission = new Permission();
                    $permission->name=$route->getName();
                    $permission->save();
                    //permission::create(['name' => $route->getName()]);
                }
                }
            }
            
        }

        $this->info('Permission routes added successfully.');
    }
}
