<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ApproveInvoiceAction;
use App\Http\Requests\ApproveRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ((float) $item['quantity'] * (float) $item['unit_price']), 0.0);

        DB::transaction(function () use ($request, $validated, $items, $subtotal): void {
            $invoice = Invoice::create($request->safe()->except('items')->merge([
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'created_by' => $request->user()->id,
            ])->toArray());

            foreach ($items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => (float) $item['quantity'] * (float) $item['unit_price'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Facture créée avec succès.');
    }

    public function approve(ApproveRequest $request, Invoice $invoice, ApproveInvoiceAction $approveAction): RedirectResponse
    {
        $approveAction->execute($invoice, $request->user()->id);

        return redirect()->back()->with('success', 'Facture approuvée avec succès.');
    }
}
