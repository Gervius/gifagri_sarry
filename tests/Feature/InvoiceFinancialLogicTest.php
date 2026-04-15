<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Ingredient;
use App\Models\Partner;
use App\Models\LandedCostAllocation;
use App\Models\JournalVoucher;
use App\Actions\ApproveInvoiceAction;
use App\Services\AccountingEngineService;

test('purchase invoice approval generates accounting entries', function () {
    $accountingEngine = app(AccountingEngineService::class);
    $partner = Partner::factory()->create();
    $ingredient = Ingredient::factory()->create(['pmp' => 10.0]);

    $invoice = Invoice::factory()->create([
        'type' => 'purchase',
        'partner_id' => $partner->id,
        'subtotal' => 1000.0,
        'total' => 1000.0,
        'status' => 'draft',
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'itemable_type' => Ingredient::class,
        'itemable_id' => $ingredient->id,
        'quantity' => 100,
        'unit_price' => 10.0,
    ]);

    $action = app(ApproveInvoiceAction::class);
    $action->execute($invoice, 1);

    expect($invoice->fresh()->status)->toBe('approved');

    $vouchers = JournalVoucher::where('source_id', $invoice->id)
        ->where('source_type', Invoice::class)
        ->get();

    expect($vouchers)->not->toBeEmpty();
    expect($vouchers->first()->journalEntries->count())->toBeGreaterThan(0);
});

test('purchase invoice does not decrement physical stock', function () {
    $ingredient = Ingredient::factory()->create(['current_stock' => 100.0, 'pmp' => 10.0]);
    $partner = Partner::factory()->create();

    $invoice = Invoice::factory()->create([
        'type' => 'purchase',
        'partner_id' => $partner->id,
        'subtotal' => 500.0,
        'total' => 500.0,
        'status' => 'draft',
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'itemable_type' => Ingredient::class,
        'itemable_id' => $ingredient->id,
        'quantity' => 50,
        'unit_price' => 10.0,
    ]);

    $oldStock = $ingredient->current_stock;

    $action = app(ApproveInvoiceAction::class);
    $action->execute($invoice, 1);

    expect($ingredient->fresh()->current_stock)->toBe($oldStock);
});

test('sale invoice approval decrements flock quantity for broilers', function () {
    $animalType = \App\Models\AnimalType::factory()->create(['code' => 'broiler']);
    $flock = \App\Models\Flock::factory()->create([
        'animal_type_id' => $animalType->id,
        'current_quantity' => 500,
    ]);

    $invoice = Invoice::factory()->create([
        'type' => 'sale',
        'subtotal' => 5000.0,
        'total' => 5000.0,
        'status' => 'draft',
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'itemable_type' => \App\Models\Flock::class,
        'itemable_id' => $flock->id,
        'quantity' => 100,
        'unit_price' => 50.0,
    ]);

    $action = app(ApproveInvoiceAction::class);
    $action->execute($invoice, 1);

    expect($flock->fresh()->current_quantity)->toBe(400);
});

test('landed costs are allocated to ingredients pmp', function () {
    $ingredient = Ingredient::factory()->create(['current_stock' => 0, 'pmp' => 0]);
    $partner = Partner::factory()->create();

    $invoice = Invoice::factory()->create([
        'type' => 'purchase',
        'partner_id' => $partner->id,
        'subtotal' => 1000.0,
        'total' => 1050.0,
        'status' => 'draft',
    ]);

    $item = InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'itemable_type' => Ingredient::class,
        'itemable_id' => $ingredient->id,
        'quantity' => 100,
        'unit_price' => 10.0,
    ]);

    LandedCostAllocation::factory()->create([
        'invoice_item_id' => $item->id,
        'target_item_type' => Ingredient::class,
        'target_item_id' => $ingredient->id,
        'allocated_amount' => 50.0,
    ]);

    $oldPmp = $ingredient->pmp;

    $action = app(ApproveInvoiceAction::class);
    $action->execute($invoice, 1);

    $ingredient->refresh();
    expect($ingredient->pmp)->toBeGreaterThan($oldPmp);
    expect($ingredient->pmp)->toBe(10.5); // (1000 + 50) / 100
});
