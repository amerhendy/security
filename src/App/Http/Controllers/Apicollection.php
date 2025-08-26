<?php
namespace Amerhendy\Security\App\Http\Controllers;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Amerhendy\Security\App\Models\Amer;
class Apicollection extends Controller
{
    function authStatus(Request $request){
        $AmerLoggedIn=\AmerHelper::checkTokenGuard($request);
        //dd(Auth::guard('Amer')->user());
        $Employers=false;
        if(\AmerHelper::modelexists('\Amerhendy\Employers\App\Models\Base\Employer')){
            $Employers=true;
        }
        $Employers=Auth::guard('Employers')->check();
        return response()->json([
            'isAmerLoggedIn' => $AmerLoggedIn,
            'isEmployersLoggedIn' => $Employers,
            'hasEmployersModel' => \AmerHelper::modelexists('\Amerhendy\Employers\App\Models\Base\Employer'),
            'hasSecurityModel' => \AmerHelper::modelexists('\Amerhendy\Security\App\Models\Amer'),
        ]);
    }
    public function Menu(Request $request){
        $AmerLoggedIn=AmerHelper::checkTokenGuard($request);
        return $this->listPers();
        //checkpermissions
        //get full menu
    }
    public function listPers(){
        $UserId=amer_user()->id;
        $user = Amer::with(['roles','roles.permissions','permissions','team','teams'])->whereId( $UserId)->first();
        return $user;
    }
}
