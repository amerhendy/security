<?php
namespace Amerhendy\Security\App\Http\Controllers;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use \Amerhendy\Security\App\Models\Amer;
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
        //$this->middleware('guest:Employers')->except('logout');
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $amer = Amer::where('email', $request->email)->first();
        if (! $amer || ! Hash::check($request->password, $amer->password)) {
            return response()->json(['errors' => ['بيانات الدخول غير صحيحة']], 401);
        }
        $token = $amer->createToken('amer-token')->accessToken;
        return response([
            'token' => $token,
            'user' => $amer
        ]);
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
    public function htmlLogin(){
        
        return view("SEC::login");
    }
    public function htmlLoginSubmit(Request $request){
        // خطوات تسجيل الدخول
        /////////////
        // 1- تحقق من المدخلات
        $rules=[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];
        $attributes=[
            'email'=>__('SECLANG::auth.email'),
            'password'=>__('SECLANG::auth.password'),
        ];
        $errorMessages=[
            'required' => __('AMER::errors.required',[':attribute']),
            'string' => trans('AMER::errors.string',[':attribute']),
            'max'=>trans('AMER::errors.max',[':attribute',':min']),
            'min'=>trans('AMER::errors.min',[':attribute',':min']),
            'email'=>trans('AMER::errors.email',[':attribute']),
        ];
        $credentials = Validator::make($request->all(), $rules,$errorMessages,$attributes);
        if ($credentials->fails()) {
            return redirect()->back()->withErrors($credentials)->withInput();
        }
    // 2- حاول تسجيل الدخول
        $credentials = $request->only('email', 'password');

    if (Auth::guard('Amer')->attempt($credentials)) {
    $request->session()->regenerate();

    return redirect()->route('Admin.dashboard');
}


    // 4- لو فشل
    return back()->withErrors([
        'email' => 'بيانات الدخول غير صحيحة.',
    ])->onlyInput('email');
        dd($request);
    }
}
