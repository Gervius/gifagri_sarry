<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApproveFeedProductionRequest;
use App\Http\Requests\StoreFeedProductionRequest;
use App\Http\Resources\FeedProductionResource;
use App\Models\FeedProduction;
use App\Services\FeedProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class FeedProductionController extends Controller
{
    public function index(): InertiaResponse
    {
        $productions = FeedProduction::query()->with(['recipe'])->get();

        return Inertia::render('FeedProductions/Index', [
            'productions' => FeedProductionResource::collection($productions),
        ]);
    }

    public function store(StoreFeedProductionRequest $request): RedirectResponse
    {
        FeedProduction::create(array_merge($request->validated(), ['status' => 'draft']));

        return redirect()->back()->with('success', 'Production d\'aliment enregistrée en brouillon.');
    }

    public function approve(ApproveFeedProductionRequest $request, FeedProduction $feedProduction, FeedProductionService $feedProductionService): RedirectResponse
    {
        DB::transaction(function () use ($feedProduction, $feedProductionService): void {
            $feedProductionService->approveProduction($feedProduction);

            $feedProduction->forceFill([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id() ?? $feedProduction->created_by,
            ])->save();
        });

        return redirect()->back()->with('success', 'Production d\'aliment approuvée.');
    }
}
