<?php

namespace Database\Factories;

use App\Domain\Entities\Profil;
use App\Domain\Entities\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilFactory extends Factory
{
    protected $model = Profil::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'image' => 'default.jpg',
            'statut' => 'actif',
            'admin_id' => Admin::factory(),
        ];
    }
}
