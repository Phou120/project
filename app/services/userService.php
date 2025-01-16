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
    // use UserValidateTrait;

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
}
