<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\FlockCreated;
use App\Models\Flock;
use App\Models\ProphylaxisPlan;
use App\Models\ScheduledTreatment;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateProphylaxisSchedule implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FlockCreated $event): void
    {
        try {
            $flock = $event->flock;

            // Récupérer le ProphylaxisPlan lié à l'animal_type_id du flock
            $prophylaxisPlan = ProphylaxisPlan::query()
                ->where('animal_type_id', $flock->animal_type_id)
                ->first();

            // Si aucun plan n'existe, on quitte silencieusement
            if (! $prophylaxisPlan) {
                Log::info('No prophylaxis plan found for animal type', [
                    'animal_type_id' => $flock->animal_type_id,
                    'flock_id' => $flock->id,
                ]);

                return;
            }

            // Encapsuler la logique dans une transaction pour garantir l'intégrité
            DB::transaction(function () use ($flock, $prophylaxisPlan): void {
                // Récupérer les étapes du plan
                $prophylaxisSteps = $prophylaxisPlan->steps()->get();

                // Boucler sur chaque étape et créer les enregistrements scheduled_treatments
                foreach ($prophylaxisSteps as $step) {
                    $scheduledDate = $this->calculateScheduledDate(
                        $flock->arrival_date,
                        $step->day_of_age
                    );

                    ScheduledTreatment::create([
                        'flock_id' => $flock->id,
                        'prophylaxis_step_id' => $step->id,
                        'scheduled_date' => $scheduledDate,
                        'status' => 'pending',
                    ]);
                }

                Log::info('Prophylaxis schedule generated successfully', [
                    'flock_id' => $flock->id,
                    'plan_id' => $prophylaxisPlan->id,
                    'steps_count' => $prophylaxisSteps->count(),
                ]);
            });
        } catch (\Exception $exception) {
            Log::error('Error generating prophylaxis schedule', [
                'flock_id' => $event->flock->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }

    /**
     * Calculer la date programmée en ajoutant le day_of_age à la date d'arrivée.
     */
    private function calculateScheduledDate(Carbon|string $arrivalDate, int $dayOfAge): Carbon
    {
        $date = $arrivalDate instanceof Carbon
            ? $arrivalDate->copy()
            : Carbon::parse($arrivalDate);

        return $date->addDays($dayOfAge);
    }
}
