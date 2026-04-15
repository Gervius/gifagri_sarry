<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DailyRecordApproved;
use App\Events\InvoiceApproved;
use App\Events\PurchaseInvoiceApproved;
use App\Events\SaleInvoiceApproved;
use App\Listeners\AdjustPMPFromLandedCosts;
use App\Listeners\DeductFeedFromStock;
use App\Listeners\GenerateDraftDeliveryFromInvoice;
use App\Listeners\GenerateDraftReceiptFromInvoice;
use App\Listeners\ProcessEggProduction;
use App\Listeners\ProcessPartialSales;
use App\Listeners\UpdateFlockMortality;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Les mappages d'événement à listener pour l'application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DailyRecordApproved::class => [
            DeductFeedFromStock::class,
            ProcessEggProduction::class,
            UpdateFlockMortality::class,
        ],
        InvoiceApproved::class => [
            ProcessPartialSales::class,
            AdjustPMPFromLandedCosts::class,
        ],
        PurchaseInvoiceApproved::class => [
            GenerateDraftReceiptFromInvoice::class,
        ],
        SaleInvoiceApproved::class => [
            GenerateDraftDeliveryFromInvoice::class,
        ],
    ];

    /**
     * Disable auto-discovery of events and listeners.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
