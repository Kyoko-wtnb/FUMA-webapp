<?php

namespace fuma;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    // The common validation rules for the user model are stored in 
    // the model itself are adjusted in the FormRequests (app/Http/Request/*)
    public const VALIDATION_RULES = [
        'name' => 'required|max:255',
        'email'=>'required|email|max:255|unique:users,email',
    ];

    use \Illuminate\Notifications\Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Add a mutator to ensure hashed passwords
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
