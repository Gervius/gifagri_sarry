<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalTypeRequest;
use App\Http\Requests\UpdateAnimalTypeRequest;
use App\Http\Resources\AnimalTypeResource;
use App\Models\AnimalType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AnimalTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AnimalType::class, 'animal_type');
    }

    public function index(): InertiaResponse
    {
        $animalTypes = AnimalType::query()->get();

        return Inertia::render('AnimalTypes/Index', [
            'animalTypes' => AnimalTypeResource::collection($animalTypes),
        ]);
    }

    public function store(StoreAnimalTypeRequest $request): RedirectResponse
    {
        AnimalType::create($request->validated());

        return redirect()->back()->with('success', 'Type d\'animal créé.');
    }

    public function update(UpdateAnimalTypeRequest $request, AnimalType $animalType): RedirectResponse
    {
        $animalType->update($request->validated());

        return redirect()->back()->with('success', 'Type d\'animal mis à jour.');
    }

    public function destroy(AnimalType $animalType): RedirectResponse
    {
        $animalType->delete();

        return redirect()->back()->with('success', 'Type d\'animal supprimé.');
    }
}
