<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
    Route::post('Security/admin/login', 'LoginController@login')->name('Back.admin.login.api')->middleware('api');
    //Route::post('Security/login', 'ApiAuthController@login')->name('login.api');
    //Route::post('Security/register','ApiAuthController@register')->name('register.api');
    Route::post('Security/logout', 'ApiAuthController@logout')->name('logout.api');

    Route::post('setcollPerms','\Amerhendy\Security\App\Http\Controllers\PermissionAmerController@newPermissions');
// routes/api.php
    Route::get('/auth/status','Apicollection@authStatus')->middleware('api');
    Route::get('Security/admin/Menu', 'Apicollection@Menu')->name('Back.admin.Menu.api')->middleware('api');
    Route::middleware(['auth:Amer-api'])->get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('admin.account.infos');
    Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('admin.account.info.stores');
    Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('admin.account.passwords');
    Route::group(
    [
        'namespace' => "\\".config('Amer.Security.Controllers'),
        'middleware' =>['auth:Amer-api'],
        'prefix'=>config('Amer.Security.route_prefix','Security'),
        'name'=>config('Amer.Security.routeName_prefix','Security'),
    ],function(){
        Route::Amer('user','UserAmerController');
    });

