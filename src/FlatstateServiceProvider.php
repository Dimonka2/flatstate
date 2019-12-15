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
        AliasLoader::getInstance(config('flatstate.aliases', []));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigFile() => config_path(self::config),
            ], 'config');
        } else {
            $this->app->bind('dimonka2.flatstate', FlatstateService::class);
            /* Blade::directive(config('flatform.blade_directive', 'form'), function ($form) {
            //     return "<?php echo \dimonka2\\flatform\Flatform::render($form); ?>";
            // });
            // $this->loadViewsFrom(
            //     config('flatform.views_directory', __DIR__.'/../views'), 'flatform');
            */
        }
    }

    protected function getConfigFile(): string
    {
        return __DIR__ . '/../config/' . self::config;
    }
}
