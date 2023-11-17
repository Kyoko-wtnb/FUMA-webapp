<?php

namespace App\Http\Controllers\RolesPermissions;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\Log;

use Session;

class RoleController extends Controller
{
    public const VALIDATION_RULES = [
        'name'=>'required|max:20|unique:roles,name',
        'permissions' =>'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $roles = Role::all();//Get all roles

        return view('roles.index')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $permissions = Permission::all();//Get all permissions

        return view('roles.create', ['permissions'=>$permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *     Containing:
     *         name: text representing a unique Role name
     *         permissions: array of integer Permission ids
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //Validate name and permissions field
        $this->validate($request, $this::VALIDATION_RULES);

        $name = $request['name'];
        $role = Role::create(['name' => $name]);
        $permissions = $request['permissions'];
        Log::info("Call to roles.store, permissions: " . implode(" ", $permissions) . " to role: " . $name);

        $role->save();
        Log::info("Saved new role: " . $name);
        //Looping thru selected permissions
        foreach ($permissions as $permission) {
            Log::info("Getting permission: " . $permission . " from DB ");
            $p = Permission::where('id', '=', $permission)->firstOrFail(); 
            Log::info("Processing permission: " . $permission . " for role: " . $name);
            //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $name)->first(); 
            $role->givePermissionTo($p);
            Log::info("Assign permission: " . $permission . " to role: " . $name);
        }
        Log::info("Assign complete - redirect");
        return redirect()->route('roles.index')
            ->with('alert-success',
                'Role'. $role->name.' added!'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $role = Role::findOrFail($id);//Get role with the given id
        //Validate name and permission fields - ignore existing id for unique
        $rules = $this::VALIDATION_RULES;
        $rules['name'] .= ",$id";
        $this->validate($request, $rules);

        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();

        $p_all = Permission::all();//Get all permissions

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p); //Remove all permissions associated with role
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
            $role->givePermissionTo($p);  //Assign permission to role
        }

        return redirect()->route('roles.index')
            ->with('alert-success',
            'Role'. $role->name.' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('alert-success',
             'Role deleted!');

    }
}