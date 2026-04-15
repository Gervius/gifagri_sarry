<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AccountController extends Controller
{
    public function index(): InertiaResponse
    {
        $accounts = Account::query()->get();

        return Inertia::render('Accounts/Index', [
            'accounts' => AccountResource::collection($accounts),
        ]);
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        Account::create($request->validated());

        return redirect()->back()->with('success', 'Compte comptable créé avec succès.');
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $account->update($request->validated());

        return redirect()->back()->with('success', 'Compte comptable mis à jour.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        if ($account->journalEntries()->exists()) {
            abort(409, 'Impossible de supprimer un compte qui contient des écritures comptables.');
        }

        $account->delete();

        return redirect()->back()->with('success', 'Compte comptable supprimé.');
    }
}
