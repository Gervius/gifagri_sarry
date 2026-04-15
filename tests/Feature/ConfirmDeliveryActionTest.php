<?php

use App\Actions\ConfirmDeliveryAction;
use App\Models\Delivery;
use App\Models\Flock;
use App\Models\Invoice;
use App\Models\StockMovement;
use App\Services\StockManagerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('confirm delivery decreases stock and creates movement', function () {
    // Créer une facture de vente approuvée
    $invoice = Invoice::factory()->create([
        'type' => 'sale',
        'status' => 'approved',
    ]);

    // Créer un lot avec du stock
    $flock = Flock::factory()->create([
        'current_quantity' => 100,
    ]);

    // Créer une livraison brouillon
    $delivery = Delivery::factory()->create([
        'invoice_id' => $invoice->id,
        'status' => 'draft',
    ]);

    // Ajouter un item à la livraison
    $deliveryItem = $delivery->items()->create([
        'itemable_type' => Flock::class,
        'itemable_id' => $flock->id,
        'quantity' => 10,
        'unit_price' => 5.00,
        'total' => 50.00,
    ]);

    // Confirmer la livraison
    $action = new ConfirmDeliveryAction(new StockManagerService());
    $action->execute($delivery, 1);

    // Vérifier que la livraison est confirmée
    $delivery->refresh();
    expect($delivery->status)->toBe('confirmed');
    expect($delivery->confirmed_by)->toBe(1);
    expect($delivery->confirmed_at)->not->toBeNull();

    // Vérifier que le stock a été décrémenté
    $flock->refresh();
    expect($flock->current_quantity)->toBe(90);

    // Vérifier qu'un mouvement de stock a été créé
    $movement = StockMovement::first();
    expect($movement)->not->toBeNull();
    expect($movement->stockable_type)->toBe(Flock::class);
    expect($movement->stockable_id)->toBe($flock->id);
    expect($movement->quantity)->toBe(-10);
    expect($movement->type)->toBe('decrease');
    expect($movement->reason)->toBe('delivery_confirmed');
});

test('cannot confirm already confirmed delivery', function () {
    $delivery = Delivery::factory()->create(['status' => 'confirmed']);

    $action = new ConfirmDeliveryAction(new StockManagerService());

    expect(fn() => $action->execute($delivery, 1))
        ->toThrow(\InvalidArgumentException::class, 'Cette livraison a déjà été confirmée.');
});

test('cannot confirm non-draft delivery', function () {
    $delivery = Delivery::factory()->create(['status' => 'cancelled']);

    $action = new ConfirmDeliveryAction(new StockManagerService());

    expect(fn() => $action->execute($delivery, 1))
        ->toThrow(\InvalidArgumentException::class, 'Seules les livraisons en brouillon peuvent être confirmées.');
});
