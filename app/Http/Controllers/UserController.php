<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use fuma\User;
use fuma\Http\Requests\UserRegistrationRequest;
use fuma\Http\Requests\UserModifyRequest;
use Auth;

// for flash messages
use Session;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'isAdmin']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index() {
    //Get all users and pass it to the view
        $users = User::all(); 
        return view('users.index')->with('users', $users);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Get all roles and pass it to the view
        $roles = Role::get();
        return view('users.create', ['roles'=>$roles]);
    }

    /**
     * Store a newly created resource in storage.
     * Type hint as UserRegistrationRequest request for validation logic
     *
     * @param  UserRegistrationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRegistrationRequest $request)
    {
        //Validate name, email and password fields
        $validated = $request->validated();

        //Password encryption is handled in the Eloquent User
        $user = User::create([
            'email' => $validated['email'], 
            'name' => $validated['name'], 
            'password' => $validated['password']
        ]);

        $roles = $request['roles']; //Retrieving the roles field
        //Checking if a role was selected
        if (isset($roles)) {

            foreach ($roles as $role) {
                $role_r = Role::where('id', '=', $role)->firstOrFail();            
                $user->assignRole($role_r); //Assigning role to user
            }
        }   

        //Redirect to the users.index view and display message
        return redirect()->route('users.index')
            ->with('alert-success',
            'User successfully added.');
    }

    /**
    * Display the specified resource.
    * We just display the user index table for all users
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id) {
        return redirect()->route('users.index'); 
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id) {
        $user = User::findOrFail($id); //Get user with specified id
        $roles = Role::get(); //Get all roles

        return view('users.edit', compact('user', 'roles')); //pass user and roles data to view

    }

    /**
    * Update the specified resource in storage.
    * Password update is optional.
    *
    * @param  Request  $request 
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(UserModifyRequest $request, $id) {
        Log::info('User update started');
        $user = User::findOrFail($id); //Get role specified by id

        $validated = $request->validated();
        $input = array_intersect_key($validated, array_flip(['name', 'email', 'password']));
        // optional password update
        if ($input['password']) {
            $user->fill($input)->save();
        } else {
            $user->fill(array_except($input, 'password'))->save();
        } 

        $roles = $request['roles']; //Retrieve all roles

        if (isset($roles)) {        
            $user->roles()->sync($roles);  //If one or more role is selected associate user to roles          
        }        
        else {
            $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
        }
        return redirect()->route('users.index')
            ->with('alert-success',
            'User successfully edited.');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id) {
        //Find a user with a given id and delete
        $user = User::findOrFail($id); 
        $user->delete();

        return redirect()->route('users.index')
            ->with('alert-success',
                'User successfully deleted.');
    }

}
