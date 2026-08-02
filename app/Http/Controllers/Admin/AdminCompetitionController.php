<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryCompetition;
use App\Models\Competition;
use App\Models\Criterion;
use App\Models\Event;
use App\Models\TechStack;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCompetitionController extends Controller
{
    public function index(): View
    {
        $activeEvent = Event::query()->where('is_active', true)->first();

        $competitions = $activeEvent
            ? Competition::query()
                ->where('event_id', $activeEvent->id)
                ->with(['categories', 'criteria', 'techStacks'])
                ->withCount('teams')
                ->orderBy('name')
                ->get()
            : collect();

        $allCategories = Category::query()->orderBy('name')->get();
        $allEvents     = Event::query()->orderByDesc('created_at')->get();

        return view('admin.competitions.index', compact(
            'competitions',
            'activeEvent',
            'allCategories',
            'allEvents'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'deadline'       => ['required', 'date'],
            'max_members'    => ['required', 'integer', 'min:1'],
            'price'          => ['required', 'numeric', 'min:0'],
            'description'    => ['nullable', 'string'],
            'guide_book'     => ['nullable', 'string', 'max:500'],
            'event_id'       => ['required', 'exists:events,id'],
            'categories'     => ['nullable', 'array'],
            'categories.*'   => ['exists:categories,id'],
        ]);

        $competition = Competition::query()->create([
            'name'        => $data['name'],
            'deadline'    => $data['deadline'],
            'max_members' => $data['max_members'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? null,
            'guide_book'  => $data['guide_book'] ?? null,
            'event_id'    => $data['event_id'],
            'cover'       => null,
        ]);

        if (! empty($data['categories'])) {
            $pivotData = array_map(
                fn ($catId) => ['competition_id' => $competition->id, 'category_id' => $catId],
                $data['categories']
            );
            CategoryCompetition::query()->insert($pivotData);
        }

        return redirect()->back()->with('success', "Kompetisi \"{$competition->name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $competition = Competition::query()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'deadline'     => ['required', 'date'],
            'max_members'  => ['required', 'integer', 'min:1'],
            'price'        => ['required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string'],
            'guide_book'   => ['nullable', 'string', 'max:500'],
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        $competition->update([
            'name'        => $data['name'],
            'deadline'    => $data['deadline'],
            'max_members' => $data['max_members'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? null,
            'guide_book'  => $data['guide_book'] ?? null,
        ]);

        // Sync categories
        $categoryIds = $data['categories'] ?? [];
        $competition->categories()->sync($categoryIds);

        return redirect()->back()->with('success', "Kompetisi \"{$competition->name}\" berhasil diperbarui.");
    }

    public function destroy(string $slug): RedirectResponse
    {
        $competition = Competition::query()->where('slug', $slug)->firstOrFail();
        $name        = $competition->name;
        $competition->delete();

        return redirect()->back()->with('success', "Kompetisi \"{$name}\" berhasil dihapus.");
    }
}
