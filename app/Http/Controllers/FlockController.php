<?php

namespace App\Http\Controllers;

use App\Actions\ApproveFlockAction;
use App\Actions\EndFlockAction;
use App\Actions\StoreFlockAction;
use App\Http\Requests\EndFlockRequest;
use App\Http\Requests\RejectFlockRequest;
use App\Http\Requests\StoreFlockRequest;
use App\Http\Resources\FlockResource;
use App\Models\Flock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlockController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Flock::with(['building', 'animalType', 'creator'])
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('building_id'), fn($q) => $q->where('building_id', $request->building_id))
            ->orderByDesc('created_at');

        $flocks = $query->paginate(20)->withQueryString();

        return Inertia::render('Flocks/Index', [
            'flocks' => FlockResource::collection($flocks),
            'filters' => $request->only(['search', 'status', 'building_id']),
            'buildings' => \App\Http\Resources\BuildingResource::collection(\App\Models\Building::all()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Flocks/Create', [
            'buildings' => \App\Http\Resources\BuildingResource::collection(\App\Models\Building::all()),
            'animalTypes' => \App\Http\Resources\AnimalTypeResource::collection(\App\Models\AnimalType::all()),
            'suppliers' => \App\Http\Resources\PartnerResource::collection(\App\Models\Partner::suppliers()->get()),
        ]);
    }

    public function store(StoreFlockRequest $request): RedirectResponse
    {
        $flock = (new StoreFlockAction())->execute($request->validated(), $request->user());

        return redirect()->route('flocks.show', $flock)
            ->with('success', 'Lot créé avec succès.');
    }

    public function show(Flock $flock): Response
    {
        $flock->load([
            'animalType',
            'building.site',
            'creator',
            'approver',
            'dailyRecords' => fn($q) => $q->with(['creator', 'approver'])->orderByDesc('date'),
            'weightRecords' => fn($q) => $q->orderByDesc('date'),
            'pigBreedingEvents' => fn($q) => $q->with('boarFlock')->orderByDesc('event_date'),
        ]);

        // Données pour l'analyse financière
        $financialAnalysis = (new \App\Services\ProfitabilityService())->analyze($flock);

        return Inertia::render('Flocks/Show', [
            'flock' => new FlockResource($flock),
            'financial_analysis' => $financialAnalysis,
            'recipes' => \App\Http\Resources\RecipeResource::collection(\App\Models\Recipe::all()),
        ]);
    }

    public function edit(Flock $flock): Response
    {
        $this->authorize('update', $flock);

        return Inertia::render('Flocks/Edit', [
            'flock' => new FlockResource($flock),
            'buildings' => \App\Http\Resources\BuildingResource::collection(\App\Models\Building::all()),
        ]);
    }

    public function update(StoreFlockRequest $request, Flock $flock): RedirectResponse
    {
        $this->authorize('update', $flock);

        $flock->update($request->validated());

        return redirect()->route('flocks.show', $flock)
            ->with('success', 'Lot mis à jour.');
    }

    public function destroy(Flock $flock): RedirectResponse
    {
        $this->authorize('delete', $flock);

        $flock->delete();

        return redirect()->route('flocks.index')
            ->with('success', 'Lot supprimé.');
    }

    public function submit(Flock $flock): RedirectResponse
    {
        $this->authorize('submit', $flock);

        $flock->status = 'pending';
        $flock->save();

        return back()->with('success', 'Lot soumis pour approbation.');
    }

    public function approve(Flock $flock, ApproveFlockAction $action): RedirectResponse
    {
        $this->authorize('approve', $flock);

        $action->execute($flock, auth()->user());

        return back()->with('success', 'Lot approuvé avec succès.');
    }

    public function reject(RejectFlockRequest $request, Flock $flock): RedirectResponse
    {
        $this->authorize('reject', $flock);

        $flock->status = 'rejected';
        $flock->rejection_reason = $request->input('reason');
        $flock->save();

        return back()->with('success', 'Lot rejeté.');
    }

    public function end(EndFlockRequest $request, Flock $flock, EndFlockAction $action): RedirectResponse
    {
        $this->authorize('end', $flock);

        $action->execute($flock, $request->validated(), auth()->user());

        return redirect()->route('flocks.show', $flock)
            ->with('success', 'Lot terminé avec succès.');
    }
}