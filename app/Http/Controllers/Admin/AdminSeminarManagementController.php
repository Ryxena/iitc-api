<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSeminarManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $status = $request->query('status', 'ALL'); // ALL | ACTIVE | INACTIVE

        $query = Seminar::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status === 'ACTIVE') {
            $query->where('is_active', true);
        } elseif ($status === 'INACTIVE') {
            $query->where('is_active', false);
        }

        $seminars = $query->paginate(15)->withQueryString();

        $totalCount  = Seminar::count();
        $activeCount = Seminar::where('is_active', true)->count();

        return view('admin.seminars.index', compact(
            'seminars',
            'search',
            'status',
            'totalCount',
            'activeCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'speaker'     => ['nullable', 'string', 'max:255'],
            'date_time'   => ['nullable', 'date'],
            'location'    => ['nullable', 'string', 'max:255'],
            'is_active'   => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Seminar::create($validated);

        return redirect()->back()->with('success', 'Seminar berhasil ditambahkan.');
    }

    public function update(Request $request, Seminar $seminar): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'speaker'     => ['nullable', 'string', 'max:255'],
            'date_time'   => ['nullable', 'date'],
            'location'    => ['nullable', 'string', 'max:255'],
            'is_active'   => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);

        $seminar->update($validated);

        return redirect()->back()->with('success', "Seminar \"{$seminar->title}\" berhasil diperbarui.");
    }

    public function destroy(Seminar $seminar): RedirectResponse
    {
        $title = $seminar->title;
        $seminar->delete();

        return redirect()->back()->with('success', "Seminar \"{$title}\" berhasil dihapus.");
    }

    public function toggleActive(Seminar $seminar): RedirectResponse
    {
        $seminar->update([
            'is_active' => ! $seminar->is_active,
        ]);

        $statusText = $seminar->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Status seminar \"{$seminar->title}\" berhasil {$statusText}.");
    }
}
