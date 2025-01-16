<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->success($this->userService->getAll($request));
    }

    // /**
    //  * @param User $user
    //  * @return JsonResponse
    //  */
    // public function show(User $user): JsonResponse
    // {
    //     return $this->success($this->userService->getById($user));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, User $user): JsonResponse
    // {
    //     return $this->success($this->userService->update($request, $user));
    // }

    // public function exportUser(QueryRequest $request)
    // {
    //     return $this->userService->exportUser($request);
    // }
}