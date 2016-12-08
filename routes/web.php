<?php use Illuminate\Routing\Router;

/**
 *
 * @var Router $router
 *
 */

$adminRoute = config('webed.admin_route');

$moduleRoute = 'users';

/*
 * Admin route
 * */
$router->group(['prefix' => $adminRoute . '/' . $moduleRoute], function (Router $router) use ($adminRoute, $moduleRoute) {
    $router->get('/', 'UserController@getIndex')
        ->name('admin::users.index.get')
        ->middleware('has-permission:view-users');

    $router->post('/', 'UserController@postListing')
        ->name('admin::users.index.post')
        ->middleware('has-permission:view-users');

    $router->post('update-status/{id}/{status}', 'UserController@postUpdateStatus')
        ->name('admin::users.update-status.post')
        ->middleware('has-permission:edit-other-users');

    $router->get('create', 'UserController@getCreate')
        ->name('admin::users.create.get')
        ->middleware('has-permission:create-users');

    $router->get('edit/{id}', 'UserController@getEdit')
        ->name('admin::users.edit.get');
    $router->post('edit/{id}', 'UserController@postEdit')
        ->name('admin::users.edit.post');
});
