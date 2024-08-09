<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Entities\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_register()
    {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);

        // Vérification que l'utilisateur est bien enregistré dans la base de données
        $this->assertDatabaseHas('admins', ['email' => 'admin@example.com']);

        // Vérification que le mot de passe est bien haché
        $admin = Admin::where('email', 'admin@example.com')->first();
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    /** @test */
    public function an_admin_can_login()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->dump(); // Cela affichera la réponse JSON dans la console

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid credentials']);
    }
}
