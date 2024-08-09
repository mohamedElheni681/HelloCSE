<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Domain\Entities\Admin;

class AdminService
{
    protected $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function register(array $data): Admin
    {
        return $this->adminRepository->create($data);
    }

    public function login(array $data): ?string
    {
        $admin = $this->adminRepository->findByEmail($data['email']);

        if ($admin && Hash::check($data['password'], $admin->password)) {
            return $admin->createToken('admin-token')->plainTextToken;
        }

        return null;
    }
}
