<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $receipts = Receipt::with(['partner', 'invoice'])
            ->orderByDesc('date')
            ->get();

        return ReceiptResource::collection($receipts);
    }

    public function store(Request $request): ReceiptResource
    {
        $validated = $request->validate([
            'receipt_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $receipt = Receipt::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return new ReceiptResource($receipt->load(['partner', 'invoice']));
    }

    public function show(Receipt $receipt): ReceiptResource
    {
        return new ReceiptResource($receipt->load(['partner', 'invoice']));
    }

    public function update(Request $request, Receipt $receipt): ReceiptResource
    {
        $validated = $request->validate([
            'receipt_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $receipt->update($validated);

        return new ReceiptResource($receipt->refresh()->load(['partner', 'invoice']));
    }

    public function destroy(Receipt $receipt): Response
    {
        $receipt->delete();

        return response()->noContent();
    }
}
