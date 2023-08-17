<?php
use Illuminate\Support\Facades\Route;
//admin area
Route::group(
            [
                'prefix'=>config('amerSecurity.route_prefix','amer'),
                'namespace' => config('amerSecurity.Controllers'),
                'middleware' =>array_merge((array) config('amer.web_middleware'),(array) config('amer.admin_auth.middleware_key')),
                'name'=>'admin.',
            ],
            function(){
                Route::get('logout', 'LoginController@logout')->name('admin.logout-get');
                Route::post('logout', 'LoginController@logout');
                Route::get('amer', 'AdminController@redirect')->name('Admin');
                Route::get('/dashboard', 'AdminController@dashboard')->name('Admin.dashboard');
                Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('admin.account.info');
                Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('admin.account.info.store');
                Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('admin.account.password');
                Route::Amer('permission','PermissionAmerController');
                Route::Amer('role','RoleAmerController');
                Route::Amer('user','UserAmerController');
                Route::Amer('Teams','TeamsAmerController');
});

Route::group(
    [
        'namespace' => config('amerSecurity.Controllers'),
        'middleware' => config('amer.web_middleware', 'web'),
        'prefix'=>config('amerSecurity.route_prefix','amer'),
    ],function(){
        Route::post('admin/login', 'ApiAuthController@BackLogin')->name('Back.login.api');
    Route::post('/login', 'ApiAuthController@login')->name('login.api');
    Route::post('/register','ApiAuthController@register')->name('register.api');
    Route::post('/logout', 'ApiAuthController@logout')->name('logout.api');
    }
);