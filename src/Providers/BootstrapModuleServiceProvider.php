<?php namespace WebEd\Base\Users\Providers;

use Illuminate\Support\ServiceProvider;

class BootstrapModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        app()->booted(function () {
            $this->booted();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function booted()
    {
        /**
         * Register to dashboard menu
         */
        \DashboardMenu::registerItem([
            'id' => 'webed-users',
            'priority' => 3,
            'parent_id' => null,
            'heading' => trans('webed-users::base.admin_menu.heading'),
            'title' => trans('webed-users::base.admin_menu.title'),
            'font_icon' => 'icon-users',
            'link' => route('admin::users.index.get'),
            'css_class' => null,
            'permissions' => ['view-users'],
        ]);
    }
}
