<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DeliveryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $deliveries = Delivery::with(['partner', 'invoice'])
            ->orderByDesc('date')
            ->get();

        return DeliveryResource::collection($deliveries);
    }

    public function store(Request $request): DeliveryResource
    {
        $validated = $request->validate([
            'delivery_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $delivery = Delivery::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return new DeliveryResource($delivery->load(['partner', 'invoice']));
    }

    public function show(Delivery $delivery): DeliveryResource
    {
        return new DeliveryResource($delivery->load(['partner', 'invoice']));
    }

    public function update(Request $request, Delivery $delivery): DeliveryResource
    {
        $validated = $request->validate([
            'delivery_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $delivery->update($validated);

        return new DeliveryResource($delivery->refresh()->load(['partner', 'invoice']));
    }

    public function destroy(Delivery $delivery): Response
    {
        $delivery->delete();

        return response()->noContent();
    }
}
