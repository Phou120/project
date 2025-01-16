<?php

namespace App\Traits\Validate;

use App\Rules\AlphaNumericSpaceBarStringRule;
use App\Rules\Uuid7;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait AuthValidateTrait
{


    public function validateRegister(Request $request): void
    {
        $rules = [
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users', 'user_name'), new AlphaNumericSpaceBarStringRule],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|string|max:255|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => ['nullable', Rule::exists('roles', 'id')],
        ];
        $request->validate($rules);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function validateLogin(Request $request): void
    {
        $request->validate([
            'credentials' => 'required|max:255',
            'password' => 'required|min:2|max:255',
        ]);
    }


     /**
     * @param Request $request
     * @return void
     */
    public function validateRefreshToken(Request $request): void
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);
    }
}