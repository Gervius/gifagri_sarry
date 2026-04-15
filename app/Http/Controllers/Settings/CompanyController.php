<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CompanySettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:update settings']);
    }

    public function edit(): Response
    {
        $keys = [
            'company_name',
            'company_rccm',
            'company_ifu',
            'company_address',
            'company_phone',
            'company_email',
            'company_logo_path',
        ];

        $settings = Setting::query()
            ->whereIn('key', $keys)
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        return Inertia::render('Settings/Company', [
            'companySettings' => $settings,
            'companyLogoUrl' => isset($settings['company_logo_path']) ? Storage::disk('public')->url($settings['company_logo_path']) : null,
        ]);
    }

    public function update(CompanySettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $data['company_logo_path'] = $logoPath;

            $currentLogo = Setting::where('key', 'company_logo_path')->value('value');

            if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }
        }

        $fields = [
            'company_name' => 'string',
            'company_rccm' => 'string',
            'company_ifu' => 'string',
            'company_address' => 'string',
            'company_phone' => 'string',
            'company_email' => 'string',
            'company_logo_path' => 'file',
        ];

        foreach ($fields as $key => $type) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data[$key],
                    'type' => $type,
                ]
            );
        }

        return redirect()->back()->with('success', 'Informations de l\'entreprise sauvegardées.');
    }
}
