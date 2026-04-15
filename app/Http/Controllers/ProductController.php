<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductController extends Controller
{
    public function index(): InertiaResponse
    {
        $products = Product::query()->get();

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->back()->with('success', 'Produit créé avec succès.');
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->back()->with('success', 'Produit mis à jour.');
    }
}
