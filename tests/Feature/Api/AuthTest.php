<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    // Register method requires 'User' role to exist
    Role::firstOrCreate(['name' => 'User']);
});

it('can register a new user', function () {
    $response = $this->postJson('/api/register', [
        'fullName' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '1234567890',
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

it('can login a user', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'access_token'
            ]
        ]);
});

it('fails to login with wrong password', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

it('can logout an authenticated user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    $response->assertSuccessful();
});

it('can request password reset link', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertSuccessful();
});
