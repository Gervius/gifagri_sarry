<?php

use App\Actions\RecordBreedingEventAction;
use App\Actions\ProcessPigWeaningAction;

use App\Http\Requests\StorePigBreedingEventRequest;
use App\Http\Requests\UpdatePigBreedingEventRequest;
use App\Http\Resources\PigBreedingEventResource;
use App\Models\PigBreedingEvent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PigBreedingEventController extends Controller
{
    public function index(): InertiaResponse
    {
        $events = PigBreedingEvent::with(['flock', 'boarFlock'])->get();

        return Inertia::render('PigBreedingEvents/Index', [
            'events' => PigBreedingEventResource::collection($events),
        ]);
    }

    public function store(StorePigBreedingEventRequest $request, RecordBreedingEventAction $recordAction, ProcessPigWeaningAction $weaningAction): RedirectResponse
    {
        $pigBreedingEvent = $recordAction->execute($request->validated(), $request->user()->id);

        if ($pigBreedingEvent->event_type === 'weaning') {
            $newFlock = $weaningAction->execute($pigBreedingEvent, $request->user()->id);
            return redirect()->back()->with('success', 'Événement de sevrage enregistré. Nouveau lot d\'engraissement créé : ' . $newFlock->name);
        }

        return redirect()->back()->with('success', 'Événement de reproduction enregistré.');
    }

    public function show(PigBreedingEvent $pigBreedingEvent): PigBreedingEventResource
    {
        return new PigBreedingEventResource($pigBreedingEvent->load(['flock', 'boarFlock']));
    }

    public function update(UpdatePigBreedingEventRequest $request, PigBreedingEvent $pigBreedingEvent): RedirectResponse
    {
        $pigBreedingEvent->update($request->validated());

        return redirect()->back()->with('success', 'Événement de reproduction mis à jour.');
    }
}
