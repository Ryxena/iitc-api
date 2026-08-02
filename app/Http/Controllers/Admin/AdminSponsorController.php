<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminSponsorController extends Controller
{
    private const TIERS = ['platinum', 'gold', 'silver', 'bronze'];

    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $tier   = $request->query('tier', 'ALL');

        $query = Sponsor::query()->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($tier !== 'ALL' && in_array(strtolower($tier), self::TIERS)) {
            $query->where('tier', strtolower($tier));
        }

        $sponsors    = $query->paginate(15)->withQueryString();
        $totalCount  = Sponsor::count();

        $tierCounts = [];
        foreach (self::TIERS as $t) {
            $tierCounts[$t] = Sponsor::where('tier', $t)->count();
        }

        return view('admin.sponsors.index', compact('sponsors', 'search', 'tier', 'totalCount', 'tierCounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'tier'  => ['required', 'in:platinum,gold,silver,bronze'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sponsors', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        }

        Sponsor::create($validated);

        return redirect()->back()->with('success', 'Sponsor berhasil ditambahkan.');
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'tier'  => ['required', 'in:platinum,gold,silver,bronze'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($sponsor->image) {
                $oldPath = parse_url($sponsor->image, PHP_URL_PATH);
                $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('sponsors', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['image']);
        }

        $sponsor->update($validated);

        return redirect()->back()->with('success', "Sponsor \"{$sponsor->name}\" berhasil diperbarui.");
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        if ($sponsor->image) {
            $oldPath = parse_url($sponsor->image, PHP_URL_PATH);
            $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
            Storage::disk('public')->delete($oldPath);
        }

        $name = $sponsor->name;
        $sponsor->delete();

        return redirect()->back()->with('success', "Sponsor \"{$name}\" berhasil dihapus.");
    }
}
