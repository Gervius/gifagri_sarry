<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DailyRecordApproved;
use App\Events\SettingsUpdatedEvent;
use App\Events\TreatmentApproved;
use App\Listeners\DeductFeedFromStock;
use App\Listeners\DeductMedicationFromStock;
use App\Listeners\InvalidateReferenceCache;
use App\Listeners\ProcessEggProduction;
use App\Listeners\UpdateFlockMortality;
use App\Models\AccountingRule;
use App\Models\AnimalType;
use App\Models\DailyRecord;
use App\Models\Flock;
use App\Models\ProductionPhase;
use App\Models\Treatment;
use App\Models\Unit;
use App\Observers\DailyRecordObserver;
use App\Observers\FlockObserver;
use App\Observers\ReferenceParameterObserver;
use App\Observers\TreatmentObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerObservers();
        $this->registerEventListeners();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {

        // Le listener synchrone pour la validation d'un Treatment
        Event::listen(TreatmentApproved::class, DeductMedicationFromStock::class);
        // Les trois listeners synchrones pour la validation d'un DailyRecord
        Event::listen(DailyRecordApproved::class, DeductFeedFromStock::class);
        Event::listen(DailyRecordApproved::class, ProcessEggProduction::class);
        Event::listen(DailyRecordApproved::class, UpdateFlockMortality::class);
        Event::listen(SettingsUpdatedEvent::class, InvalidateReferenceCache::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Flock::observe(FlockObserver::class);
        DailyRecord::observe(DailyRecordObserver::class);
        Treatment::observe(TreatmentObserver::class);
        AnimalType::observe(ReferenceParameterObserver::class);
        ProductionPhase::observe(ReferenceParameterObserver::class);
        AccountingRule::observe(ReferenceParameterObserver::class);
        Unit::observe(ReferenceParameterObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
