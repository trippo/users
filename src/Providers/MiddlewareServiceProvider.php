<?php namespace WebEd\Base\Users\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

use WebEd\Base\Users\Http\Middleware\AuthenticateAdmin;
use WebEd\Base\Users\Http\Middleware\GuestAdmin;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('webed.auth-admin', AuthenticateAdmin::class);
        $router->aliasMiddleware('webed.guest-admin', GuestAdmin::class);
        $router->pushMiddlewareToGroup('web', AuthenticateAdmin::class);
    }
}
