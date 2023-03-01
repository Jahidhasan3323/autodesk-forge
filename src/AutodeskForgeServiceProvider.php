<?php
namespace AutodeskForge;
use Carbon\Laravel\ServiceProvider;

class AutodeskForgeServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->mergeConfigFrom(__DIR__.'/config/autodeskForge.php', 'autodeskForge');
        $this->publishes([
            __DIR__.'/config/autodeskForge.php' => config_path('autodeskForge.php')
        ]);
    }

    /**
     * @return void
     */
    public function register(): void
    {

    }

}
