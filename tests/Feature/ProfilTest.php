<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Entities\Profil;
use App\Domain\Entities\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_a_profil()
    {
        $this->actingAsAdmin();
        
        Storage::fake('public');
        
        $response = $this->postJson('/api/profils', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'image' => $file = UploadedFile::fake()->image('avatar.jpg'),
            'statut' => 'actif',
        ]);
        
        $response->assertStatus(201);

        // Récupérer le profil pour obtenir son ID
        $profil = Profil::where('nom', 'Doe')->first();
        $this->assertNotNull($profil);

        $expectedPath = "images/profils/{$profil->id}/avatar.jpg";
        
        // Vérifier que l'image est stockée dans le bon répertoire
        $this->assertEquals($expectedPath, $profil->image);
    }

    /** @test */
    public function an_admin_can_update_a_profil()
    {
        $this->actingAsAdmin();

        Storage::fake('public');

        // Créer un profil sans image initiale
        $profil = Profil::factory()->create();

        $profil->update([
            'image' => "images/profils/{$profil->id}/original.jpg",
        ]);

        // Ajouter l'image originale pour la tester
        Storage::disk('public')->put("images/profils/{$profil->id}/original.jpg", 'dummy content');

        // Metter à jour le profil avec une nouvelle image
        $response = $this->putJson("/api/profils/{$profil->id}", [
           'nom' => 'Doe Updated',
           'prenom' => 'John Updated',
           'image' => $newImage = UploadedFile::fake()->image('avatar.jpg'),
            'statut' => 'inactif',
        ]);

        $response->assertStatus(200);

        // Vérifier que l'ancienne image a été supprimée
        Storage::disk('public')->assertMissing("images/profils/{$profil->id}/original.jpg");

        // Vérifier que la nouvelle image a été stockée correctement
        $profil->refresh();
        $newImageName = $newImage->getClientOriginalName();
        Storage::disk('public')->assertExists("images/profils/{$profil->id}/{$newImageName}");

        // Vérifier les données mises à jour dans la base de données
        $this->assertDatabaseHas('profils', [
           'id' => $profil->id,
           'nom' => 'Doe Updated',
           'prenom' => 'John Updated',
           'statut' => 'inactif',
           'image' => "images/profils/{$profil->id}/{$newImageName}",  // Vérification du chemin correct dans la base de données
        ]);
    }

    /** @test */
    public function an_admin_can_delete_a_profil()
    {
        $this->actingAsAdmin();

        $profil = Profil::factory()->create();

        $response = $this->deleteJson("/api/profils/{$profil->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('profils', ['id' => $profil->id]);
    }

    /** @test */
    public function it_lists_active_profils()
    {
        $activeProfil = Profil::factory()->create(['statut' => 'actif']);
        $inactiveProfil = Profil::factory()->create(['statut' => 'inactif']);

        $response = $this->getJson('/api/profils');

        $response->assertStatus(200);
        $response->assertJsonFragment(['nom' => $activeProfil->nom]);
        $response->assertJsonMissing(['nom' => $inactiveProfil->nom]);
    }

    /** @test */
    public function it_lists_active_profils_for_authenticated_users()
    {
        $admin = Admin::factory()->create();
        $token = $admin->createToken('Test Token')->plainTextToken;
        $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->getJson('/api/profils');
        
        $activeProfil = Profil::factory()->create(['statut' => 'actif']);
        $inactiveProfil = Profil::factory()->create(['statut' => 'inactif']);
        
        $response = $this->getJson('/api/profils');
        
        $response->assertStatus(200);
        $response->assertJsonFragment(['nom' => $activeProfil->nom]);
        $response->assertJsonFragment(['statut' => $activeProfil->statut]);
        $response->assertJsonMissing(['nom' => $inactiveProfil->nom]);
    }

    /** @test */
    public function it_lists_active_profils_for_unauthenticated_users_without_statut()
    {
        $activeProfil = Profil::factory()->create(['statut' => 'actif']);
        $inactiveProfil = Profil::factory()->create(['statut' => 'inactif']);

        $response = $this->getJson('/api/profils');

        $response->assertStatus(200);
        $response->assertJsonFragment(['nom' => $activeProfil->nom]);
        $response->assertJsonMissing(['statut' => $activeProfil->statut]);
        $response->assertJsonMissing(['nom' => $inactiveProfil->nom]);
    }

    private function actingAsAdmin()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin-api');
    }
}
