<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use fuma\User;
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //Validate name, email and password fields
            $this->validate($request, [
                'name'=>'required|max:120',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:6|confirmed'
            ]);

            $user = User::create($request->only('email', 'name', 'password')); //Retrieving only the email and password data

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
                ->with('flash_message',
                'User successfully added.');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return back()->with('flash_message', $e->getMessage());
        }
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
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id) {
        try {
            Log::info('User update started');
            $user = User::findOrFail($id); //Get role specified by id

            //Validate name, email and (optional)password/password_confirmed fields 
            // TODO From Laravel 6 can change to $request->validate
            $this->validate($request, [
                'name'=>'required|max:120',
                'email'=>'required|email|unique:users,email,'.$id,
                'password'=>'nullable|min:6|confirmed'
            ]);

            // //Retreive the name, email and password fields
            $input = $request->only(['name', 'email', 'password']);
            if ($input['password']) {
                $user->fill($input)->save();
            } else {
                $user->fill(array_except($input, 'password'))->save();
            } 

            $roles = $request['roles']; //Retreive all roles

            if (isset($roles)) {        
                $user->roles()->sync($roles);  //If one or more role is selected associate user to roles          
            }        
            else {
                $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
            }
            return redirect()->route('users.index')
                ->with('flash_message',
                'User successfully edited.');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return back()->with('flash_message', $e->getMessage());
        }
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
            ->with('flash_message',
                'User successfully deleted.');
    }

}
