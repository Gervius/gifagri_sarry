<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BuildingController extends Controller
{
    public function index(): InertiaResponse
    {
        $buildings = Building::query()->get();

        return Inertia::render('Buildings/Index', [
            'buildings' => BuildingResource::collection($buildings),
        ]);
    }

    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        Building::create($request->validated());

        return redirect()->back()->with('success', 'Bâtiment créé avec succès.');
    }

    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $building->update($request->validated());

        return redirect()->back()->with('success', 'Bâtiment mis à jour.');
    }
}
