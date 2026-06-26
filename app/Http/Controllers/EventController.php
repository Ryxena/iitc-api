<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::query()->get();

        $responseData = [
            'status' => 1,
            'message' => 'Succeed get all competition',
            'data' => [
                'events' => $events,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $eventData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $event = Event::query()->create($eventData);

        $responseData = [
            'status' => 1,
            'message' => 'Succeed create new event',
            'data' => [
                'event' => $event,
            ],
        ];

        return response()->json($responseData, 201);
    }

    public function update(UpdateEventRequest $request, string $eventId)
    {
        $this->authorize('update', Event::query()->where('id', $eventId)->firstOrFail());
        $event = Event::query()->where('id', $eventId)->firstOrFail();

        $eventData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $event->update($eventData);

        $responseData = [
            'status' => 1,
            'message' => 'Succeed update event',
            'data' => [
                'event' => $event,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function destroy(string $eventId)
    {
        $this->authorize('delete', Event::query()->where('id', $eventId)->firstOrFail());

        $event = Event::query()->where('id', $eventId)->firstOrFail();
        $event->delete();

        $responseData = [
            'status' => 1,
            'message' => 'Succeed delete even$event',
        ];

        return response()->json($responseData, 200);
    }

    public function changeIsActive(string $eventId): JsonResponse {
        $this->authorize('update', Event::query()->where('id', $eventId)->firstOrFail());

        Event::query()->where('id', '!=', $eventId)->update(['is_active' => 0]);
        Event::query()->where('id', '=', $eventId)->update(['is_active' => 1]);

        $event = Event::query()->where('id', $eventId)->firstOrFail();

        $responseData = [
            'status' => 1,
            'message' => 'Succeed make event active',
            'data' => [
                'event' => $event,
            ],
        ];

        return response()->json($responseData, 200);
    }
}
