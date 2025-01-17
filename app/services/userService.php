<?php

namespace App\Services;

use App\Exports\BidderExport;
use App\Exports\FacilityUserExport;
use App\Http\Requests\QueryRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Traits\Validate\UserValidateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserService
{
    use UserValidateTrait;

    private UserRepositoryInterface $userRepository;


    /**
     * @param UserRepositoryInterface $userRepository
     * @param FacilityUserService $facilityUserService
     * @param BidderCompanyUserService $bidderCompanyUserService
     * @param RoleService $roleService
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param QueryRequest $request
     * @return mixed
     */
    public function getAll(Request $request): mixed
    {
        return $this->userRepository->getAll($request);
    }

    public function save(Request $request): User
    {
        return $this->userRepository->save($request);
    }


     /**
     * @param array $attributes
     * @return User|null
     */
    public function getByColumn(array $attributes): ?User
    {
        return $this->userRepository->getByColumn($attributes);
    }

     /**
     * @param array $attributes
     * @return User|null
     */
    public function getById($user): ?User
    {
        return $this->userRepository->getById($user);
    }


     /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function update(Request $request, User $user): User
    {
        $this->validateUpdate($request, $user);

        return DB::transaction(function () use ($request, $user) {
            return $this->userRepository->update($request, $user);
        });
    }


     /**
     * @param Request $request
     * @param User $user
     * @return User
     */
    public function delete(User $user): ?User
    {
        return $this->userRepository->delete($user);
    }


    /**
     * @param QueryRequest $request
     * @return BinaryFileResponse
     */
    // public function exportUser(Request $request): BinaryFileResponse
    // {
    //     $user = $this->userRepository->getAll($request);
    //     if ($request->user_type === 'facility_user') {
    //         return Excel::download(new FacilityUserExport($user), 'facility users.xlsx');
    //     } else {
    //         return Excel::download(new BidderExport($user), 'bidders.xlsx');
    //     }
    // }
}