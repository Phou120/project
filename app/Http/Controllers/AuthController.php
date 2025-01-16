<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    private AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


     /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function register(Request $request): JsonResponse
    {
        return $this->success($this->authService->register($request));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws ConnectionException
     */
    public function login(Request $request): JsonResponse
    {
        return $this->success($this->authService->login($request));
    }

     /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws ConnectionException
     */
    public function refreshToken(Request $request): JsonResponse
    {
        return $this->success($this->authService->refreshToken($request));
    }

}
