<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_register_a_user()
    {
        $payload = [
            'firstName' => 'Hansani',
            'lastName' => 'Wathsala',
            'email' => 'hansani@gmail.com',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at'
            ],
            'token',
            'msg'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'hansani@gmail.com'
        ]);
    }
    /** @test */
    public function it_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'hansani@gmail.com']);

        $payload = [
            'firstName' => 'Hansani',
            'lastName' => 'Wathsala',
            'email' => 'hansani@gmail.com',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('email');
    }
    /** @test */
    public function it_can_login_a_user()
    {
        $user = User::factory()->create([
            'email' => 'hansani@gmail.com',
            'password' => bcrypt('123456')
        ]);

        $payload = [
            'email' => 'hansani@gmail.com',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email'
            ]
        ]);
    }
    /** @test */
    public function it_cannot_login_with_invalid_credentials()
    {
        $payload = [
            'email' => 'wathsala@gmail.com',
            'password' => '147258'
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'Invalid credentials'
        ]);
    }
}
