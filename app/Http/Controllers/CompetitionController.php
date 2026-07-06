<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Models\Category;
use App\Models\CategoryCompetition;
use App\Models\Competition;
use App\Models\Criterion;
use App\Models\Event;
use App\Models\TechStack;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CompetitionController extends Controller
{
    public function index(): JsonResponse
    {
        $activeEventIds = Event::query()->where('is_active', true)->pluck('id');

        if ($activeEventIds->isEmpty()) {
            return $this->error('No active event found.', 404);
        }

        $competitions = Competition::query()
            ->whereIn('event_id', $activeEventIds)
            ->with('categories')
            ->get()->map(function (Competition $competition) {
                $categories = $competition->categories->map(fn (Category $cat) => ['name' => $cat->name]);

                return [
                    'slug'       => $competition->slug,
                    'name'       => $competition->name,
                    'cover'      => $competition->cover,
                    'maxMembers' => $competition->max_members,
                    'categories' => $categories,
                ];
            });

        return $this->success('Succeed get all competitions.', ['competitions' => $competitions]);
    }

    public function store(StoreCompetitionRequest $request): JsonResponse
    {
        $this->authorize('create', Competition::class);

        $event = Event::query()->where('is_active', true)->first();
        if (! $event) {
            return $this->error('No active event found.', 422);
        }

        $cover = $request->file('cover')->store('competition/avatar', ['disk' => 'public']);

        $competition = Competition::query()->create([
            'name'        => $request->input('name'),
            'deadline'    => $request->input('deadline'),
            'max_members' => $request->input('maxMembers'),
            'price'       => $request->input('price'),
            'description' => $request->input('description'),
            'guide_book'  => $request->input('guideBookLink'),
            'cover'       => Storage::disk('public')->url($cover),
            'event_id'    => $event->id,
        ]);

        $criteriaData    = $this->getCriteriaToDatabase(json_decode($request->criteria), $competition->id);
        $techStacksData  = $this->getTechStacksToDatabase(json_decode($request->techStacks), $competition->id);
        $categoriesData  = $this->getCategoriesToDatabase($request->categories, $competition->id);

        Criterion::query()->insert($criteriaData);
        TechStack::query()->insert($techStacksData);
        CategoryCompetition::query()->insert($categoriesData);

        return $this->success('Succeed create new competition.', ['competition' => $competition], 201);
    }

    public function show(string $slug): JsonResponse
    {
        $result = Competition::with([
            'criteria:id,competition_id,name,percentage',
            'techStacks:id,competition_id,name',
            'categories:id,name',
        ])->where('slug', $slug)->firstOrFail();

        $deadline   = Carbon::parse($result->deadline);
        $techStacks = $result->techStacks->map(fn ($item) => $item->name);
        $categories = $result->categories->map(fn ($item) => ['name' => $item->name]);
        $criteria   = $result->criteria->map(fn ($item) => ['name' => $item->name, 'percentage' => $item->percentage]);

        $competition = [
            'name'             => $result->name,
            'slug'             => $result->slug,
            'cover'            => $result->cover,
            'deadline'         => (int) $deadline->diffInDays(Carbon::now()),
            'deadlineDate'     => $deadline->format('Y-m-d'),
            'maxMembers'       => $result->max_members,
            'description'      => $result->description,
            'guideBookLink'    => $result->guide_book,
            'competitionPrice' => $result->price,
            'techStacks'       => $techStacks,
            'categories'       => $categories,
            'criteria'         => $criteria,
        ];

        return $this->success('Succeed get detail competition.', ['competition' => $competition]);
    }

    public function update(UpdateCompetitionRequest $request, string $slug): JsonResponse
    {
        $competition = Competition::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('update', $competition);

        $competitionData = [
            'name'        => $request->input('name'),
            'deadline'    => $request->input('deadline'),
            'max_members' => $request->input('maxMembers'),
            'price'       => $request->input('price'),
            'description' => $request->input('description'),
            'guide_book'  => $request->input('guideBookLink'),
        ];

        if ($request->file('cover') !== null) {
            $cover = $request->file('cover')->store('competition/avatar', ['disk' => 'public']);
            $competitionData['cover'] = Storage::disk('public')->url($cover);
            Storage::disk('public')->delete($competition->cover);
        }

        $competition->update($competitionData);

        Criterion::query()->where('competition_id', $competition->id)->delete();
        TechStack::query()->where('competition_id', $competition->id)->delete();

        $criteriaData   = $this->getCriteriaToDatabase(json_decode($request->criteria), $competition->id);
        $techStacksData = $this->getTechStacksToDatabase(json_decode($request->techStacks), $competition->id);
        $categoriesData = $this->getCategoriesToDatabase($request->categories, $competition->id);

        Criterion::query()->insert($criteriaData);
        TechStack::query()->insert($techStacksData);
        $competition->categories()->sync($categoriesData);

        $competition['criteria']   = $criteriaData;
        $competition['techStacks'] = $techStacksData;

        return $this->success('Succeed update competition.', ['competition' => $competition]);
    }

    public function destroy(string $slug): JsonResponse
    {
        $competition = Competition::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('delete', $competition);
        $competition->delete();

        return $this->success('Succeed delete competition.');
    }

    private function getCategoriesToDatabase(string $categories, int $competitionId): array
    {
        return array_map(
            fn ($cat) => ['competition_id' => $competitionId, 'category_id' => $cat],
            json_decode($categories)
        );
    }

    private function getCriteriaToDatabase(array $criteria, int $competitionId): array
    {
        return array_map(
            fn ($c) => ['competition_id' => $competitionId, 'name' => $c->name, 'percentage' => $c->percentage],
            $criteria
        );
    }

    private function getTechStacksToDatabase(array $techStacks, int $competitionId): array
    {
        return array_map(
            fn ($ts) => ['competition_id' => $competitionId, 'name' => $ts],
            $techStacks
        );
    }
}
