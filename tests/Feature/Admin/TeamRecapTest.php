<?php

use App\Helpers\PaymentStatus as PaymentStatusHelper;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Member;
use App\Models\Participant;
use App\Models\PaymentStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->event = Event::query()->create([
        'name' => 'IITC 2026',
        'description' => 'Test Event Description',
        'is_active' => true,
    ]);

    $this->competition = Competition::query()->create([
        'name' => 'Web Development',
        'slug' => 'web-development',
        'description' => 'Test Competition Description',
        'guide_book' => 'https://example.com/guide.pdf',
        'deadline' => now()->addDays(30),
        'max_members' => 3,
        'price' => 50000,
        'event_id' => $this->event->id,
    ]);
});

test('guest cannot access teams recap page', function () {
    $response = $this->get(route('admin.teams.recap'));
    $response->assertRedirect('/login');
});

test('admin can access teams recap page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.teams.recap'));

    $response->assertStatus(200);
    $response->assertSee('Recap Peserta Lomba');
    $response->assertSee('Web Development');
});

test('admin can filter teams by search, competition, and status', function () {
    $leader = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    Participant::query()->create([
        'user_id' => $leader->id,
        'grade' => 'STUDENT',
        'gender' => 'L',
        'student_id_number' => '12345678',
        'institution' => 'Universitas Technology',
    ]);

    $team = Team::query()->create([
        'name' => 'Alpha Team',
        'code' => 'ALPHA123',
        'leader_id' => $leader->id,
        'competition_id' => $this->competition->id,
    ]);

    PaymentStatus::query()->create([
        'team_id' => $team->id,
        'status' => PaymentStatusHelper::VALID,
        'reason' => 'Payment approved',
    ]);

    // Search matching
    $resMatch = $this->actingAs($this->admin)->get(route('admin.teams.recap', ['search' => 'Alpha']));
    $resMatch->assertStatus(200);
    $resMatch->assertSee('Alpha Team');

    // Search non-matching
    $resNoMatch = $this->actingAs($this->admin)->get(route('admin.teams.recap', ['search' => 'NonExistent']));
    $resNoMatch->assertStatus(200);
    $resNoMatch->assertDontSee('Alpha Team');

    // Filter status VALID
    $resValid = $this->actingAs($this->admin)->get(route('admin.teams.recap', ['status' => 'VALID']));
    $resValid->assertStatus(200);
    $resValid->assertSee('Alpha Team');

    // Filter status INVALID
    $resInvalid = $this->actingAs($this->admin)->get(route('admin.teams.recap', ['status' => 'INVALID']));
    $resInvalid->assertStatus(200);
    $resInvalid->assertDontSee('Alpha Team');
});

test('admin can export teams recap CSV', function () {
    $leader = User::factory()->create(['name' => 'Jane Smith']);
    Participant::query()->create([
        'user_id' => $leader->id,
        'grade' => 'STUDENT',
        'gender' => 'P',
        'student_id_number' => '87654321',
        'institution' => 'Universitas Sains',
    ]);

    Team::query()->create([
        'name' => 'Beta Team',
        'code' => 'BETA456',
        'leader_id' => $leader->id,
        'competition_id' => $this->competition->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.teams.recap.export', ['export_type' => 'teams']));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('Beta Team');
});

test('admin can export participant roster CSV', function () {
    $leader = User::factory()->create(['name' => 'Leader User']);
    Participant::query()->create([
        'user_id' => $leader->id,
        'grade' => 'STUDENT',
        'gender' => 'L',
        'student_id_number' => '112233',
        'institution' => 'Institut Tekno',
    ]);

    $member = User::factory()->create(['name' => 'Member User']);
    Participant::query()->create([
        'user_id' => $member->id,
        'grade' => 'STUDENT',
        'gender' => 'L',
        'student_id_number' => '445566',
        'institution' => 'Institut Tekno',
    ]);

    $team = Team::query()->create([
        'name' => 'Gamma Squad',
        'code' => 'GAMMA789',
        'leader_id' => $leader->id,
        'competition_id' => $this->competition->id,
    ]);

    Member::query()->create([
        'team_id' => $team->id,
        'user_id' => $member->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.teams.recap.export', ['export_type' => 'participants']));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('Nama Tim');
    expect($response->streamedContent())->toContain('Tanggal Daftar Tim');
    expect($response->streamedContent())->toContain('Ketua Tim');
    expect($response->streamedContent())->toContain('Anggota Tim');
    expect($response->streamedContent())->toContain('Leader User');
    expect($response->streamedContent())->toContain('Member User');
    expect($response->streamedContent())->toContain('Gamma Squad');
});
