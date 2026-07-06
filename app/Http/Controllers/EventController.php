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

        return $this->success('Succeed get all events.', [
            'events' => $events,
        ]);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $this->authorize('create', Event::class);

        // Business logic validation: duplicate name check
        $exists = Event::query()->where('name', $request->name)->exists();
        if ($exists) {
            return $this->error('An event with this name already exists.', 400);
        }

        $event = Event::query()->create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return $this->success('Succeed create new event.', [
            'event' => $event,
        ], 201);
    }

    public function update(UpdateEventRequest $request, string $eventId): JsonResponse
    {
        $event = Event::query()->findOrFail($eventId);
        $this->authorize('update', $event);

        // Check if updating to a duplicate name
        $exists = Event::query()->where('name', $request->name)->where('id', '!=', $eventId)->exists();
        if ($exists) {
            return $this->error('An event with this name already exists.', 400);
        }

        $event->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return $this->success('Succeed update event.', [
            'event' => $event,
        ]);
    }

    public function destroy(string $eventId): JsonResponse
    {
        $event = Event::query()->findOrFail($eventId);
        $this->authorize('delete', $event);
        
        $event->delete();

        return $this->success('Succeed delete event.');
    }

    public function changeIsActive(string $eventId): JsonResponse
    {
        $event = Event::query()->findOrFail($eventId);
        $this->authorize('update', $event);

        Event::query()->where('id', '!=', $eventId)->update(['is_active' => 0]);
        $event->update(['is_active' => 1]);

        return $this->success('Succeed make event active.', [
            'event' => $event,
        ]);
    }
}
