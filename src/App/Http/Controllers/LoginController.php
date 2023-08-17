<?php
namespace Amerhendy\Security\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Amerhendy\Amer\App\Models\Base\Employers;
use Auth;
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
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    
    protected $redirectTo = '/Employers';

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
    public function showAdminLoginForm()
    {
        return view('layout.login', ['url' => 'Employers']);

    }
    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'uid'   => 'required|numeric',
            'nid' => 'required'
        ]);
        $error=[];
        $user=Employers::where('nid',$request->nid)->get()->toArray();
        if(count($user) == 1){
            if($request->uid !== $user[0]['uid']){
                $error['uid']='رقم الملف الوظيفى غير صحيح';
            }else{
                Auth::guard('Employers')->loginUsingId($user[0]['id']);
                $request->session()->regenerate();
                return redirect(route('employerdashboard'));
            }
        }else{
            $error['nid']='الرقم القومى غير صحيح';
        }
        return back()->withInput()->withErrors($error);
        
        if(count($user) == 1){
            Auth::guard('Employers')->loginUsingId($user[0]['id']);
            $request->session()->regenerate();
            return redirect(route('employerdashboard'));
        }else{
            return redirect(route('employerlogin-form'));
        }
        if (Auth::guard('Employers')->attempt(['uid' => $request->uid, 'nid' => $request->nid], $request->get('remember'))) {
            return redirect()->intended('/Employers');
        }else{
            return $this->sendLoginResponse($request);
        }
    }
    public function adminLogout(Request $request){
        dd('sdd');
    }
}