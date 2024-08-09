<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Entities\Admin;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->assertDatabaseHas('admins', ['email' => 'admin@example.com']);
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
}
