<?php
use Illuminate\Support\Facades\Route;
////////////////////////////////////UI///////////////////////////////////////////////
Route::group(
    [
        'namespace' => config('Amer.Security.Controllers'),
        'middleware' =>array_merge((array) config('Amer.Amer.web_middleware'),(array) config('Amer.Security.auth.middleware_key')),
        'prefix'=>config('Amer.Security.route_prefix','Security'),
        'name'=>config('Amer.Security.routeName_prefix','Security'),
    ],
    function(){
        Route::get('logout', 'LoginController@logout')->name('admin.logout-get');
        Route::post('logout', 'LoginController@logout');
        Route::get('amer', 'AdminController@redirect')->name('Admin');
        Route::get('/dashboard', 'AdminController@dashboard')->name('Admin.dashboard');
        /*Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('admin.account.info');
        Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('admin.account.info.store');
        Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('admin.account.password');*/
        Route::Amer('permission','PermissionAmerController');
        Route::Amer('role','RoleAmerController');
        Route::Amer('user','UserAmerController');
        Route::Amer('Teams','TeamsAmerController');
});
///////////////////////////////////API//////////////////////////////////////////////
Route::group(
    [
        'namespace' => config('Amer.Security.Controllers'),
        'middleware' => config('Amer.Amer.web_middleware', 'web'),
        'prefix'=>config('Amer.Security.route_prefix','amer'),
        'name'=>config('Amer.Security.routeName_prefix','Security'),
    ],function(){
        //Route::post('setcollPerms','\Amerhendy\Security\App\Http\Controllers\PermissionAmerController@newPermissions');
    }
);
