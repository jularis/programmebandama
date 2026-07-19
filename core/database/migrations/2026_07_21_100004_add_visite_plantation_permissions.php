<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    protected $permissions = [
        'manager.suivi.visiteplantation.index',
        'manager.suivi.visiteplantation.create',
        'manager.suivi.visiteplantation.store',
        'manager.suivi.visiteplantation.edit',
        'manager.suivi.visiteplantation.show',
        'manager.suivi.visiteplantation.status',
        'manager.suivi.visiteplantation.delete',
        'manager.suivi.visiteplantation.exportExcel.plantationAll',
    ];

    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $managerRoleId = DB::table('roles')->where('name', 'Manager')->where('guard_name', 'web')->value('id');

        foreach ($this->permissions as $name) {
            $permissionId = DB::table('permissions')->where('name', $name)->where('guard_name', 'web')->value('id');
            if (!$permissionId) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($managerRoleId) {
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', $managerRoleId)
                    ->exists();
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $managerRoleId,
                    ]);
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        $managerRoleId = DB::table('roles')->where('name', 'Manager')->where('guard_name', 'web')->value('id');
        $permissionIds = DB::table('permissions')->whereIn('name', $this->permissions)->pluck('id');

        if ($managerRoleId) {
            DB::table('role_has_permissions')
                ->where('role_id', $managerRoleId)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
