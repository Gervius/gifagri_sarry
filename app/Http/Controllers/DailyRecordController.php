<?php

namespace App\Http\Controllers;

use App\Actions\SubmitDailyRecordAction;

use App\Http\Requests\ApproveDailyRecordRequest;
use App\Http\Requests\StoreDailyRecordRequest;
use App\Models\DailyRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DailyRecordController extends Controller
{
    public function store(StoreDailyRecordRequest $request): RedirectResponse
    {
        $flock = \App\Models\Flock::findOrFail($request->input('flock_id'));

        (new SubmitDailyRecordAction())->execute($flock, $request->safe()->toArray(), $request->user()->id);

        return redirect()->back()->with('success', 'Fiche journalière enregistrée.');
    }

    public function approve(ApproveDailyRecordRequest $request, DailyRecord $dailyRecord): RedirectResponse
    {
        DB::transaction(function () use ($dailyRecord): void {
            $dailyRecord->approve();
        });

        return redirect()->back()->with('success', 'Fiche journalière approuvée.');
    }
}
