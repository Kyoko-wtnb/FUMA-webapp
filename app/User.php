<?php

namespace fuma;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use fuma\Rules\Lowercase;

class User extends Authenticatable
{
    // The common validation rules for the user model are stored in 
    // the model itself are adjusted in the FormRequests (app/Http/Request/*)
    public static function getValidationRules() {
        return [
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', new Lowercase, 'unique:users'],
        ];
    } 

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
