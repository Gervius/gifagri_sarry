<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Setting::class, 'setting');
    }

    public function index(): InertiaResponse
    {
        $settings = Setting::query()->get();

        return Inertia::render('Settings/Index', [
            'settings' => SettingResource::collection($settings),
        ]);
    }

    public function store(StoreSettingRequest $request): RedirectResponse
    {
        Setting::create($request->validated());

        return redirect()->back()->with('success', 'Paramètre créé.');
    }

    public function update(UpdateSettingRequest $request, Setting $setting): RedirectResponse
    {
        $setting->update($request->validated());

        return redirect()->back()->with('success', 'Paramètre mis à jour.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        $setting->delete();

        return redirect()->back()->with('success', 'Paramètre supprimé.');
    }
}
