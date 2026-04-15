<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BatchController extends Controller
{
    public function index(): InertiaResponse
    {
        $batches = Batch::with('batchable')->get();

        return Inertia::render('Batches/Index', [
            'batches' => BatchResource::collection($batches),
        ]);
    }

    public function store(StoreBatchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! isset($data['current_quantity'])) {
            $data['current_quantity'] = $data['initial_quantity'];
        }

        Batch::create($data);

        return redirect()->back()->with('success', 'Lot enregistré avec succès.');
    }

    public function show(Batch $batch): BatchResource
    {
        return new BatchResource($batch->load('batchable'));
    }

    public function update(UpdateBatchRequest $request, Batch $batch): RedirectResponse
    {
        $batch->update($request->validated());

        return redirect()->back()->with('success', 'Lot mis à jour.');
    }
}
