<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Commentaire;

class CommentaireRepository
{
    public function create(array $data): Commentaire
    {
        return Commentaire::create($data);
    }
}
