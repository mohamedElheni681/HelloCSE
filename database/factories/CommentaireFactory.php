<?php

namespace Database\Factories;

use App\Domain\Entities\Commentaire;
use App\Domain\Entities\Admin;
use App\Domain\Entities\Profil;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentaireFactory extends Factory
{
    protected $model = Commentaire::class;

    public function definition()
    {
        return [
            'contenu' => $this->faker->text,
            'admin_id' => Admin::factory(),
            'profil_id' => Profil::factory(),
        ];
    }
}
