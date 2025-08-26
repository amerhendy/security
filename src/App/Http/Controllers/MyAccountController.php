<?php

namespace Amerhendy\Security\App\Http\Controllers;
use Illuminate\Http\Request;
use Amerhendy\Amer\App\Helpers\Alert;
use Amerhendy\Security\App\Http\Requests\AccountInfoRequest;
use Amerhendy\Security\App\Http\Requests\ChangePasswordRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use PSpell\Config;
use Illuminate\Support\Facades\Auth;
class MyAccountController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
    public $data = [];

    public function __construct()
    {
        //$this->middleware(config('Amer.Security.auth.middleware_key'));
    }
    public function setup()
    {
        $this->Amer->setModel(\Amerhendy\Security\App\Models\User::class);
        $this->Amer->setEntityNameStrings(trans('SECLANG::permissionmanager.role'), trans('SECLANG::permissionmanager.roles'));
        $this->Amer->setRoute(Amerurl('role'));
    }
    public function getAccountInfoForm(Request $request)
    {
        $this->data['title']=trans('AMER::auth.my_account');
        $this->data['user'] = auth('Amer-api')->user();
        return $this->data;
        return view('SEC::Admin.my_account', $this->data);
    }
    public function postAccountInfoForm(AccountInfoRequest $request)
    {
        $result = $this->guard()->user()->update($request->except(['_token']));
        if ($result) {
            Alert::success(trans('SECLANG::auth.account_updated'))->flash();
        } else {
            Alert::error(trans('AMER::crud.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('SECLANG::auth.account_updated'))->flash();
        } else {
            Alert::error(trans('AMER::crud.error_saving'))->flash();
        }

        // If the AuthenticateSessions middleware is being used
        // the password hash should be updated, in order to
        // invalidate all authenticated browser sessions
        // except for the current one.
        $this->guard()->logoutOtherDevices($request->new_password);

        // If the AuthenticateSession middleware was used until now,
        // also update the password hash in the session so that the
        // admin does not get logged out in the next request.
        if ($request->session()->has('password_hash_'.Amer_guard_name())) {
            $request->session()->put([
                'password_hash_'.Amer_guard_name() => $user->getAuthPassword(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return amer_auth();
    }
}
