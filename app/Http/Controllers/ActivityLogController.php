<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Super-Admin');
    }

    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        $activities = $query->paginate(50);

        return Inertia::render('ActivityLogs/Index', [
            'activities' => ActivityLogResource::collection($activities),
            'filters' => $request->only(['subject_type', 'causer_id']),
        ]);
    }
}
