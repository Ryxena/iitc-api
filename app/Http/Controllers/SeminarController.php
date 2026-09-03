<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus;
use App\Models\Seminar;
use App\Models\SeminarRegistration;
use App\Models\Setting;
use App\Models\User;
use App\Services\CertificateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeminarController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    /**
     * [ADMIN] List all registered seminar participants.
     */
    public function index(): JsonResponse
    {

        $registrations = SeminarRegistration::query()
            ->with('user')
            ->get();

        $data = $registrations->map(function (SeminarRegistration $reg) {
            $user = $reg->user;
            $team = $user ? TeamController::findUserTeam($user) : null;
            $winner = $team?->winner;
            $participantLabel = Setting::get('non_winner_label', 'Partisipasi');

            $certificateSeminar = $reg->certificate_path
                ? Storage::disk('public')->url($reg->certificate_path)
                : null;

            return [
                'userId' => $reg->user_id,
                'name' => $user->name ?? null,
                'email' => $user->email ?? null,
                'phone' => $user->phone ?? null,
                'attended' => $reg->attended,
                'certificateNumber' => $reg->certificate_number,
                'certificateSeminar' => $certificateSeminar,
                'teamId' => $team?->id,
                'teamName' => $team?->name,
                'competitionName' => $team?->competition?->name,
                'winnerStatus' => $winner
                    ? "{$winner->award_title} (Rank {$winner->rank})"
                    : ($team ? $participantLabel : null),
            ];
        });

        return $this->success('success get all seminar registrations', ['registrations' => $data]);
    }

    /**
     * [USER] Register self for the seminar (free, no payment required).
     */
    public function register(Request $request): JsonResponse
    {
        $user = $request->user();

        $alreadyRegistered = SeminarRegistration::query()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRegistered) {
            return $this->error('You have already registered for this seminar.', 409);
        }

        SeminarRegistration::query()->create([
            'user_id' => $user->id,
            'attended' => false,
        ]);

        return $this->success('Seminar registration successful. See you there!', [
            'userId' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * [USER / ADMIN] Get a single user's seminar registration and certificate status.
     */
    public function show(string $userId): JsonResponse
    {
        $user = User::query()->findOrFail($userId);

        $registration = SeminarRegistration::query()
            ->where('user_id', $userId)
            ->first();

        if ($registration === null) {
            return $this->error('This user has not registered for the seminar.', 404);
        }

        return $this->success('success get seminar registration', [
            'userId' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'attended' => $registration->attended,
            'certificateNumber' => $registration->certificate_number,
            'certificateUrl' => $registration->certificate_path
                ? Storage::disk('public')->url($registration->certificate_path)
                : null,
        ]);
    }

    /**
     * [ADMIN] Verify attendance and auto-generate the participant's certificate.
     */
    public function verifyAttendance(Request $request, string $userId): JsonResponse
    {
        $this->authorize('update', [SeminarRegistration::class, new SeminarRegistration]);

        $user = User::query()->findOrFail($userId);

        $registration = SeminarRegistration::query()
            ->where('user_id', $userId)
            ->first();

        if ($registration === null) {
            return $this->error('This user has not registered for the seminar.', 404);
        }

        $isApprove = (bool) $request->input('isApprove', false);

        if (! $isApprove) {
            $registration->update(['attended' => false]);

            return $this->success('Attendance marked as not attended.', [
                'userId' => $userId,
                'attended' => false,
            ]);
        }

        // Already has a certificate — just return existing URL
        if ($registration->certificate_path !== null) {
            return $this->success('Attendance already verified, certificate already issued.', [
                'userId' => $userId,
                'attended' => true,
                'certificateNumber' => $registration->certificate_number,
                'certificateUrl' => Storage::disk('public')->url($registration->certificate_path),
            ]);
        }

        // Generate certificate
        $cert = $this->certificateService->generate($user->name, $user->id);

        $registration->update([
            'attended' => true,
            'certificate_number' => $cert['certificate_number'],
            'certificate_path' => $cert['certificate_path'],
        ]);

        return $this->success('Attendance verified and certificate generated successfully.', [
            'userId' => $userId,
            'attended' => true,
            'certificateNumber' => $cert['certificate_number'],
            'certificateUrl' => $cert['url'],
        ]);
    }

    public function myCertificate(Request $request): JsonResponse
    {
        $user = $request->user();
        $team = TeamController::findUserTeam($user);
        $winner = $team?->winner;
        $participantLabel = Setting::get('non_winner_label', 'Partisipasi');

        if (! $team) {
            return $this->error('Didnt have team cannot create certificate', 404);
        }

        // Check registration deadline — team created before deadline is eligible
        $deadline = Carbon::parse($team->competition->deadline);
        if ($team->created_at->gt($deadline)) {
            return $this->error('Cannot create certificate because registration is closed', 403);
        }

        // Check payment status
        $paymentStatus = $team->paymentStatus->status ?? null;
        if ($paymentStatus !== PaymentStatus::VALID) {
            return $this->error('Cannot create certificate because payment is not done yet / not valid', 403);
        }

        // Create registration if not exists, then generate number
        $registration = SeminarRegistration::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['attended' => true],
        );

        if (! $registration->certificate_number) {
            $number = $this->certificateService->generateCertificateNumber();
            $registration->update(['certificate_number' => $number]);
            $registration->refresh();
        }

        return $this->success('success get my certificate', [
            'seminarName' => Seminar::where('is_active', true)->value('title'),
            'name' => $user->name,
            'email' => $user->email,
            'teamId' => $team->id,
            'teamName' => $team->name,
            'competitionName' => $team->competition?->name,
            'paymentStatus' => $paymentStatus,
            'certificateNumber' => $registration->certificate_number,
            'certificateUrl' => $registration->certificate_path
                ? Storage::disk('public')->url($registration->certificate_path)
                : null,
            'winnerStatus' => $winner
                ? $winner->award_title
                : $participantLabel,
        ]);
    }
}
