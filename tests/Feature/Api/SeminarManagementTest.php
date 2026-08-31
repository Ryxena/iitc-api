<?php

use App\Models\Seminar;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    Role::firstOrCreate(['name' => 'User']);
});

it('can fetch active seminars from public endpoint', function () {
    Seminar::create([
        'title' => 'Public Seminar 1',
        'description' => 'Test Description',
        'speaker' => 'John Doe',
        'location' => 'Online Zoom',
        'is_active' => true,
    ]);

    Seminar::create([
        'title' => 'Inactive Seminar',
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/seminars');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'success get seminar data',
        ]);

    $data = $response->json('data.seminars');
    expect(count($data))->toBe(1);
    expect($data[0]['title'])->toBe('Public Seminar 1');
});

it('can fetch specific seminar details from public endpoint', function () {
    $seminar = Seminar::create([
        'title' => 'Public Seminar Detail',
        'description' => 'Detailed Description',
        'speaker' => 'Jane Smith',
        'location' => 'Hall A',
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/seminars/{$seminar->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'seminar' => [
                    'id' => $seminar->id,
                    'title' => 'Public Seminar Detail',
                ],
            ],
        ]);
});

it('returns 404 for non-existent seminar', function () {
    $response = $this->getJson('/api/seminars/999999');
    $response->assertStatus(404);
});

it('allows superadmin to view and create seminars via admin web route', function () {
    $superadmin = User::factory()->create(['email_verified_at' => now()]);
    $superadmin->assignRole('Super Admin');

    // Access index page
    $indexResponse = $this->actingAs($superadmin)->get('/admin/seminars');
    $indexResponse->assertStatus(200);

    // Create a new seminar
    $storeResponse = $this->actingAs($superadmin)
        ->withSession(['_token' => 'test-token'])
        ->post('/admin/seminars', [
            '_token' => 'test-token',
            'title' => 'New Admin Seminar',
            'description' => 'Created by admin',
            'speaker' => 'Dr. Admin',
            'date_time' => '2026-09-01T10:00',
            'location' => 'Auditorium',
            'is_active' => '1',
        ]);

    $storeResponse->assertRedirect();
    $this->assertDatabaseHas('seminars', [
        'title' => 'New Admin Seminar',
        'speaker' => 'Dr. Admin',
    ]);
});

it('allows superadmin to update, toggle status, and delete a seminar', function () {
    $superadmin = User::factory()->create(['email_verified_at' => now()]);
    $superadmin->assignRole('Super Admin');

    $seminar = Seminar::create([
        'title' => 'Original Seminar Title',
        'is_active' => true,
    ]);

    // Update
    $updateResponse = $this->actingAs($superadmin)
        ->withSession(['_token' => 'test-token'])
        ->patch("/admin/seminars/{$seminar->id}", [
            '_token' => 'test-token',
            'title' => 'Updated Seminar Title',
            'is_active' => '1',
        ]);
    $updateResponse->assertRedirect();
    $this->assertDatabaseHas('seminars', [
        'id' => $seminar->id,
        'title' => 'Updated Seminar Title',
    ]);

    // Toggle active
    $toggleResponse = $this->actingAs($superadmin)
        ->withSession(['_token' => 'test-token'])
        ->patch("/admin/seminars/{$seminar->id}/toggle-active", [
            '_token' => 'test-token',
        ]);
    $toggleResponse->assertRedirect();
    expect($seminar->fresh()->is_active)->toBeFalse();

    // Delete
    $deleteResponse = $this->actingAs($superadmin)
        ->withSession(['_token' => 'test-token'])
        ->delete("/admin/seminars/{$seminar->id}", [
            '_token' => 'test-token',
        ]);
    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('seminars', ['id' => $seminar->id]);
});
