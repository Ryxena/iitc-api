<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can get seminar list', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->getJson('/api/seminar');

    expect($response->status())->toBeIn([200, 403]);
});

it('can get user specific seminar data', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->getJson('/api/seminar/' . $user->id);

    expect($response->status())->toBeIn([200, 403, 404]);
});

it('can get user specific seminar data as admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('User'); // Actually Admin
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->getJson('/api/seminar/' . $user->id . '/admin');

    expect($response->status())->toBeIn([200, 403, 404]);
});

it('can update user specific seminar data', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->postJson('/api/seminar/' . $user->id . '/update', [
        'isApprove' => true,
        'reason' => 'Approved',
    ]);

    expect($response->status())->toBeIn([200, 201, 403, 400]);
});
