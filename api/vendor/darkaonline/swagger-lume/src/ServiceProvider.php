<?php

namespace SwaggerLume;

use Illuminate\Support\ServiceProvider as BaseProvider;
use SwaggerLume\Console\GenerateDocsCommand;
use SwaggerLume\Console\PublishCommand;
use SwaggerLume\Console\PublishConfigCommand;
use SwaggerLume\Console\PublishViewsCommand;

class ServiceProvider extends BaseProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'swagger-lume');

        $this->app->router->group(['namespace' => 'SwaggerLume'], function ($route) {
            require __DIR__.'/routes.php';
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/swagger-lume.php';
        $this->mergeConfigFrom($configPath, 'swagger-lume');

        $this->app->singleton('command.swagger-lume.publish', function () {
            return new PublishCommand();
        });

        $this->app->singleton('command.swagger-lume.publish-config', function () {
            return new PublishConfigCommand();
        });

        $this->app->singleton('command.swagger-lume.publish-views', function () {
            return new PublishViewsCommand();
        });

        $this->app->singleton('command.swagger-lume.generate', function () {
            return new GenerateDocsCommand();
        });

        $this->commands(
            'command.swagger-lume.publish',
            'command.swagger-lume.publish-config',
            'command.swagger-lume.publish-views',
            'command.swagger-lume.generate'
        );
    }
}
