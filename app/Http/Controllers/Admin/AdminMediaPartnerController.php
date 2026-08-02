<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaPartner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminMediaPartnerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');

        $query = MediaPartner::query()->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $mediaPartners = $query->paginate(15)->withQueryString();
        $totalCount    = MediaPartner::count();

        return view('admin.media-partners.index', compact('mediaPartners', 'search', 'totalCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('partners', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        }

        MediaPartner::create($validated);

        return redirect()->back()->with('success', 'Media partner berhasil ditambahkan.');
    }

    public function update(Request $request, MediaPartner $mediaPartner): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($mediaPartner->image) {
                $oldPath = parse_url($mediaPartner->image, PHP_URL_PATH);
                $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('partners', ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['image']);
        }

        $mediaPartner->update($validated);

        return redirect()->back()->with('success', "Media partner \"{$mediaPartner->name}\" berhasil diperbarui.");
    }

    public function destroy(MediaPartner $mediaPartner): RedirectResponse
    {
        // Delete image file if exists
        if ($mediaPartner->image) {
            $oldPath = parse_url($mediaPartner->image, PHP_URL_PATH);
            $oldPath = ltrim(str_replace('/storage', '', $oldPath), '/');
            Storage::disk('public')->delete($oldPath);
        }

        $name = $mediaPartner->name;
        $mediaPartner->delete();

        return redirect()->back()->with('success', "Media partner \"{$name}\" berhasil dihapus.");
    }
}
