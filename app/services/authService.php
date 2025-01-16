<?php

namespace App\Services;

use App\Models\User;
use App\Traits\Validate\AuthValidateTrait;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Throwable;

class AuthService
{
    use AuthValidateTrait;

    private UserService $userService;
    // private RoleService $roleService;

    /**
     * Create a new class instance.
     *
     * @param UserService $userService
     * @param RoleService $roleService
     * @param FacilityUserService $facilityUserService
     * @param BidderCompanyUserService $bidderCompanyUserService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request): User
    {
        $this->validateRegister($request);

        return DB::transaction(function () use ($request) {
            $newUser = $this->userService->save($request);

            return $newUser;
        });
    }


    /**
     * @param Request $request
     * @return array
     *
     * @throws ConnectionException
     */
    public function login(Request $request): array
    {
        $this->validateLogin($request);
        $credential = $this->checkCredentials($request->credentials, $request->password);
        if (!empty($credential['access_token'])) {
            $user = $this->userService->getByColumn(['email' => $request->credentials]);
            // if ($user->status !== true) {
            //     abort(403, 'fail.account_was_disabled');
            // }
            $user->load(['roles.permissions']);

            return [
                'credentials' => $credential,
                'user' => $user,
            ];
        }
        abort(401, 'fail.invalid_credential');
    }


     /**
     * @param Request $request
     * @return array|null
     *
     * @throws ConnectionException
     */
    public function refreshToken(Request $request): ?array
    {
        $this->validateRefreshToken($request);
        $credential = $this->checkCredentials(null, null, 'refresh_token', $request->refresh_token);
        if (!empty($credential['access_token'])) {
            $request->headers->set('Authorization', 'Bearer ' . $credential['access_token']);
            Auth('api')->setRequest($request);
            $user = Auth('api')->user();

            // if ($user->status !== true) {
            //     abort(403, __('fail.account_was_disabled'));
            // }

            return [
                'credentials' => $credential,
                'user' => $user->load(['roles.permissions']),
            ];
        }
        abort(401, 'fail.invalid_credential');
    }



    /**
     * @param string|null $credential
     * @param string|null $password
     * @param string $grantType
     * @param string|null $refreshToken
     * @return array|null
     *
     * @throws ConnectionException
     */
    private function checkCredentials(?string $credential = null, ?string $password = null, string $grantType = 'password', ?string $refreshToken = null): ?array
    {
        $params = [
            'grant_type' => $grantType,
            'client_id' => config('passport.personal_access_client.id'),
            'client_secret' => config('passport.personal_access_client.secret'),
            'scope' => '',
        ];
        if ($grantType === 'password') {
            $params['username'] = $credential; // Use 'username' instead of 'name'
            $params['password'] = $password;
        } elseif ($grantType === 'refresh_token') {
            $params['refresh_token'] = $refreshToken;
        } else {
            abort(400, 'fail.grant_type_incorrect');
        }

        return Http::asForm()->acceptJson()->post(config('passport.personal_access_client.url'), $params)->json();
    }
}
