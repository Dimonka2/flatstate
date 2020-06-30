<?php

namespace dimonka2\flatstate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;

class FlatstateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    private const config = 'flatstate.php';
    public function register()
    {
        AliasLoader::getInstance(config('aliases', []));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('flatstates', StateManager::class);
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigFile() => config_path(self::config),
            ], 'config');
        } else {            
            $this->app->bind('Flatstate', FlatstateService::class);
            Blade::directive(config('state_directive', 'state'), function ($state) {
                return "<?php echo app('flatstates')->formatState($state); ?>";
            });            
        }
    }

    protected function getConfigFile(): string
    {
        return __DIR__ . '/../config/' . self::config;
    }
}
