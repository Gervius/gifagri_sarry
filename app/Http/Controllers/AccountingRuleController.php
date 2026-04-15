<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountingRuleRequest;
use App\Http\Requests\UpdateAccountingRuleRequest;
use App\Http\Resources\AccountingRuleResource;
use App\Models\AccountingRule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AccountingRuleController extends Controller
{
    public function index(): InertiaResponse
    {
        $rules = AccountingRule::query()->get();

        return Inertia::render('AccountingRules/Index', [
            'rules' => AccountingRuleResource::collection($rules),
        ]);
    }

    public function store(StoreAccountingRuleRequest $request): RedirectResponse
    {
        AccountingRule::create($request->validated());

        return redirect()->back()->with('success', 'Règle comptable créée.');
    }

    public function update(UpdateAccountingRuleRequest $request, AccountingRule $accountingRule): RedirectResponse
    {
        $accountingRule->update($request->validated());

        return redirect()->back()->with('success', 'Règle comptable mise à jour.');
    }

    public function destroy(AccountingRule $accountingRule): RedirectResponse
    {
        $accountingRule->delete();

        return redirect()->back()->with('success', 'Règle comptable supprimée.');
    }
}
