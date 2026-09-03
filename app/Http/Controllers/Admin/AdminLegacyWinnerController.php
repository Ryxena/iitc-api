<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegacyWinner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminLegacyWinnerController extends Controller
{
    public function index(): View
    {
        $currentYear = (int) now()->format('Y');
        $legacyWinners = LegacyWinner::query()
            ->orderBy('year', 'desc')
            ->orderBy('rank')
            ->paginate(15);

        return view('admin.legacy-winners.index', compact('legacyWinners', 'currentYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:' . now()->format('Y')],
            'project_name' => ['required', 'string', 'max:255'],
            'project_description' => ['nullable', 'string'],
            'institution' => ['nullable', 'string', 'max:255'],
            'competition_name' => ['required', 'string', 'max:255'],
            'rank' => ['required', 'integer', 'min:1'],
            'award_title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('legacy-winners', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        }

        LegacyWinner::create($validated);

        return redirect()->back()->with('success', 'Data juara lama berhasil ditambahkan.');
    }

    public function update(Request $request, LegacyWinner $legacyWinner): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:' . now()->format('Y')],
            'project_name' => ['required', 'string', 'max:255'],
            'project_description' => ['nullable', 'string'],
            'institution' => ['nullable', 'string', 'max:255'],
            'competition_name' => ['required', 'string', 'max:255'],
            'rank' => ['required', 'integer', 'min:1'],
            'award_title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($legacyWinner->image) {
                $oldPath = parse_url($legacyWinner->image, PHP_URL_PATH);
                $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('legacy-winners', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['image']);
        }

        $legacyWinner->update($validated);

        return redirect()->back()->with('success', "Data juara \"{$legacyWinner->project_name}\" berhasil diperbarui.");
    }

    public function destroy(LegacyWinner $legacyWinner): RedirectResponse
    {
        if ($legacyWinner->image) {
            $oldPath = parse_url($legacyWinner->image, PHP_URL_PATH);
            $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
            Storage::disk('public')->delete($oldPath);
        }

        $legacyWinner->delete();

        return redirect()->back()->with('success', 'Data juara lama berhasil dihapus.');
    }
}