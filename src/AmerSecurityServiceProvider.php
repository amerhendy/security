<?php
namespace Amerhendy\Security;
use Illuminate\Support\Facades\Config;
use Amerhendy\Employment\App\Helpers\AmerHelper;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanel;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanelFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;  
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Amerhendy\Security\app\Http\Middleware\ThrottlePasswordRecovery;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
class AmerSecurityServiceProvider extends ServiceProvider
{
    public $startcomm="SEC";
    protected $commands = [];
    protected $defer = false;
    public $pachaPath="Amerhendy\Security\\";
    public function register(): void
    {
        require_once __DIR__.'/macro.php';   
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $path=base_path('vendor/AmerHendy/Security/src/');
        $this->loadConfigs();
        $this->publishFiles();
        $this->loadMigrationsFrom($path.'database/migrations');
        $this->loadroutes($this->app->router);
        $this->loadTranslationsFrom(__DIR__.'/Lang','SECLANG');
        $this->loadViewsFrom($path.'/view', 'SEC');
        $this->loadGuards();
        $this->registerMiddlewareGroup($this->app->router);
        
    }
    public function loadConfigs(){
        foreach(getallfiles(__DIR__.'/config/') as $file){
            $this->mergeConfigFrom($file,Str::replace('/','.',Str::afterLast(Str::remove('.php',$file),'config/')),'securityconfig');
        }
    }
    public function loadroutes(Router $router)
    {
        $packagepath=base_path('vendor/AmerHendy/Security/src/');
        $routepath=$this->getallfiles($packagepath.'/Route/');
        foreach($routepath as $path){
            $this->loadRoutesFrom($path);
        }
    }
    public function getLastLineNumberThatContains($needle, $haystack,$skipcomment=false)
    {
        $matchingLines = array_filter($haystack, function ($k) use ($needle,$skipcomment) {
            if($skipcomment == true){
                if(!Str::startsWith(trim($k),'//')){
                    return strpos($k, $needle) !== false;
                }
            }else{
                    return strpos($k, $needle) !== false;
            }
            
        });
        if ($matchingLines) { 
            return array_key_last($matchingLines);
        }

        return false;
    }   
    function getallfiles($path){
        $files = array_diff(scandir($path), array('.', '..'));
        $out=[];
        foreach($files as $a=>$b){
            if(is_dir($path."/".$b)){
                $out=array_merge($out,getallfiles($path."/".$b));
            }else{
                $ab=Str::after($path,'/vendor');
                $ab=Str::replace('//','/',$ab);
                $ab=Str::finish($ab,'/');
                $out[]=$ab.$b;
            }
        }
        return $out;
    }
    function publishFiles()  {
        $pb=config('Amer.Security.package_path') ?? __DIR__;
        $config_files = [$pb.'/config' => config_path()];
        $this->publishes($config_files, $this->startcomm.':SecConfig');
    }
    
    public function loadGuards(){
        $b=config('Amer.Security.auth');
        $name=$b['middleware_key'];
        $model=$b['model'];
        app()->config['auth.guards'] = app()->config['auth.guards'] +
                [
                    $name => [
                        'driver'   => 'session',
                        'provider' => $name,
                    ],
                ];
                app()->config['auth.providers'] = app()->config['auth.providers'] +
                [
                    $name => [
                        'driver'  => 'eloquent',
                        'model'   => $model,
                    ],
                ];
        //////////////////////// publish Amer Guard/////////////////////////
        
        app()->config['auth.passwords'] = app()->config['auth.passwords'] +
        [
            'Amer' => [
                'provider'  => 'Amer',
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
        $name=$b['middleware_key'];
        $model=$b['model'];
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
            app('router')->aliasMiddleware('client', CheckClientCredentials::class);
    }
}
