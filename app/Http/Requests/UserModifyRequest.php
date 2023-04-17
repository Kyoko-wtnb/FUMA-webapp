<?php

namespace fuma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use fuma\User;

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
        // In User modification password is optional
        $rules = User::getValidationRules() + [
            'password'=>'nullable|min:8|confirmed'
        ];
        // Get user id from the route and exclude from the unique email test
        // email = <other rules>|unique:<table>,<column>,<except id>
        $id = $this->route('user');
        if ($id !== null) {
            $rules['email'] .= ",$id";
        }
        Log::info('Modify rules: '.implode(',', $rules));
        return $rules;
    }
}
