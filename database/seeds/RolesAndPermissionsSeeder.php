<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['guard_name' => 'web', 'name' => 'Administer roles & permissions']);
        Permission::create(['guard_name' => 'web', 'name' => 'Queue jobs']);

        $adminRole = Role::create(['guard_name' => 'web', 'name' => 'Admin'])
            ->givePermissionTo('Administer roles & permissions');
        $superRole = Role::create(['guard_name' => 'web', 'name' => 'SuperRunner'])
            ->givePermissionTo('Queue jobs');
        $normalRole = Role::create(['guard_name' => 'web', 'name' => 'NormalRunner'])
            ->givePermissionTo('Queue jobs');
        $guestRole = Role::create(['guard_name' => 'web', 'name' => 'GuestRunner'])
            ->givePermissionTo('Queue jobs');

        // Assign at least one user to have the Admin role
        // This assumes that the Admin role has id 1 (role_id)
        // You need to insert an existing user id at model_id
        // and uncomment the code
        //DB::table('model_has_roles')->insert(
        //    ['role_id'=>1, 'model_type'=>'fuma\User', 'model_id'=>/** Replace this comment by an existing user id*/]
        //);
    }
}
