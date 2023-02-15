<?php

namespace fuma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use fuma\User;

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
     * Common user validation + password with length & confirmition check
     *
     * @return array
     */
    public function rules()
    {
        // Append required password validation
        return User::VALIDATION_RULES + [
            'password' => 'required|min:8|confirmed',
        ];
    }
}
