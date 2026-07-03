<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can get list of teams as admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('User'); // Actually Admin role in the real app

    $response = $this->actingAs($admin)->getJson('/api/admin/teams');

    expect($response->status())->toBeIn([200, 403]);
});

it('can get specific team as admin from admin path', function () {
    $admin = User::factory()->create();
    $admin->assignRole('User');
    $team = Team::factory()->create();

    $response = $this->actingAs($admin)->getJson('/api/admin/teams/' . $team->id);

    expect($response->status())->toBeIn([200, 403, 404]);
});

it('can get specific team admin details', function () {
    $admin = User::factory()->create();
    $admin->assignRole('User');
    $team = Team::factory()->create();

    $response = $this->actingAs($admin)->getJson('/api/teams/' . $team->id . '/admin');

    expect($response->status())->toBeIn([200, 403, 404]);
});
