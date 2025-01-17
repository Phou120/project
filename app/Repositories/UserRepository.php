<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\FormatQueryTrait;
use App\Http\Requests\QueryRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Traits\GenerateRandomValueTrait;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    // use FormatQueryTrait, GenerateRandomValueTrait;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


     /**
     * @param QueryRequest $request
     * @return mixed
     */
    public function getAll($request): mixed
    {
        return $this->user->all();
    }


    /**
     * @param Request $request
     * @return User
     */
    public function save(Request $request): User
    {
        $data = $request->all();
        $newUser = $this->user->newInstance();
        $newUser->user_name = $data['user_name'];
        $newUser->email = $data['email'];
        $newUser->password = Hash::make($data['password']);
        $newUser->save();

        /** Add Role */
        $newUser->syncRoles($data['roles']);

        return $newUser;
    }


    public function getByColumn(array $attributes): ?User
    {
        $query = $this->user->query();
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $query->where($key, $value[0], $value[1]);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

     /**
     * @param Request $request
     * @return User
     */
    public function getById($user): ?User
    {
        $user = $this->user->find($user->id);
        $user->load(['roles.permissions']);

        return $user;
    }


    /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function update(Request $request, User $user): User
    {
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->save();

        /** Add Role */
        $user->syncRoles($request['roles']);

        return $user;
    }


     /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function delete(User $user): ?User
    {
        $user = $this->user->find($user->id);
        $user->delete();

        return $user;
    }
}
