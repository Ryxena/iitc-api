<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus;
use App\Services\CompetitionCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParticipantCertificateController extends Controller
{
    public function download(Request $request, CompetitionCertificateService $certificateService): JsonResponse
    {
        $user = $request->user();
        $team = TeamController::findUserTeam($user);

        if (! $team) {
            return $this->error('You are not in any team for the active event.', 404);
        }

        // Check if team is paid (PaymentStatus::VALID)
        $paymentStatus = $team->paymentStatus->status ?? null;
        if ($paymentStatus !== PaymentStatus::VALID) {
            return $this->error('Your team payment is not valid yet.', 403);
        }

        // Ensure the team is not a winner
        if ($team->winner()->exists()) {
            return $this->error('Winners receive a different certificate.', 403);
        }

        $data = $certificateService->generateForParticipant($user, $team);

        return $this->success('Certificate generated successfully.', $data);
    }

    public function preview(Request $request, CompetitionCertificateService $certificateService)
    {
        $user = $request->user();
        $team = TeamController::findUserTeam($user);

        if (! $team) {
            return $this->error('You are not in any team for the active event.', 404);
        }

        if (($team->paymentStatus->status ?? null) !== PaymentStatus::VALID) {
            return $this->error('Your team payment is not valid yet.', 403);
        }

        if ($team->winner()->exists()) {
            return $this->error('Winners receive a different certificate.', 403);
        }

        return $certificateService->previewForParticipant($user, $team);
    }
}
