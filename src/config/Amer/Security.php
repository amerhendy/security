<?php
return [
    'route_prefix' => 'Security',
    'routeName_prefix'=>'Security',
    'web_middleware' => 'web',
    'view_namespace' => 'Amer::',
    'root_disk_name' => 'amer',
    'package_path'=>base_path().'\\vendor\Amerhendy\Security\src\\',
        'auth'=>[
            'model'=>\Amerhendy\Security\App\Models\Amer::class,
            'middleware_key'=>'Amer',
            'middleware_class'=>[
                \Amerhendy\Security\App\Http\Middleware\CheckIfAdmin::class,
                \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
                \Amerhendy\Security\App\Http\Middleware\AuthenticateSession::class,
            ],
        ],
    'nameSpace'=>'Amerhendy\Security',
    'Controllers'=>'Amerhendy\Security\App\Http\Controllers',
    'password_recovery_token_expiration' => 60,
    'password_recovery_throttle_notifications' => 600, // time in seconds
    'password_recovery_throttle_access' => '3,10',
    'guard' => 'Amer',
    'passwords' => 'Amer',
    'authentication_column'      => 'email',
    'authentication_column_name' => 'Email',
    'setup_password_recovery_routes' => true,
];
