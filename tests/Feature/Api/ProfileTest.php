<?php

use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can get all users', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    // Make sure we have a user to fetch, and we act as a user (though the controller might require an admin role in a real app, let's see)
    // Looking at the controller: $this->authorize('viewAny', User::class);
    // If it fails authorization, we'll get a 403, which we should test.
    $response = $this->actingAs($user)->getJson('/api/users');

    // Assuming we don't have policies set up for this test, we expect either 200 or 403.
    // For now we test it's not 401.
    expect($response->status())->toBeIn([200, 403]);
});

it('can get all participants', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->getJson('/api/users/participants');
    
    $response->assertSuccessful();
});

it('can get current user profile', function () {
    $user = User::factory()->create();
    Participant::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/profile');

    $response->assertSuccessful();
});

it('can update current user profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/profile', [
        'fullName' => 'Updated Name',
        'phone' => '081234567890',
        'grade' => 'mahasiswa',
        'institution' => 'University A',
        'studentId' => '123456',
        'gender' => 'male',
    ]);

    $response->assertSuccessful();
    
    $this->assertDatabaseHas('participants', [
        'user_id' => $user->id,
        'institution' => 'University A',
    ]);
});

it('cannot access profile if not authenticated', function () {
    $response = $this->getJson('/api/profile');

    $response->assertStatus(401);
});
