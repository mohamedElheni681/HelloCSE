<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Entities\Admin;
use App\Domain\Entities\Profil;
use App\Domain\Entities\Commentaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;

class CommentaireTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_admin_can_create_a_commentaire()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin-api');

        $profil = Profil::factory()->create();

        $response = $this->postJson('/api/profils/' . $profil->id . '/commentaires', [
            'contenu' => 'This is a comment.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Commentaire ajouté avec succès',
            'data' => [
                'contenu' => 'This is a comment.',
            ],
        ]);
        
        $this->assertCount(1, Commentaire::all());
        $this->assertDatabaseHas('commentaires', [
            'profil_id' => $profil->id,
            'admin_id' => $admin->id,
            'contenu' => 'This is a comment.',
        ]);
    }

    /** @test */
    public function an_admin_cannot_create_multiple_commentaires_on_the_same_profil()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin-api');

        $profil = Profil::factory()->create();

        Commentaire::factory()->create([
            'admin_id' => $admin->id,
            'profil_id' => $profil->id,
            'contenu' => 'This is the first comment.',
        ]);

        $response = $this->postJson('/api/profils/' . $profil->id . '/commentaires', [
            'contenu' => 'This is a second comment.',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Vous avez déjà posté un commentaire sur ce profil',
        ]);

        $this->assertCount(1, Commentaire::all());
        $this->assertDatabaseHas('commentaires', [
            'profil_id' => $profil->id,
            'admin_id' => $admin->id,
            'contenu' => 'This is the first comment.',
        ]);
    }
}
