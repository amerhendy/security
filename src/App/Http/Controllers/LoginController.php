<?php
namespace Amerhendy\Security\App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Alert;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
///////////////////////////////تظبيط ملف الدخول والخروج للسكيورتى والعاملين
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:Employers')->except('logout');
    }
    public function login (Request $request)
	{
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()]);
        }
        
        $error=[];
        if($request->remember == 'on'){$remembers=true;}else{$remembers=false;}
        $user=USER::where('email',$request->email)->first();
        if($user){
            if(!Hash::check($request->password, $user->password)){
                $error['password']='كلمة المرور غير صحيحة';
            }else{
                Auth::guard(config('Amer.Security.auth.middleware_key'))->loginUsingId($user->id, $remember = $remembers);
                //$token = $user->createToken('Employer')->accessToken;
                $request->session()->regenerate();
                $response = ['request'=>$request->toArray()];
                Alert::add('success', trans('SECLANG::auth.loginsuccessed'))->flash();
                return response($response, 200);
            }
        }else{
            $error['email']='البريد الالكترونى غير صحيح';
        }
        return response(['errors'=>$error]);
    }
    public function logout(Request $request){
        if(Auth::guard(config('Amer.Security.auth.middleware_key'))->check()){
            Auth::guard(config('Amer.Security.auth.middleware_key'))->logout();
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Alert::add('success', trans('SECLANG::auth.logoutsuccessed'))->flash();
            return $this->loggedOut($request) ?: redirect()->back();
        }
    }
    public function loggedOut($request){
        return redirect()->back();
    }
}