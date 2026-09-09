<?php

namespace Modules\Quotations\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QuotationsServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Quotations';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'quotations';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerCommands();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Auto-run version cleanup once per day (no cron needed)
        $this->autoCleanupOldVersions();
    }

    /**
     * Automatically run the old-version cleanup once per day.
     * Uses cache to ensure it only runs once every 24 hours,
     * triggered on the first API request of the day.
     */
    protected function autoCleanupOldVersions()
    {
        // Only run during HTTP requests (not during artisan commands, queue workers, etc.)
        if ($this->app->runningInConsole()) {
            return;
        }

        // Use cache lock: if the key exists, cleanup already ran today — skip
        $cacheKey = 'quotations:cleanup-old-versions:last-run';

        if (Cache::has($cacheKey)) {
            return;
        }

        // Set the cache key immediately so concurrent requests don't trigger multiple cleanups
        // Expires in 23 hours (slightly less than 24h to avoid edge cases)
        Cache::put($cacheKey, now()->toDateTimeString(), now()->addHours(23));

        // Run cleanup after the response is sent to the user (non-blocking)
        $this->app->terminating(function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('quotations:cleanup-old-versions');
                Log::info('Auto-cleanup old versions completed: ' . \Illuminate\Support\Facades\Artisan::output());
            } catch (\Exception $e) {
                Log::error('Auto-cleanup old versions failed: ' . $e->getMessage());
            }
        });
    }

    /**
     * Register console commands for the module.
     */
    protected function registerCommands()
    {
        $this->commands([
            \Modules\Quotations\Console\CleanupOldVersions::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
