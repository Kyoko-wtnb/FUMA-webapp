<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserModifyRequest extends FormRequest
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
     * Standard rules but email can already exist if modifying existing user
     * Add optional password with length and confirmation check
     *
     * @return array
     */
    public function rules()
    {
        // Get user id from the route and exclude from the unique email test
        $id = $this->route('user');
        // In User modification password is optional but if present requires confirmation
        $rules = User::getValidationRules($id) + [
            'password'=>'nullable|min:8|confirmed'
        ];
        return $rules;
    }
}