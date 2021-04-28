<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
    }
}
