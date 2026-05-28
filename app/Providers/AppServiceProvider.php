<?php

namespace App\Providers;

use App\Models\Configuracion;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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
        // Compartir configuraciones en todas las vistas
        View::composer('*', function ($view) {
            if (!Schema::hasTable('configuraciones')) return;

            $config = Cache::remember('configuraciones_globales', 3600, function () {
                return Configuracion::all()
                    ->mapWithKeys(fn($c) => [$c->nombre_conf => $c->valor])
                    ->toArray();
            });

            $view->with('conf', $config);
        });
        
        $this->configureDefaults();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        if (request()->hasHeader('X-Forwarded-Host')) {
            URL::forceRootUrl(config('app.url'));
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        \Carbon\Carbon::setLocale('es');

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
