<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BankAccountController extends Controller
{
    public function index(): InertiaResponse
    {
        $bankAccounts = BankAccount::query()->with('accountingAccount')->get();

        return Inertia::render('BankAccounts/Index', [
            'bankAccounts' => BankAccountResource::collection($bankAccounts),
        ]);
    }

    public function store(StoreBankAccountRequest $request): RedirectResponse
    {
        BankAccount::create($request->validated());

        return redirect()->back()->with('success', 'Compte bancaire enregistré avec succès.');
    }

    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->update($request->validated());

        return redirect()->back()->with('success', 'Compte bancaire mis à jour.');
    }

    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()->back()->with('success', 'Compte bancaire supprimé.');
    }
}
