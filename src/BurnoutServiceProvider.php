<?php

namespace DefStudio\Burnout;

use DefStudio\Burnout\Commands\Cleanup;
use DefStudio\Burnout\Models\BurnoutEntry;
use DefStudio\Burnout\Policies\BurnoutEntryPolicy;
use Facade\FlareClient\Report;
use Facade\Ignition\Facades\Flare;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class BurnoutServiceProvider extends ServiceProvider
{
    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Cleanup::class
            ]);
        }

        $this->app->booted(function ($app) {
            $app->make(Schedule::class)->command('burnout:cleanup')->daily();
        });


        $this->publishes([
            __DIR__ . '/../config/burnout.php' => config_path('burnout.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/burnout'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__ . "/../database/migrations/2020_10_05_100000_create_burnout_table.php");


        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'burnout');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Gate::policy(BurnoutEntry::class, BurnoutEntryPolicy::class);

        Flare::registerMiddleware(function (Report $report, $next) {

            $burnout = new Burnout();

            if ($burnout->is_enabled()) {
                $burnout->store_report($report);
            }

            return $next($report);
        });
    }

    public function register()
    {


        $this->app->bind('burnout', function () {
            return new Burnout();
        });


        $this->mergeConfigFrom(__DIR__ . '/../config/burnout.php', 'burnout');
    }
}
