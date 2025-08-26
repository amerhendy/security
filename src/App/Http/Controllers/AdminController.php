<?php

namespace Amerhendy\Security\App\Http\Controllers;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(amer_middleware());
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {

        $this->data['title'] = trans('SECLANG::auth.dashboard'); // set the page title
        $this->data['breadcrumbs'] = [
            trans('SECLANG::auth.admin')     => Amerurl('dashboard'),
            trans('SECLANG::auth.dashboard') => false,
        ];
        return view('SEC::Admin.dashboard', $this->data);
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(Amerurl('dashboard'));
    }
}
