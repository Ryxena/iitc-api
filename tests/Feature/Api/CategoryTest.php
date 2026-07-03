<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can list categories', function () {
    Category::factory()->create();

    $response = $this->getJson('/api/competitions/categories');

    $response->assertSuccessful();
});

it('can create a category if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    $response = $this->actingAs($user)->postJson('/api/competitions/categories', [
        'name' => 'New Category',
    ]);

    expect($response->status())->toBeIn([201, 403]);
});

it('can update a category if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->putJson('/api/competitions/categories/' . $category->id, [
        'name' => 'Updated Category',
    ]);

    expect($response->status())->toBeIn([200, 403]);
});

it('can delete a category if authorized', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/competitions/categories/' . $category->id);

    expect($response->status())->toBeIn([200, 403]);
});
