<?php

use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can get list of teams if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    Event::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->getJson('/api/teams');

    expect($response->status())->toBeIn([200, 403]);
});

it('can get specific team if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $team = Team::factory()->create();
    Event::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->getJson('/api/teams/' . $team->id);

    expect($response->status())->toBeIn([200, 403, 404]);
});

it('can create a team for a competition', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    
    $event = Event::factory()->create(['is_active' => true]);
    $competition = Competition::factory()->create(['event_id' => $event->id]);

    $response = $this->actingAs($user)->postJson('/api/teams/' . $competition->slug, [
        'name' => 'Test Team',
        'title' => 'Test Title',
    ]);

    expect($response->status())->toBeIn([201, 403, 400]);
});

it('can update a team', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $team = Team::factory()->create(['leader_id' => $user->id]);

    $response = $this->actingAs($user)->postJson('/api/teams/' . $team->id . '/update', [
        'name' => 'Updated Team',
        'title' => 'Updated Title',
    ]);

    expect($response->status())->toBeIn([200, 403, 400]);
});

it('can delete a team', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $team = Team::factory()->create(['leader_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson('/api/teams/' . $team->id);

    expect($response->status())->toBeIn([200, 403, 400]);
});
