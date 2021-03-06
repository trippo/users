<?php namespace WebEd\Base\Users\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use WebEd\Base\Users\Http\Middleware\BootstrapModuleMiddleware;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*Load views*/
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'webed-users');
        /*Load translations*/
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'webed-users');
        /*Load migrations*/
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../resources/views' => config('view.paths')[0] . '/vendor/webed-users',
        ], 'views');
        $this->publishes([
            __DIR__ . '/../../resources/lang' => base_path('resources/lang/vendor/webed-users'),
        ], 'lang');
        $this->publishes([
            __DIR__ . '/../../config' => base_path('config'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../../resources/assets' => resource_path('assets'),
        ], 'webed-assets');
        $this->publishes([
            __DIR__ . '/../../resources/public' => public_path(),
        ], 'webed-public-assets');

        app()->booted(function () {
            $this->app->register(BootstrapModuleServiceProvider::class);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        load_module_helpers(__DIR__);

        //Merge configs
        $configs = split_files_with_basename($this->app['files']->glob(__DIR__ . '/../../config/*.php'));

        foreach ($configs as $key => $row) {
            $this->mergeConfigFrom($row, $key);
        }

        config([
            'auth.defaults' => [
                'guard' => 'web-auth',
                'passwords' => 'users',
            ],
            'auth.guards.web-auth' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'auth.providers.users' => [
                'driver' => 'eloquent',
                'model' => \WebEd\Base\Users\Models\User::class,
            ],
            'auth.passwords.users' => [
                'provider' => 'users',
                'table' => 'password_resets',
                'expire' => 60,
            ],
        ]);

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(MiddlewareServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', BootstrapModuleMiddleware::class);
    }
}
