<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEggCategoryRequest;
use App\Http\Requests\UpdateEggCategoryRequest;
use App\Http\Resources\EggCategoryResource;
use App\Models\EggCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EggCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EggCategory::class, 'egg_category');
    }

    public function index(): InertiaResponse
    {
        $categories = EggCategory::query()->get();

        return Inertia::render('EggCategories/Index', [
            'categories' => EggCategoryResource::collection($categories),
        ]);
    }

    public function store(StoreEggCategoryRequest $request): RedirectResponse
    {
        EggCategory::create($request->validated());

        return redirect()->back()->with('success', 'Catégorie d\'œuf créée.');
    }

    public function update(UpdateEggCategoryRequest $request, EggCategory $eggCategory): RedirectResponse
    {
        $eggCategory->update($request->validated());

        return redirect()->back()->with('success', 'Catégorie d\'œuf mise à jour.');
    }

    public function destroy(EggCategory $eggCategory): RedirectResponse
    {
        $eggCategory->delete();

        return redirect()->back()->with('success', 'Catégorie d\'œuf supprimée.');
    }
}
