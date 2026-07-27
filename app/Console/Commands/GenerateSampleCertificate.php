<?php

namespace App\Console\Commands;

use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateSampleCertificate extends Command
{
    protected $signature = 'cert:sample {name=Budi Santoso}';
    protected $description = 'Generate a sample certificate for preview';

    public function handle(CertificateService $certificateService): void
    {
        $name = $this->argument('name');
        $result = $certificateService->generate($name, 'sample-preview-001');

        $this->info('Certificate generated!');
        $this->table(['Key', 'Value'], [
            ['Number', $result['certificate_number']],
            ['Path',   $result['certificate_path']],
            ['URL',    $result['url']],
        ]);
    }
}
