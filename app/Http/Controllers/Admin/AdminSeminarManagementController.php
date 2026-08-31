<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $totalCount = Seminar::count();
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'speaker' => ['nullable', 'string', 'max:255'],
            'date_time' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'registration_link' => ['nullable', 'url', 'max:255'],
            'poster' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('seminars', 'public');
        }

        Seminar::create($validated);

        return redirect()->back()->with('success', 'Seminar berhasil ditambahkan.');
    }

    public function update(Request $request, Seminar $seminar): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'speaker' => ['nullable', 'string', 'max:255'],
            'date_time' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'registration_link' => ['nullable', 'url', 'max:255'],
            'poster' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);

        if ($request->boolean('delete_poster')) {
            if ($seminar->poster) {
                Storage::disk('public')->delete($seminar->poster);
            }
            $validated['poster'] = null;
        } elseif ($request->hasFile('poster')) {
            if ($seminar->poster) {
                Storage::disk('public')->delete($seminar->poster);
            }
            $validated['poster'] = $request->file('poster')->store('seminars', 'public');
        }

        $seminar->update($validated);

        return redirect()->back()->with('success', "Seminar \"{$seminar->title}\" berhasil diperbarui.");
    }

    public function destroy(Seminar $seminar): RedirectResponse
    {
        $title = $seminar->title;

        if ($seminar->poster) {
            Storage::disk('public')->delete($seminar->poster);
        }

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
