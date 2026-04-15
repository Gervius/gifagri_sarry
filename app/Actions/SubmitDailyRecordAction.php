<?php

namespace App\Actions;

use App\Models\DailyRecord;
use App\Models\Flock;
use Illuminate\Support\Facades\DB;

class SubmitDailyRecordAction
{
    public function execute(Flock $flock, array $data, int $creatorId): DailyRecord
    {
        DB::transaction(function () use ($flock, $data, $creatorId, &$dailyRecord) {
            if (!$flock->canSubmitDailyRecord()) {
                throw new \InvalidArgumentException('Cannot submit daily record for this flock.');
            }

            // Validation spécifique à l'espèce
            $behavior = $flock->getSpeciesBehavior();
            if (isset($data['eggs']) && $data['eggs'] > 0 && !$behavior->canCollectEggs($flock)) {
                throw new \InvalidArgumentException('Egg collection not allowed in current phase.');
            }

            // Pour Broilers et Porcs, forcer eggs à 0
            if (in_array($flock->animalType->code, ['broiler', 'pig'])) {
                $data['eggs'] = 0;
            }

            $dailyRecord = DailyRecord::create(array_merge($data, [
                'flock_id' => $flock->id,
                'created_by' => $creatorId,
                'status' => 'draft',
            ]));
        });

        return $dailyRecord;
    }
}