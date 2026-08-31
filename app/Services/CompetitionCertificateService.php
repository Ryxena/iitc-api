<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class CompetitionCertificateService
{
    /**
     * Generate next sequential certificate number for competitions, e.g. IITC-COMP-2026-0001
     */
    public function generateCertificateNumber(): string
    {
        // For simplicity, we can just use a timestamp or a DB lookup
        // However, a simple unique hash or timestamp works if we don't have a dedicated table.
        // Or we can just use the user ID / team ID to ensure uniqueness.
        $next = rand(1000, 9999);

        return sprintf('IITC-COMP-%d-%04d', now()->year, $next);
    }

    /**
     * Generate the PDF certificate for a non-winning participant.
     *
     * @return array{certificate_number: string, certificate_path: string, url: string}
     */
    public function generateForParticipant(User $user, Team $team): array
    {
        $certificateNumber = $this->generateCertificateNumber();
        $participantName = $user->name;

        // Create new PDF document
        // Landscape A4
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins to 0 for full background image
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);

        // Add a page
        $pdf->AddPage();

        // Get template path
        $templatePath = base_path('Example certificate.jpg');

        if (file_exists($templatePath)) {
            // A4 dimensions: 297 x 210 mm
            $pdf->Image($templatePath, 0, 0, 297, 210, 'JPG', '', '', false, 300, '', false, false, 0);
        }

        // Set font
        $pdf->SetFont('', 'B', 30);
        $pdf->SetTextColor(0, 0, 0);

        // Overlay Participant Name (Adjust Y position as needed)
        // SetXY(X, Y) in mm
        $pdf->SetXY(10, 100);
        $pdf->Cell(277, 15, $participantName, 0, 1, 'C');

        // Overlay Certificate Number
        $pdf->SetFont('', '', 14);
        $pdf->SetXY(10, 180);
        $pdf->Cell(277, 10, 'Certificate No: '.$certificateNumber, 0, 1, 'C');

        // Save PDF to storage
        $relativePath = "certificates/competition/{$team->competition->slug}/{$user->id}.pdf";
        $pdfContent = $pdf->Output('', 'S');
        Storage::disk('public')->put($relativePath, $pdfContent);

        $url = Storage::disk('public')->url($relativePath);

        return [
            'certificate_number' => $certificateNumber,
            'certificate_path' => $relativePath,
            'url' => $url,
        ];
    }

    /**
     * Generate the certificate for a non-winning participant and stream it inline (preview).
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response
     */
    public function previewForParticipant($user, $team)
    {
        $certificateNumber = $this->generateCertificateNumber($team);
        $participantName = $user->name;

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        $templatePath = base_path('Example certificate.jpg');

        if (file_exists($templatePath)) {
            $pdf->Image($templatePath, 0, 0, 297, 210, 'JPG', '', '', false, 300, '', false, false, 0);
        }

        $pdf->SetFont('', 'B', 30);
        $pdf->SetTextColor(0, 0, 0);

        // --- NAME COORDINATES ---
        $pdf->SetXY(10, 100);
        $pdf->Cell(277, 15, $participantName, 0, 1, 'C');

        $pdf->SetFont('', '', 14);

        // --- CERTIFICATE NUMBER COORDINATES ---
        $pdf->SetXY(10, 180);
        $pdf->Cell(277, 10, 'Certificate No: '.$certificateNumber, 0, 1, 'C');

        // Return as a proper Laravel response for inline viewing
        $pdfContent = $pdf->Output('certificate-'.$certificateNumber.'.pdf', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-'.$certificateNumber.'.pdf"',
        ]);
    }

    /**
     * Preview the certificate template in the browser with dummy data.
     *
     * @return Response
     */
    public function preview()
    {
        $certificateNumber = 'IITC-COMP-2026-1234';
        $participantName = 'John Doe Participant';

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        $templatePath = base_path('Example certificate.jpg');

        if (file_exists($templatePath)) {
            $pdf->Image($templatePath, 0, 0, 297, 210, 'JPG', '', '', false, 300, '', false, false, 0);
        }

        $pdf->SetFont('', 'B', 30);
        $pdf->SetTextColor(0, 0, 0);

        // --- NAME COORDINATES ---
        $pdf->SetXY(10, 100);
        $pdf->Cell(277, 15, $participantName, 0, 1, 'C');

        $pdf->SetFont('', '', 14);

        // --- CERTIFICATE NUMBER COORDINATES ---
        $pdf->SetXY(10, 180);
        $pdf->Cell(277, 10, 'Certificate No: '.$certificateNumber, 0, 1, 'C');

        // Return as a proper Laravel response for inline viewing
        $pdfContent = $pdf->Output('preview.pdf', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }
}
