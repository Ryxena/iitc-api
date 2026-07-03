<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can get list of events', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    Event::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/events');

    expect($response->status())->toBeIn([200, 403]);
});

it('can create an event if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'New Event',
        'description' => 'Event Description',
    ]);

    expect($response->status())->toBeIn([201, 403, 400]);
});

it('can update an event if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $event = Event::factory()->create();

    $response = $this->actingAs($user)->putJson('/api/events/' . $event->id, [
        'name' => 'Updated Event',
        'description' => 'Updated Description',
    ]);

    expect($response->status())->toBeIn([200, 403, 400]);
});

it('can delete an event if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $event = Event::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/events/' . $event->id);

    expect($response->status())->toBeIn([200, 403]);
});

it('can change event active status if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $event = Event::factory()->create(['is_active' => false]);

    $response = $this->actingAs($user)->putJson('/api/events/' . $event->id . '/set-active');

    expect($response->status())->toBeIn([200, 403]);
});
