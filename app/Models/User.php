<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Rules\Lowercase;

use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // The common validation rules for the user model are stored in 
    // the model itself are adjusted in the FormRequests (app/Http/Request/*)

    /**
     * 
     * @param mixed $modifyingUserId (optional) 
     *      If modifying a user then email uniqueness will be ignored in the rule
     * @return (string|(string|Lowercase|bool)[])[] 
     *      An array of array containing the validation rules for 
     *      name and email 
     */
    public static function getValidationRules($modifyingUserId = null) {
        return [
            'name' => 'required|max:255',
            'email' => [
                'required', 
                'email', 
                'max:255', 
                new Lowercase, 
                empty($modifyingUserId) ? Rule::unique('users','email') : Rule::unique('users','email')->ignore($modifyingUserId)
            ]
        ];
    }

    /**
     * Add a mutator to ensure hashed passwords at one centralized place
     *  have User registration & editing now
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(SubmitJob::class);
    }
}
