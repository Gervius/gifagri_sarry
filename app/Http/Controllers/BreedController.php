<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBreedRequest;
use App\Http\Requests\UpdateBreedRequest;
use App\Http\Resources\BreedResource;
use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:view breeds')->only('index');
        $this->middleware('permission:create breeds')->only('store');
        $this->middleware('permission:update breeds')->only('update');
        $this->middleware('permission:delete breeds')->only('destroy');
    }

    public function index(Request $request)
    {
        $breeds = Breed::with('animalType')
            ->paginate($request->input('per_page', 15));

        return BreedResource::collection($breeds);
    }

    public function store(StoreBreedRequest $request)
    {
        $breed = Breed::create($request->validated());

        return new BreedResource($breed->load('animalType'));
    }

    public function update(UpdateBreedRequest $request, Breed $breed)
    {
        $breed->update($request->validated());

        return new BreedResource($breed->load('animalType'));
    }

    public function destroy(Breed $breed)
    {
        $breed->delete();

        return response()->noContent();
    }
}
