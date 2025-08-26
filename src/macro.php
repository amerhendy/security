<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Http\Request;
if (! function_exists('Sec_url')) {
    function Sec_url($path = null, $parameters = [], $secure = null)
    {
        $path = ! $path || (substr($path, 0, 1) == '/') ? $path : '/'.$path;
        return url(config('Amer.Amer.api_version')."/".config('Amer.Security.routeName_prefix', 'Security').$path, $parameters, $secure);
    }
}
if (! function_exists('checkTokenGuard')) {
 function checkTokenGuard(Request $request,$type='check')
    {
        $guards = array_keys(config('auth.guards'));
        //dd($guards);
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if($type == 'get'){return $guard;}
                //dd($guard);
                return true;
            }
        }
        return false;
    }
}
?>
