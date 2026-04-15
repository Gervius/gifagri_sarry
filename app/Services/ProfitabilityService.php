<?php

namespace App\Services;

use App\Models\Flock;

class ProfitabilityService
{
    public function calculateFCR(Flock $flock): ?float
    {
        // Total Aliment Consommé (kg)
        $totalFeedConsumed = $flock->dailyRecords()->sum('feed_consumed');

        if ($totalFeedConsumed == 0) {
            return null;
        }

        // Biomasse Vendue (kg) : Somme des quantités vendues via InvoiceItems liées au flock
        $biomassSold = $flock->invoiceItems()
            ->whereHas('invoice', fn($q) => $q->where('type', 'sale')->where('status', 'approved'))
            ->sum('quantity');

        // Biomasse Actuelle (kg) : Dernier WeightRecord ou estimation
        $currentBiomass = $flock->weightRecords()->latest()->first()?->average_weight * $flock->current_quantity ?? 0;

        $totalBiomass = $biomassSold + $currentBiomass;

        if ($totalBiomass == 0) {
            return null;
        }

        return $totalFeedConsumed / $totalBiomass;
    }

    public function calculatePigletsWeanedPerSowPerYear(Flock $sowFlock): ?float
    {
        // Vérifier que c'est une truie
        if ($sowFlock->animalType->code !== 'pig') {
            return null;
        }

        // Récupérer tous les événements de sevrage pour cette truie
        $weaningEvents = $sowFlock->pigBreedingEvents()
            ->where('event_type', 'weaning')
            ->whereNotNull('piglets_weaned')
            ->get();

        if ($weaningEvents->isEmpty()) {
            return null;
        }

        $totalPigletsWeaned = $weaningEvents->sum('piglets_weaned');
        $numberOfWeaningEvents = $weaningEvents->count();

        // Estimer le nombre de sevrage par an (365 jours / intervalle moyen entre sevrages)
        if ($numberOfWeaningEvents < 2) {
            return null;
        }

        $firstEventDate = $weaningEvents->first()->event_date;
        $lastEventDate = $weaningEvents->last()->event_date;
        $daysSpanned = $lastEventDate->diffInDays($firstEventDate);

        if ($daysSpanned <= 0) {
            return null;
        }

        $daysPerWeaningEvent = $daysSpanned / ($numberOfWeaningEvents - 1);
        $weaningEventsPerYear = 365 / $daysPerWeaningEvent;

        return round($totalPigletsWeaned / $numberOfWeaningEvents * $weaningEventsPerYear, 2);
    }

    public function calculateAverageWeanedPigletsPerEvent(Flock $sowFlock): ?float
    {
        if ($sowFlock->animalType->code !== 'pig') {
            return null;
        }

        $weaningEvents = $sowFlock->pigBreedingEvents()
            ->where('event_type', 'weaning')
            ->whereNotNull('piglets_weaned')
            ->get();

        if ($weaningEvents->isEmpty()) {
            return null;
        }

        return round($weaningEvents->avg('piglets_weaned'), 2);
    }
}