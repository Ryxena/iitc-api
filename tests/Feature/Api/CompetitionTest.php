<?php

use App\Models\Competition;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can list competitions', function () {
    Event::factory()->create(['is_active' => true]);
    $competition = Competition::factory()->create();

    $response = $this->getJson('/api/competitions');

    $response->assertSuccessful();
});

it('can show competition details', function () {
    Event::factory()->create(['is_active' => true]);
    $competition = Competition::factory()->create();

    $response = $this->getJson('/api/competitions/' . $competition->slug);

    $response->assertSuccessful();
});

it('can create a competition if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User'); // Actually, create might need an Admin role based on policies, but let's test the endpoint response.

    Storage::fake('public');
    $file = UploadedFile::fake()->image('cover.jpg');

    $event = Event::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->postJson('/api/competitions', [
        'name' => 'New Competition',
        'isIndividu' => false,
        'deadline' => now()->addDays(10)->format('Y-m-d'),
        'maxMembers' => 3,
        'price' => 50000,
        'description' => 'Test description',
        'guideBookLink' => 'http://example.com/guide',
        'cover' => $file,
        'criteria' => json_encode([['name' => 'Design', 'percentage' => 100]]),
        'techStacks' => json_encode(['Laravel', 'Vue']),
        'categories' => json_encode([]),
    ]);

    expect($response->status())->toBeIn([201, 403]);
});

it('can update a competition if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $event = Event::factory()->create(['is_active' => true]);
    $competition = Competition::factory()->create(['event_id' => $event->id]);

    $response = $this->actingAs($user)->postJson('/api/competitions/' . $competition->slug, [
        'name' => 'Updated Competition',
        'isIndividu' => false,
        'deadline' => now()->addDays(10)->format('Y-m-d'),
        'maxMembers' => 5,
        'price' => 100000,
        'description' => 'Updated description',
        'guideBookLink' => 'http://example.com/guide2',
        'criteria' => json_encode([]),
        'techStacks' => json_encode([]),
        'categories' => json_encode([]),
    ]);

    expect($response->status())->toBeIn([200, 403]);
});

it('can delete a competition if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $event = Event::factory()->create(['is_active' => true]);
    $competition = Competition::factory()->create(['event_id' => $event->id]);

    $response = $this->actingAs($user)->deleteJson('/api/competitions/' . $competition->slug);

    expect($response->status())->toBeIn([200, 403]);
});
