<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeminarRegistration;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeminarAdminController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    public function index(Request $request): View
    {
        $search   = $request->query('search', '');
        $attended = $request->query('attended', 'ALL'); // ALL | YES | NO

        $query = SeminarRegistration::query()
            ->with('user')
            ->latest();

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($attended === 'YES') {
            $query->where('attended', true);
        } elseif ($attended === 'NO') {
            $query->where('attended', false);
        }

        $registrations = $query->paginate(25)->withQueryString();

        $totalCount    = SeminarRegistration::count();
        $attendedCount = SeminarRegistration::where('attended', true)->count();

        return view('admin.seminar.index', compact(
            'registrations',
            'search',
            'attended',
            'totalCount',
            'attendedCount'
        ));
    }

    public function verify(Request $request, string $userId): RedirectResponse
    {
        $request->validate([
            'is_approve' => ['required', 'in:1,0'],
        ]);

        $user         = User::query()->findOrFail($userId);
        $registration = SeminarRegistration::query()->where('user_id', $userId)->firstOrFail();

        $isApprove = (bool) $request->input('is_approve');

        if (! $isApprove) {
            $registration->update(['attended' => false]);

            return redirect()->back()->with('success', "Kehadiran \"{$user->name}\" ditandai tidak hadir.");
        }

        // Already has certificate — just mark attended
        if ($registration->certificate_path !== null) {
            $registration->update(['attended' => true]);

            return redirect()->back()->with('success', "Kehadiran \"{$user->name}\" sudah diverifikasi sebelumnya.");
        }

        // Generate certificate
        $cert = $this->certificateService->generate($user->name, $user->id);

        $registration->update([
            'attended'           => true,
            'certificate_number' => $cert['certificate_number'],
            'certificate_path'   => $cert['certificate_path'],
        ]);

        return redirect()->back()->with('success', "Sertifikat \"{$user->name}\" berhasil dibuat.");
    }

    public function bulkVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $userIds = $request->input('user_ids');
        $count   = 0;

        foreach ($userIds as $userId) {
            $user         = User::query()->find($userId);
            $registration = SeminarRegistration::query()->where('user_id', $userId)->first();

            if ($registration === null || $user === null) {
                continue;
            }

            if ($registration->certificate_path !== null) {
                $registration->update(['attended' => true]);
                $count++;
                continue;
            }

            $cert = $this->certificateService->generate($user->name, $user->id);

            $registration->update([
                'attended'           => true,
                'certificate_number' => $cert['certificate_number'],
                'certificate_path'   => $cert['certificate_path'],
            ]);

            $count++;
        }

        return redirect()->back()->with('success', "{$count} peserta berhasil diverifikasi kehadirannya.");
    }
}
