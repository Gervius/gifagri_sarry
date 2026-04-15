<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentRequest;
use App\Http\Requests\UpdateTreatmentRequest;
use App\Http\Resources\TreatmentResource;
use App\Models\Treatment;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TreatmentController extends Controller
{
    public function index(): InertiaResponse
    {
        $treatments = Treatment::with(['batch', 'flock'])->get();

        return Inertia::render('Treatments/Index', [
            'treatments' => TreatmentResource::collection($treatments),
        ]);
    }

    public function store(StoreTreatmentRequest $request): RedirectResponse
    {
        Treatment::create(array_merge($request->validated(), [
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]));

        return redirect()->back()->with('success', 'Traitement enregistré en brouillon.');
    }

    public function show(Treatment $treatment): TreatmentResource
    {
        return new TreatmentResource($treatment->load(['batch', 'flock']));
    }

    public function update(UpdateTreatmentRequest $request, Treatment $treatment): RedirectResponse
    {
        $treatment->update($request->validated());

        return redirect()->back()->with('success', 'Traitement mis à jour.');
    }
}
