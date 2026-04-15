<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PartnerController extends Controller
{
    public function index(): InertiaResponse
    {
        $partners = Partner::query()->get();

        return Inertia::render('Partners/Index', [
            'partners' => PartnerResource::collection($partners),
        ]);
    }

    public function store(StorePartnerRequest $request): RedirectResponse
    {
        Partner::create($request->validated());

        return redirect()->back()->with('success', 'Partenaire créé avec succès.');
    }

    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $partner->update($request->validated());

        return redirect()->back()->with('success', 'Partenaire mis à jour.');
    }
}
