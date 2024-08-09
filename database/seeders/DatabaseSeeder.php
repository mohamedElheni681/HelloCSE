<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\Admin;
use App\Domain\Entities\Profil;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un admin
        $admin = Admin::factory()->create([
            'name' => 'Admin A',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'), 
        ]);

        // Créer 5 profils pour cet admin
        $profils = Profil::factory(5)->create([
            'admin_id' => $admin->id,
        ]);

        // Ajouter un commentaire à chaque profil créé
        foreach ($profils as $profil) {
            $profil->commentaires()->create([
                'contenu' => 'Ceci est un commentaire.',
                'admin_id' => $admin->id,
            ]);
        }
    }
}
