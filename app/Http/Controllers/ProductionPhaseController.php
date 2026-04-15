<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionPhaseRequest;
use App\Http\Requests\UpdateProductionPhaseRequest;
use App\Http\Resources\ProductionPhaseResource;
use App\Models\ProductionPhase;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductionPhaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ProductionPhase::class, 'production_phase');
    }

    public function index(): InertiaResponse
    {
        $phases = ProductionPhase::query()->with('animalType')->get();

        return Inertia::render('ProductionPhases/Index', [
            'phases' => ProductionPhaseResource::collection($phases),
        ]);
    }

    public function store(StoreProductionPhaseRequest $request): RedirectResponse
    {
        ProductionPhase::create($request->validated());

        return redirect()->back()->with('success', 'Phase de production créée.');
    }

    public function update(UpdateProductionPhaseRequest $request, ProductionPhase $productionPhase): RedirectResponse
    {
        $productionPhase->update($request->validated());

        return redirect()->back()->with('success', 'Phase de production mise à jour.');
    }

    public function destroy(ProductionPhase $productionPhase): RedirectResponse
    {
        $productionPhase->delete();

        return redirect()->back()->with('success', 'Phase de production supprimée.');
    }
}
