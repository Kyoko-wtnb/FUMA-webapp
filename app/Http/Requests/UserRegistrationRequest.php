<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Validate:
     * Common user validation + password with length & confirmation check
     *
     * @return array
     */
    public function rules()
    {
        // Append required password validation
        return User::getValidationRules() + [
            'password' => 'required|min:8|confirmed',
        ];
    }
}