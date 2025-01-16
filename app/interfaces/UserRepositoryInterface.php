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

}