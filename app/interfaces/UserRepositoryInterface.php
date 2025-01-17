<?php

namespace App\Interfaces;

use App\Http\Requests\QueryRequest;
use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    /**
     * @param QueryRequest $request
     * @return mixed
     */
    public function getAll($request): mixed;

     /**
     * @param Request $request
     * @return User
     */
    public function save(Request $request): User;

      /**
     * @param array $attributes
     * @return User|null
     */
    public function getByColumn(array $attributes): ?User;


      /**
     * @param $user
     * @return User|null
     */
    public function getById($user): ?User;


    /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function update(Request $request, User $user): User;


    /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function delete(User $user): ?User;

}