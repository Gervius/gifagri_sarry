<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TaxController extends Controller
{
    public function index(): InertiaResponse
    {
        $taxes = Tax::query()->with('accountingAccount')->get();

        return Inertia::render('Taxes/Index', [
            'taxes' => TaxResource::collection($taxes),
        ]);
    }

    public function store(StoreTaxRequest $request): RedirectResponse
    {
        Tax::create($request->validated());

        return redirect()->back()->with('success', 'Taxe enregistrée avec succès.');
    }

    public function update(UpdateTaxRequest $request, Tax $tax): RedirectResponse
    {
        $tax->update($request->validated());

        return redirect()->back()->with('success', 'Taxe mise à jour.');
    }

    public function destroy(Tax $tax): RedirectResponse
    {
        $tax->delete();

        return redirect()->back()->with('success', 'Taxe supprimée.');
    }
}
