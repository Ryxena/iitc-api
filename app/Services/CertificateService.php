<?php

namespace App\Services;

use App\Models\SeminarRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Path to the certificate background image.
     *
     * HOW TO USE YOUR OWN TEMPLATE:
     *   Replace `public/certificate-template.png` with your own image.
     *   Supported formats: PNG, JPG.
     *   Recommended size: 3508 × 2480 px (A4 landscape @ 300 DPI).
     *
     * The image is used as the full-page background.
     * All text (name, title, date, cert number) is overlaid on top via CSS absolute positioning.
     * Adjust the top/left values in seminar_certificate.blade.php to match your template layout.
     */
    private function getTemplatePath(): string
    {
        // Check for custom template first (PNG or JPG)
        foreach (['certificate-template.png', 'certificate-template.jpg'] as $filename) {
            $path = public_path($filename);
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback: no background (plain white)
        return '';
    }

    /**
     * Generate next sequential certificate number, e.g. IITC-2026-0001
     */
    public function generateCertificateNumber(): string
    {
        $last = SeminarRegistration::query()
            ->whereNotNull('certificate_number')
            ->orderByDesc('certificate_number')
            ->value('certificate_number');

        if ($last === null) {
            $next = 1;
        } else {
            // Format: IITC-2026-XXXX — extract the trailing number
            $parts = explode('-', $last);
            $next = ((int) end($parts)) + 1;
        }

        return sprintf('IITC-%d-%04d', now()->year, $next);
    }

    /**
     * Generate the PDF certificate for a user, store it, and return the public URL.
     *
     * @return array{certificate_number: string, certificate_path: string, url: string}
     */
    public function generate(string $participantName, string $userId): array
    {
        $certificateNumber = $this->generateCertificateNumber();

        $pdf = Pdf::loadView('certificates.seminar_certificate', [
            'participantName'   => $participantName,
            'certificateNumber' => $certificateNumber,
            'templatePath'      => $this->getTemplatePath(),
        ])->setPaper('A4', 'landscape');

        $relativePath = "certificates/{$userId}.pdf";

        Storage::disk('public')->put($relativePath, $pdf->output());

        $url = Storage::disk('public')->url($relativePath);

        return [
            'certificate_number' => $certificateNumber,
            'certificate_path'   => $relativePath,
            'url'                => $url,
        ];
    }
}
