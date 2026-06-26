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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CompetitionController extends Controller
{
    public function index(): JsonResponse
    {
        $event = Event::query()->where('is_active', true)->first();
        if ($event == null) {
            throw new ModelNotFoundException("event not found");
        }
        $competitions = Competition::query()
            ->where('event_id', $event->id)
            ->with('categories')
            ->get()->map(function (Competition $competition) {
                $categories = $competition->categories->map(function (Category $category) {
                    return [
                        'name' => $category->name
                    ];
                });
                return [
                    'slug' => $competition->slug,
                    'name' => $competition->name,
                    'cover' => $competition->cover,
                    'maxMembers' => $competition->max_members,
                    'categories' => $categories,
                ];
            });

        $responseData = [
            'status' => 1,
            'message' => 'Succeed get all competition',
            'data' => [
                'competitions' => $competitions,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function store(StoreCompetitionRequest $request): JsonResponse
    {
        $this->authorize('create', Competition::class);
        $cover = $request->file('cover')->store('competition/avatar', ['disk' => 'public']);

        $event = Event::query()->where('is_active', true)->first();

        $competitionData = [
            'name' => $request->input('name'),
            'deadline' => $request->input('deadline'),
            'max_members' => $request->input('maxMembers'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'guide_book' => $request->input('guideBookLink'),
            'cover' => Storage::disk('public')->url($cover),
            'event_id' => $event->id,
        ];

        $competition = Competition::query()->create($competitionData);

        $arrayCriteria = json_decode($request->criteria);
        $criteriaData = [];
        foreach ($arrayCriteria as $criteria) {
            $criteriaData[] = [
                'competition_id' => $competition->id,
                'name' => $criteria->name,
                'percentage' => $criteria->percentage,
            ];
        }
        $arrayTechStacks = json_decode($request->techStacks);
        $techStacksData = [];
        foreach ($arrayTechStacks as $techStack) {
            $techStacksData[] = [
                'competition_id' => $competition->id,
                'name' => $techStack,
            ];
        }
        $arrayCategories = json_decode($request->categories);
        $categoriesData = [];
        foreach ($arrayCategories as $category) {
            $categoriesData[] = [
                'competition_id' => $competition->id,
                'category_id' => $category,
            ];
        }
        Criterion::query()->insert($criteriaData);
        TechStack::query()->insert($techStacksData);
        CategoryCompetition::query()->insert($categoriesData);

        $responseData = [
            'status' => 1,
            'message' => 'Succeed create new competition',
            'data' => [
                'competition' => $competition,
            ],
        ];

        return response()->json($responseData, 201);
    }

    public function show(string $slug): JsonResponse
    {
        $result = Competition::with([
            'criteria:id,competition_id,name,percentage',
            'techStacks:id,competition_id,name',
            'categories' => fn($query) => $query->select('name'),
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $deadline = Carbon::parse($result->deadline);
        $now = Carbon::now();
        $days = $deadline->diffInDays($now);

        $techStacks = $result->techStacks->map(fn($item) => $item->name);
        $categories = $result->categories->map(fn($item) => ['name' => $item->name]);
        $criteria = $result->criteria->map(fn($item) => ['name' => $item->name, 'percentage' => $item->percentage]);

        $competition = [
            'name' => $result->name,
            'slug' => $result->slug,
            'cover' => $result->cover,
            'deadline' => $days,
            'deadlineDate' => Carbon::parse($result->deadline)->format('Y-m-d'),
            'maxMembers' => $result->max_members,
            'description' => $result->description,
            'guideBookLink' => $result->guide_book,
            'competitionPrice' => $result->price,
            'techStacks' => $techStacks,
            'categories' => $categories,
            'criteria' => $criteria,
        ];

        $responseData = [
            'status' => 1,
            'message' => 'Succeed get detail competition',
            'data' => [
                'competition' => $competition,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function update(UpdateCompetitionRequest $request, string $slug): JsonResponse
    {
        $this->authorize('update', Competition::query()->where('slug', $slug)->firstOrFail());
        $competition = Competition::query()->where('slug', $slug)->firstOrFail();

        $competitionData = [
            'name' => $request->input('name'),
            'deadline' => $request->input('deadline'),
            'max_members' => $request->input('maxMembers'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'guide_book' => $request->input('guideBookLink'),
        ];

        if ($request->file('cover') != null) {
            $cover = $request->file('cover')->store('competition/avatar', ['disk' => 'public']);
            $competitionData['cover'] = Storage::disk('public')->url($cover);

            Storage::disk('public')->delete($competition->cover);
        }

        $competition->update($competitionData);

        Criterion::query()->where('competition_id', $competition->id)->delete();
        TechStack::query()->where('competition_id', $competition->id)->delete();

        $criteriaData = $this->getCriteriaToDatabase(json_decode($request->criteria), $competition->id);
        $techStacksData = $this->getTechStacksToDatabase(json_decode($request->techStacks), $competition->id);
        $categoriesData = $this->getCategoriesToDatabase($request->categories, $competition->id);

        Criterion::query()->insert($criteriaData);
        TechStack::query()->insert($techStacksData);
        $competition->categories()->sync($categoriesData);

        $competition['criteria'] = $criteriaData;
        $competition['techStacks'] = $techStacksData;
        $responseData = [
            'status' => 1,
            'message' => 'Succeed update competition',
            'data' => [
                'competition' => $competition,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function destroy(string $slug): JsonResponse
    {
        $this->authorize('delete', Competition::query()->where('slug', $slug)->firstOrFail());

        $competition = Competition::query()->where('slug', $slug)->firstOrFail();
        $competition->delete();

        $responseData = [
            'status' => 1,
            'message' => 'Succeed delete competition',
        ];

        return response()->json($responseData, 200);
    }

    private function getCategoriesToDatabase(string $categories, int $competitionId): array
    {
        $arrayCategories = json_decode($categories);
        $categoriesData = [];
        foreach ($arrayCategories as $category) {
            $categoriesData[] = [
                'competition_id' => $competitionId,
                'category_id' => $category,
            ];
        }

        return $categoriesData;
    }

    private function getCriteriaToDatabase(array $criteria, int $competitionId): array
    {
        $criteriaData = [];
        foreach ($criteria as $criterion) {
            $criteriaData[] = [
                'competition_id' => $competitionId,
                'name' => $criterion->name,
                'percentage' => $criterion->percentage,
            ];
        }

        return $criteriaData;
    }

    private function getTechStacksToDatabase(array $techStacks, int $competitionId): array
    {
        $techStacksData = [];
        foreach ($techStacks as $techStack) {
            $techStacksData[] = [
                'competition_id' => $competitionId,
                'name' => $techStack,
            ];
        }

        return $techStacksData;
    }
}
