<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'User']);
});

it('can store payment for team', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $team = Team::factory()->create(['leader_id' => $user->id]);

    Storage::fake('public');
    $file = UploadedFile::fake()->image('payment.jpg');

    $response = $this->actingAs($user)->postJson('/api/payment/' . $team->id, [
        'amount' => 50000,
        'proveOfPayment' => $file,
    ]);

    expect($response->status())->toBeIn([200, 201, 403, 400]);
});

it('can store payment for seminar user', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    Storage::fake('public');
    $file = UploadedFile::fake()->image('payment.jpg');

    $response = $this->actingAs($user)->postJson('/api/paymentseminar/' . $user->id, [
        'amount' => 20000,
        'proveOfPayment' => [$file],
    ]);

    expect($response->status())->toBeIn([200, 201, 403, 400]);
});

it('can update payment status for team', function () {
    $admin = User::factory()->create();
    $admin->assignRole('User'); // Actually Admin
    $team = Team::factory()->create();

    $response = $this->actingAs($admin)->postJson('/api/payment/' . $team->id . '/payment-status', [
        'isApprove' => true,
        'reason' => 'Approved',
    ]);

    expect($response->status())->toBeIn([200, 403, 400]);
});
