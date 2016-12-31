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

    $router->post('restore/{id}', 'UserController@postRestore')
        ->name('admin::users.restore.post')
        ->middleware('has-permission:edit-other-users');

    $router->get('create', 'UserController@getCreate')
        ->name('admin::users.create.get')
        ->middleware('has-permission:create-users');

    $router->post('create', 'UserController@postCreate')
        ->name('admin::users.create.post')
        ->middleware('has-permission:create-users');

    $router->get('edit/{id}', 'UserController@getEdit')
        ->name('admin::users.edit.get');
    $router->post('edit/{id}', 'UserController@postEdit')
        ->name('admin::users.edit.post');

    $router->post('update-password/{id}', 'UserController@postUpdatePassword')
        ->name('admin::users.update-password.post');

    $router->delete('delete/{id}', 'UserController@deleteDelete')
        ->name('admin::users.delete.delete')
        ->middleware('has-permission:delete-users');

    $router->delete('force-delete/{id}', 'UserController@deleteForceDelete')
        ->name('admin::users.force-delete.delete')
        ->middleware('has-permission:force-delete-users');
});
