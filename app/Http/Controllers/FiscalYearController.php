<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\FiscalYearResource;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FiscalYearController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $years = FiscalYear::orderBy('start_date')->get();

        return FiscalYearResource::collection($years);
    }

    public function store(Request $request): FiscalYearResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_closed' => ['sometimes', 'boolean'],
        ]);

        $year = FiscalYear::create($validated);

        return new FiscalYearResource($year);
    }

    public function show(FiscalYear $fiscalYear): FiscalYearResource
    {
        return new FiscalYearResource($fiscalYear);
    }

    public function update(Request $request, FiscalYear $fiscalYear): FiscalYearResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_closed' => ['sometimes', 'boolean'],
        ]);

        $fiscalYear->update($validated);

        return new FiscalYearResource($fiscalYear->refresh());
    }

    public function destroy(FiscalYear $fiscalYear): Response
    {
        abort(Response::HTTP_FORBIDDEN, 'La suppression d’un exercice fiscal est bloquée par la règle de gestion.');
    }
}
