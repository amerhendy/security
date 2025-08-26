<?php
namespace Amerhendy\Security;
use Illuminate\Support\Facades\Config;
use Amerhendy\Employment\App\Helpers\AmerHelper;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanel;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanelFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Amerhendy\Security\app\Http\Middleware\ThrottlePasswordRecovery;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
class AmerSecurityServiceProvider extends ServiceProvider
{
    use \Amerhendy\Amer\App\Helpers\Library\Database\PublishesMigrations;
    public $startcomm="SEC";
    protected $commands = [];
    protected $defer = false;
    public static $pachaPath="Amerhendy\Security\\";
    public static $config;
    public static $GuardName,$providerName;
    public function register(): void
    {
        require_once __DIR__.'/macro.php';
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadConfigs();
        self::$config=Config('Amer.Security');
        self::$pachaPath=cleanDir(__DIR__);
        $this->loadViewsFrom(cleanDir([self::$pachaPath,'view']), 'SEC');
        $this->loadTranslationsFrom(cleanDir([self::$pachaPath,"lang"]), 'SECLANG');
        $this->registerMigrations(cleanDir([self::$pachaPath,"database",'migrations']));
        $this->publishFiles();
        $this->loadGuards();
        $this->registerMiddlewareGroup($this->app->router);
        $this->loadroutes($this->app->router);
    }
    public function loadConfigs(){
        foreach(getallfiles(__DIR__.'/config') as $file){
            if(!Str::contains($file, 'config'.DIRECTORY_SEPARATOR."Amer".DIRECTORY_SEPARATOR)){
                $name=Str::afterLast(Str::remove('.php',$file),'config'.DIRECTORY_SEPARATOR);
            }else{
                $name='Amer.'.ucfirst(Str::afterLast(Str::remove('.php',$file),'config'.DIRECTORY_SEPARATOR."Amer".DIRECTORY_SEPARATOR));
            }

            $this->mergeConfigFrom(
                $file,$name
            );
        }
    }
    function publishFiles()  {
        $config_files = [cleanDir([self::$pachaPath,'config']).DIRECTORY_SEPARATOR.'permission.php' => config_path()];
        $this->publishes($config_files, $this->startcomm.':SecConfig');
        $public_assets = [cleanDir([self::$pachaPath,'public']) => config('Amer.Amer.public_path')];
        $this->publishes($public_assets, $this->startcomm.':public');
    }
    public function loadGuards(){
        $b=config('Amer.Security.auth');
        self::$GuardName=\Str::singular($b['middleware_key']);
        self::$providerName=\Str::plural(self::$GuardName);
        $model=$b['model'];
        app()->config['auth.guards'] = app()->config['auth.guards'] +
            [
                self::$GuardName => [
                    'driver'   => 'session',
                    'provider' => self::$providerName,
                ],
            ];
        app()->config['auth.guards'] = app()->config['auth.guards'] +
            [
                self::$GuardName."-api" => [
                    'driver'   => 'passport',
                    'provider' => self::$providerName,
                ],
            ];
            app()->config['auth.providers'] = app()->config['auth.providers'] +
            [
                self::$providerName => [
                    'driver'  => 'eloquent',
                    'model'   => $model,
                ],
            ];
        //////////////////////// publish Amer Guard/////////////////////////

        app()->config['auth.passwords'] = app()->config['auth.passwords'] +
        [
            'Amer' => [
                'provider'  => self::$providerName,
                'table'     => 'password_resets',
                'expire'    => config('Amer.Security.password_recovery_token_expiration', 60),
                'throttle'  => config('Amer.Security.password_recovery_throttle_notifications'),
            ],
        ];
        /////////////////////////// publish Api/////////////////////////
        //\Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        app()->config['auth.guards'] = app()->config['auth.guards'] +
        [
            'api' => [
                'driver' => 'passport',
                'provider' => 'users',
                'hash' => false,
            ],
        ];
    }
    public function registerMiddlewareGroup(Router $router)
    {
        $b=config('Amer.Security.auth');
            $middleware_key = $b['middleware_key'];
            $middleware_class = $b['middleware_class'];
            if (! is_array($middleware_class)) {
                $router->pushMiddlewareToGroup($middleware_key, $middleware_class);
                return;
            }
            foreach ($middleware_class as $middleware_class) {
                $router->pushMiddlewareToGroup($middleware_key, $middleware_class);
            }
            $router->pushMiddlewareToGroup('json.response', \Amerhendy\Security\App\Http\Middleware\ForceJsonResponse::class);
            $router->pushMiddlewareToGroup('api', \Amerhendy\Security\App\Http\Middleware\ForceJsonResponse::class);
            $router->pushMiddlewareToGroup('cors', \Amerhendy\Security\App\Http\Middleware\Cors::class);
            $router->middleware(\Amerhendy\Security\App\Http\Middleware\ForceJsonResponse::class);
            if (config('Amer.Security.setup_password_recovery_routes')) {
                $router->aliasMiddleware(config('Amer.Security.auth.middleware_key').'.throttle.password.recovery', ThrottlePasswordRecovery::class);
            }
            $router->pushMiddlewareToGroup('auth:Amer-api', \Amerhendy\Security\App\Http\Middleware\ForceJsonResponse::class);
            $router->pushMiddlewareToGroup('amer', 'auth:Amer-api');
            app('router')->aliasMiddleware('client', EnsureClientIsResourceOwner::class);
    }
    public function loadroutes(Router $router)
    {
        $routepath=getallfiles(cleanDir([self::$pachaPath,'Route']));
        foreach($routepath as $path){
            if(!\Str::contains($path, 'api.php')){
                $this->loadRoutesFrom($path);
            }else{
                Route::group($this->apirouteConfiguration(), function () use($path){
                    $this->loadRoutesFrom($path);
                });
            }
        }
    }
    protected function apirouteConfiguration()
    {
        return [
            'prefix' =>'api/'.config('Amer.Amer.api_version')??'v1',
            'name'=>(config('Amer.Security.routeName_prefix') ?? 'amer').'Api',
            'namespace'  =>config('Amer.Security.Controllers','\\Amerhendy\Security\App\Http\Controllers\\'),
        ];
    }
}
