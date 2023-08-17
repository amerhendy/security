<?php
namespace Amerhendy\Security\App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\models\User;
use App\Models\Employers;

class ApiAuthController extends Controller
{
    public function BackLogin (Request $request){
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
                Auth::guard('Amer')->loginUsingId($user->id, $remember = $remembers);
                //$token = $user->createToken('Employer')->accessToken;
                $request->session()->regenerate();
                $response = ['request'=>$request->toArray()];
                return response($response, 200);
            }
        }else{
            $error['email']='البريد الالكترونى غير صحيح';
        }
        return response(['errors'=>$error]);
    }
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }
    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
