<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeminarRegistration;
use App\Models\Setting;
use App\Models\User;
use App\Models\Winner;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SeminarAdminController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    public function index(Request $request): View
    {
        $search = $request->query('search', '');
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

        $totalCount = SeminarRegistration::count();
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

        $user = User::query()->findOrFail($userId);
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
            'attended' => true,
            'certificate_number' => $cert['certificate_number'],
            'certificate_path' => $cert['certificate_path'],
        ]);

        return redirect()->back()->with('success', "Sertifikat \"{$user->name}\" berhasil dibuat.");
    }

    public function bulkVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $userIds = $request->input('user_ids');
        $count = 0;

        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
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
                'attended' => true,
                'certificate_number' => $cert['certificate_number'],
                'certificate_path' => $cert['certificate_path'],
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "{$count} peserta berhasil diverifikasi kehadirannya.");
    }

    public function uploadCertificate(Request $request, string $userId): JsonResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $request->validate([
            'certificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $registration = SeminarRegistration::query()->where('user_id', $userId)->firstOrFail();

        if ($registration->certificate_path) {
            Storage::disk('public')->delete($registration->certificate_path);
        }

        $path = $request->file('certificate')->store('seminar-certificates', 'public');

        $certificateNumber = $registration->certificate_number
            ?? app(CertificateService::class)->generateCertificateNumber();

        $registration->update([
            'certificate_path' => $path,
            'certificate_number' => $certificateNumber,
        ]);

        return $this->success('Certificate uploaded successfully.', [
            'certificateUrl' => Storage::disk('public')->url($path),
            'certificateNumber' => $certificateNumber,
        ]);
    }

    public function certificates(Request $request): View
    {
        $search = $request->query('search', '');

        $query = Winner::query()
            ->with(['team.competition', 'team.leader', 'team.members']);

        $query->whereHas('team', function ($q) {
            $q->whereHas('competition');
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('team.leader', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('team.members', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $winners = $query->paginate(25)->withQueryString();

        // Flatten: each winner's team → one row per team member
        $rows = collect();
        foreach ($winners as $winner) {
            $team = $winner->team;
            if (! $team) {
                continue;
            }

            $users = collect([$team->leader])->concat($team->members)->filter();

            foreach ($users as $user) {
                $reg = SeminarRegistration::where('user_id', $user->id)->first();
                $rows->push((object) [
                    'winner' => $winner,
                    'team' => $team,
                    'user' => $user,
                    'reg' => $reg,
                ]);
            }
        }

        $totalCount = Winner::count();
        $withCertificateCount = SeminarRegistration::query()
            ->whereNotNull('certificate_path')
            ->whereIn('user_id', function ($q) {
                $q->select('leader_id')->from('teams')
                    ->whereIn('id', function ($q) {
                        $q->select('team_id')->from('winners');
                    });
            })
            ->count();

        $nonWinnerLabel = Setting::get('non_winner_label', 'Partisipasi');

        return view('admin.seminar.certificates', compact(
            'rows',
            'winners',
            'search',
            'totalCount',
            'withCertificateCount',
            'nonWinnerLabel',
        ));
    }

    public function updateCertificateLabel(Request $request): RedirectResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate(['label' => 'required|string|max:255']);

        Setting::set('non_winner_label', $request->label);

        return redirect()->back()->with('success', 'Label berhasil diperbarui.');
    }

    public function uploadCertificateWeb(Request $request): RedirectResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'user_id' => 'required|string|uuid|exists:users,id',
            'certificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $registration = SeminarRegistration::query()->where('user_id', $request->user_id)->firstOrCreate(['user_id' => $request->user_id]);

        if ($registration->certificate_path) {
            Storage::disk('public')->delete($registration->certificate_path);
        }

        $path = $request->file('certificate')->store('seminar-certificates', 'public');

        $certificateNumber = $registration->certificate_number
            ?? app(CertificateService::class)->generateCertificateNumber();

        $registration->update([
            'certificate_path' => $path,
            'certificate_number' => $certificateNumber,
        ]);

        return redirect()->back()->with('success', 'Sertifikat berhasil diupload.');
    }
}
