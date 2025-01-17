<?php

namespace App\Traits\Validate;

use App\Models\User;
use App\Rules\AlphaNumericSpaceBarStringRule;
use App\Rules\Uuid7;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait UserValidateTrait
{
    public function validateGet(Request $request): void
    {
        $rules = [
            'user_type' => ['required', 'string', 'max:255', Rule::in(['facility_user', 'bidder'])],
            'facility_id' => [
                'nullable',
                'prohibited_if:user_type,bidder',
                Rule::exists('facilities', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'company_id' => [
                'nullable',
                'prohibited_if:user_type,facility_user',
                Rule::exists('bidder_companies', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
        $request->validate($rules);
    }

    public function validateUpdate(Request $request, User $user): void
    {
        $rules = [
            'user_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'user_name')->ignore($user->id),
                new AlphaNumericSpaceBarStringRule,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'roles' => 'nullable|array',
            'roles.*' => ['nullable', Rule::exists('roles', 'id')],
        ];
        $request->validate($rules);
    }
}