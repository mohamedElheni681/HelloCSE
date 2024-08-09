<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Admin;

class AdminRepository
{
    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }
}
