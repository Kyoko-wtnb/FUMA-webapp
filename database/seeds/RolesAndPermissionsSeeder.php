<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException as ValidationException;
use fuma\User;

class RolesAndPermissionsSeeder extends Seeder
{
    function roleExists($role) 
    {
        return Count(Role::findByName($role)->get()) > 0;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = null;
        // create an admin permission for managing roles and permissions
        if(!$this->roleExists('Admin')) 
        {
            echo "Create Admin Role and permissions \n";
            // create an admin permission for managing roles and permissions
            Permission::create(['guard_name' => 'web', 'name' => 'Administer roles & permissions']);
            $adminRole = Role::create(['guard_name' => 'web', 'name' => 'Admin'])
                ->givePermissionTo('Administer roles & permissions');
            $adminRole->givePermissionTo(Permission::all());
        } 
        else {
            echo "Load existing Admin Role \n";
            $adminRole = Role::findByName('Admin')->get();
        }
        /*// Create three level of jobs submissions
        // In practice this can can be configured to different max jobs per
        // permission types.
        Permission::create(['guard_name' => 'web', 'name' => 'Queue limited jobs']);
        Permission::create(['guard_name' => 'web', 'name' => 'Queue jobs']);
        Permission::create(['guard_name' => 'web', 'name' => 'Queue unlimited jobs']);

        $superRole = Role::create(['guard_name' => 'web', 'name' => 'SuperRunner'])
            ->givePermissionTo('Queue unlimited jobs');
        $normalRole = Role::create(['guard_name' => 'web', 'name' => 'NormalRunner'])
            ->givePermissionTo('Queue jobs');
        $guestRole = Role::create(['guard_name' => 'web', 'name' => 'GuestRunner'])
            ->givePermissionTo('Queue limited jobs');
        */

        // Assign at least one user to have the Admin role
        // This assumes that the Admin role has id 1 (role_id)
        // You need to insert an existing user id at model_id
        // and uncomment the code
        //DB::table('model_has_roles')->insert(
        //    ['role_id'=>1, 'model_type'=>'fuma\User', 'model_id'=>1]
        //);
        $seed_admin_email = env('SEED_ADMIN_EMAIL', 'someone@example.com');
        echo "Adding Admin to user with email: ".$seed_admin_email."\n";
        $admin_user = User::where('email', $seed_admin_email)->first();
        if ($admin_user === null) {
            error_log("No user found corresponding to email: ".$seed_admin_email);
            error_log("Please set a valid user email in environment variable SEED_ADMIN_EMAIL");
            throw ValidationException::withMessages(["SEED_ADMIN_EMAIL" => "".$seed_admin_email]);
        }
        $admin_user->assignRole("Admin");
    }
}
