<?php

use App\Helpers\PaymentStatus;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use App\Services\CompetitionCertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    $this->event = Event::factory()->create(['is_active' => true]);
    $this->competition = Competition::factory()->create(['event_id' => $this->event->id]);
    $this->user = User::factory()->create();

    $this->team = Team::factory()->create([
        'leader_id' => $this->user->id,
        'competition_id' => $this->competition->id,
    ]);
});

it('prevents download if team is not paid', function () {
    $this->team->paymentStatus()->create(['status' => PaymentStatus::PENDING, 'reason' => 'pending']);

    actingAs($this->user)
        ->getJson('/api/profile/certificate')
        ->assertStatus(403)
        ->assertJsonFragment(['message' => 'Your team payment is not valid yet.']);
});

it('prevents download if team is winner', function () {
    $this->team->paymentStatus()->create(['status' => PaymentStatus::VALID, 'reason' => 'paid']);
    $this->team->winner()->create(['rank' => 1, 'award_title' => '1st']);

    actingAs($this->user)
        ->getJson('/api/profile/certificate')
        ->assertStatus(403)
        ->assertJsonFragment(['message' => 'Winners receive a different certificate.']);
});

it('allows non-winning paid participants to download certificate', function () {
    $this->team->paymentStatus()->create(['status' => PaymentStatus::VALID, 'reason' => 'paid']);

    $mock = Mockery::mock(CompetitionCertificateService::class);
    $mock->shouldReceive('generateForParticipant')->once()->andReturn([
        'certificate_number' => 'IITC-1234',
        'certificate_path' => 'path/to/cert.pdf',
        'url' => 'http://example.com/cert.pdf',
    ]);
    app()->instance(CompetitionCertificateService::class, $mock);

    actingAs($this->user)
        ->getJson('/api/profile/certificate')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'certificate_number',
                'certificate_path',
                'url',
            ],
        ]);
});
