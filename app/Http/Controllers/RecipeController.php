<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RecipeController extends Controller
{
    public function index(): InertiaResponse
    {
        $recipes = Recipe::query()->with(['animalType', 'recipeIngredients.ingredient', 'recipeIngredients.unit'])->get();

        return Inertia::render('Recipes/Index', [
            'recipes' => RecipeResource::collection($recipes),
        ]);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $ingredients = $validated['ingredients'];
        $recipeData = Arr::except($validated, ['ingredients']);

        DB::transaction(function () use ($recipeData, $ingredients): void {
            $recipe = Recipe::create($recipeData);

            foreach ($ingredients as $ingredient) {
                $recipe->recipeIngredients()->create($ingredient);
            }
        });

        return redirect()->back()->with('success', 'Recette créée avec succès.');
    }
}
