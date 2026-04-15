<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class IngredientController extends Controller
{
    public function index(): InertiaResponse
    {
        $ingredients = Ingredient::query()->get();

        return Inertia::render('Ingredients/Index', [
            'ingredients' => IngredientResource::collection($ingredients),
        ]);
    }

    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        Ingredient::create($request->validated());

        return redirect()->back()->with('success', 'Ingrédient créé avec succès.');
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $ingredient->update($request->validated());

        return redirect()->back()->with('success', 'Ingrédient mis à jour.');
    }
}
