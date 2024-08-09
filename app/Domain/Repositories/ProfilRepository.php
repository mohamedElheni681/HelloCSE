<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Profil;

class ProfilRepository
{
    public function create(array $data): Profil
    {
        return Profil::create($data);
    }

    public function update(Profil $profil, array $data): bool
    {
        return $profil->update($data);
    }

    public function delete(Profil $profil): bool
    {
        return $profil->delete();
    }

    public function findActive($perPage = 15)
    {
        return Profil::where('statut', 'actif')
                 ->paginate($perPage, ['id', 'nom', 'prenom', 'image', 'created_at', 'updated_at']);
    }

}
