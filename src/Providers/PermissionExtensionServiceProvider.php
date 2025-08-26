<?php
namespace Amerhendy\Security\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class PermissionExtensionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Super Admin bypass
        Gate::before(function (User $user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }

    public function register()
    {
        //
    }
}
