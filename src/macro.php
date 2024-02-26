<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
if (! function_exists('Sec_url')) {
    function Sec_url($path = null, $parameters = [], $secure = null)
    {
        $path = ! $path || (substr($path, 0, 1) == '/') ? $path : '/'.$path;
        return url(config('Amer.Security.routeName_prefix', 'Security').$path, $parameters, $secure);
    }
}
?>