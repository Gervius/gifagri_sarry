<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $payments = Payment::with(['bankAccount', 'invoice'])
            ->orderByDesc('payment_date')
            ->get();

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): PaymentResource
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, &$payment): void {
            $payment = Payment::create(array_merge($validated, [
                'created_by' => auth()->id(),
            ]));

            $invoice = Invoice::find($validated['invoice_id']);

            if ($invoice !== null) {
                $paid = $invoice->payments()->sum('amount');
                $invoice->update([
                    'payment_status' => $this->resolvePaymentStatus((float) $paid, (float) $invoice->total),
                ]);
            }
        });

        return new PaymentResource($payment->load(['bankAccount', 'invoice']));
    }

    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment->load(['bankAccount', 'invoice']));
    }

    public function update(StorePaymentRequest $request, Payment $payment): PaymentResource
    {
        $validated = $request->validated();
        $oldInvoice = $payment->invoice;

        DB::transaction(function () use ($validated, $payment, $oldInvoice): void {
            $payment->update($validated);

            if ($oldInvoice !== null) {
                $paid = $oldInvoice->payments()->sum('amount');
                $oldInvoice->update([
                    'payment_status' => $this->resolvePaymentStatus((float) $paid, (float) $oldInvoice->total),
                ]);
            }

            if ($payment->invoice !== null) {
                $paid = $payment->invoice->payments()->sum('amount');
                $payment->invoice->update([
                    'payment_status' => $this->resolvePaymentStatus((float) $paid, (float) $payment->invoice->total),
                ]);
            }
        });

        return new PaymentResource($payment->refresh()->load(['bankAccount', 'invoice']));
    }

    public function destroy(Payment $payment): Response
    {
        $invoice = $payment->invoice;
        $payment->delete();

        if ($invoice !== null) {
            $paid = $invoice->payments()->sum('amount');
            $invoice->update([
                'payment_status' => $this->resolvePaymentStatus((float) $paid, (float) $invoice->total),
            ]);
        }

        return response()->noContent();
    }

    private function resolvePaymentStatus(float $paid, float $total): string
    {
        if ($paid >= $total) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partial';
        }

        return 'unpaid';
    }
}
