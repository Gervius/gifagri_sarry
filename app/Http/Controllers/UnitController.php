<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Unit::class, 'unit');
    }

    public function index(): InertiaResponse
    {
        $units = Unit::query()->with('baseUnit')->get();

        return Inertia::render('Units/Index', [
            'units' => UnitResource::collection($units),
        ]);
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        Unit::create($request->validated());

        return redirect()->back()->with('success', 'Unité créée.');
    }

    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()->back()->with('success', 'Unité mise à jour.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()->back()->with('success', 'Unité supprimée.');
    }
}
