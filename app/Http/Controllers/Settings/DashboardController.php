<?php

namespace App\Http\Controllers\Settings;

use App\Models\ProphylaxisPlan;
use App\Models\AnalyticalAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('settings/dashboard', [
            'company' => auth()->user()->currentTeam ?? null,
            'prophylaxisPlans' => ProphylaxisPlan::with('steps')->get(),
            'analyticalAccounts' => AnalyticalAccount::with('children')->whereNull('parent_id')->get(),
            'roles' => Role::with('permissions')->get(),
            'allPermissions' => Permission::all()->groupBy('domain'),
        ]);
    }
}