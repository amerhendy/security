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
class AmerSecurityServiceProvider extends ServiceProvider
{
    public $startcomm="SEC";
    protected $commands = [];
    protected $defer = false;
    public $pachaPath="Amerhendy\Security\\";
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $path=base_path('vendor/AmerHendy/Security/src/');
        $this->loadConfigs();
        $this->loadMigrationsFrom($path.'database/migrations');
        $this->loadroutes($this->app->router);
        $this->loadTranslationsFrom(__DIR__.'/Lang','SECLANG');
        $this->loadViewsFrom($path.'/view', 'SEC');
        $this->loadGuards();
        $this->registerMiddlewareGroup($this->app->router);
        $this->addmainmenu();
        $this->publishFiles();
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
    public function addmainmenu(){
        $sidelayout_path=resource_path('views/vendor/Amer/Base/inc/menu/admin.blade.php');
        $file_lines=File::lines($sidelayout_path);
        $viewfile=__DIR__.'/view/adminsidemenu.blade.stub';
        $newlines=File::lines($viewfile);
        if(!$this->getLastLineNumberThatContains("userpermisions",$file_lines->toArray())){
            $newarr=array_merge($file_lines->toArray(),$newlines->toArray());
            $new_file_content = implode(PHP_EOL, $newarr);
            File::put($sidelayout_path,$new_file_content);
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
        foreach(getallfiles(__DIR__.'/config/') as $file){
            $this->publishes([$file => config_path(Str::replace('/','.',Str::afterLast(Str::remove('.php',$file),'config/')).'.php')], 'SEC_config');
            //dd(Str::replace('/','.',Str::afterLast(Str::remove('.php',$file),'config/')));
            //$this->mergeConfigFrom($file,Str::replace('/','.',Str::afterLast(Str::remove('.php',$file),'config/')),'securityconfig');
        }
    }
    
    public function loadGuards(){
        $b=config('amerSecurity.auth');
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
                'expire'    => config('amerSecurity.password_recovery_token_expiration', 60),
                'throttle'  => config('amerSecurity.password_recovery_throttle_notifications'),
            ],
        ];
        /////////////////////////// publish Api/////////////////////////
        app()->config['auth.guards'] = app()->config['auth.guards'] +
        [
            'api' => [
                'driver' => 'token',
                'provider' => 'users',
                'hash' => false,
            ],
        ];
    }
    public function registerMiddlewareGroup(Router $router)
    {
        $b=config('amerSecurity.auth');
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
            if (config('amerSecurity.setup_password_recovery_routes')) {

                $router->aliasMiddleware(config('amerSecurity.auth.middleware_key').'.throttle.password.recovery', ThrottlePasswordRecovery::class);
            }
    }
}
