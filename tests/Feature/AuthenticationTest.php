<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'member'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id','name','email','role'],
                    'token'
                ]
            ]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_login_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id','name','email','role'],
                    'token'
                ]
            ]);
    }

    public function test_logout_user_requires_auth()
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401); // بدون توکن
    }
}
