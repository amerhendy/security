<?php
namespace Amerhendy\Security\App\Http\Controllers;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Security\App\Models\Amer as User;
use \Amerhendy\Security\App\Models\Teams;
use \Amerhendy\Security\App\Models\Role;
use Amerhendy\Security\App\Http\Requests\PermissionStoreAmerRequest as StoreRequest;
use Amerhendy\Security\App\Http\Requests\PermissionUpdateAmerRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User as Model;
use App\Models\Role as RoleModel;
use Illuminate\Support\Facades\Validator;
class UserAmerController extends AmerController
{
    public static $error,$returnType;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation { store as traitStore; }
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation { update as traitUpdate; }
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\FetchOperation;

    public function setup()
    {
        $this->Amer->setModel(User::class);
        $this->Amer->setEntityNameStrings(trans('SECLANG::permissionmanager.user'), trans('SECLANG::permissionmanager.users'));
//dd(Sec_url('user'));
        $this->Amer->setRoute(Sec_url('user'));
        //$sort=Amer_user()->getsort();
        $this->Amer->addButton('line', 'btnUsersAddRole','view', 'SEC::admin.btn-users-AddRole', 'beginning');
        $this->Amer->addButton('line', 'btnUsersAddPermission','view', 'SEC::admin.btn-users-AddPermission', 'beginning');
        ////////////////////////
        /*if($sort){
            $this->Amer->addClause('whereHas','roles',function($query) use($sort){
                $query->where('sort','>=',$sort);
            });
        }*/
    }

    public function setupListOperation()
    {
        \Amerhendy\Amer\App\Helpers\Widget::add()->type('script')->stack('after_scripts')->content(url('js/Security/users.js'));
        $this->Amer->addColumns([
            [
                'name'  => 'name',
                'label' => trans('SECLANG::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('SECLANG::permissionmanager.email'),
                'type'  => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('SECLANG::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'ArName', // foreign key attribute that is shown to user
                'model'     => config('Permissionmanager.models.role'), // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('SECLANG::permissionmanager.extra_permissions'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'permissions', // the method that defines the relationship in your Model
                'entity'    => 'permissions', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('Permission.models.permission'), // foreign key model
            ],
        ]);
        // Role Filter
        $this->Amer->addFilter(
            [
                'name'  => 'role',
                'type'  => 'dropdown',
                'label' => trans('SECLANG::permissionmanager.role'),
            ],
            config('Amer.Permissionmanager.models.role')::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->Amer->addClause('whereHas', 'roles', function ($query) use ($value) {
                    $query->where('role_id', '=', $value);
                });
            }
        );

        // Extra Permission Filter
        $this->Amer->addFilter(
            [
                'name'  => 'permissions',
                'type'  => 'select2',
                'label' => trans('SECLANG::permissionmanager.extra_permissions'),
            ],
            config('permission.models.permission')::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->Amer->addClause('whereHas', 'permissions', function ($query) use ($value) {
                    $query->where('permission_id', '=', $value);
                });
            }
        );
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->Amer->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->Amer->addFields([
            [
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'title',
                'label' => trans('SECLANG::user.prefix'),
                'type'  => 'select2_from_array',
                'options'=>trans('SECLANG::user.prefixs')
            ],[
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'first_name',
                'label' => trans('SECLANG::permissionmanager.first_name'),
                'type'  => 'text',
            ],[
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'last_name',
                'label' => trans('SECLANG::permissionmanager.last_name'),
                'type'  => 'text',
            ],
            [
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'name',
                'label' => trans('SECLANG::user.name'),
                'type'  => 'text',
            ],[
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'gender',
                'label' => trans('SECLANG::user.gender'),
                'type'  => 'select2_from_array',
                'options'=>trans('SECLANG::user.genders')
            ],[
                'tab'   =>trans('SECLANG::user.personalInfo'),
                'name'  => 'birthdate',
                'label' => trans('SECLANG::user.birthdate'),
                'type'  => 'date',
            ],[
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'  => 'username',
                'label' => trans('SECLANG::user.username'),
                'type'  => 'text',
            ],
            [
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'  => 'email',
                'label' => trans('SECLANG::permissionmanager.email'),
                'type'  => 'email',
            ],[
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'  => 'mobile',
                'label' => trans('SECLANG::permissionmanager.mobile'),
                'type'  => 'text',
            ],[
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'  => 'status',
                'label' => trans('SECLANG::user.userstatus'),
                'type'  => 'radio',
                'options'=>['active'=>trans('SECLANG::user.active'),'inactive'=>trans('SECLANG::user.notactive')],
                'inline'    =>true,
            ],[
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'  => 'note',
                'label' => trans('SECLANG::user.note'),
                'type'  => 'textarea',
            ],
            [
                'tab'   =>trans('SECLANG::user.enteryInfo'),
                'name'=>'current_team_id',
                'type'=>'select2',
                'entity'=>'Team',
                'model'=>'Amerhendy\Security\App\Models\Teams',
                'attribute'=>'name',
                'label'=>trans('SECLANG::Teams.Teams'),
            ],
        ]);
        $this->Amer->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->Amer->model->fillable[]='password';
        $this->Amer->setRequest($this->Amer->validateRequest());
        $this->Amer->setRequest($this->handlePasswordInput($this->Amer->getRequest()));
        $this->Amer->unsetValidation(); // validation has already been run
        return $this->traitStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->Amer->setRequest($this->Amer->validateRequest());
        $role=Role::where('id',$this->Amer->getRequest()->request->get('roles_show'))->get('team_id');
        //dd(Teams::get('team_id'));
        //dd($this->Amer->getRequest()->request->get('roles_show'),$this->Amer->getRequest()->request);
        //$this->Amer->getRequest()->request->add(['team_id'=> $role]);
        $this->Amer->unsetValidation(); // validation has already been run
        return $this->traitUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    protected function addUserFields()
    {
        $Model=config('Amer.Permissionmanager.models.role');
        $routes=$this->Amer->routelist;
        $this->Amer->addFields([
            [
                'name'  => 'title',
                'label' => trans('SECLANG::user.prefix'),
                'type'  => 'select2_from_array',
                'options'=>trans('SECLANG::user.prefixs')
            ],[
                'name'  => 'first_name',
                'label' => trans('SECLANG::permissionmanager.first_name'),
                'type'  => 'text',
            ],[
                'name'  => 'last_name',
                'label' => trans('SECLANG::permissionmanager.last_name'),
                'type'  => 'text',
            ],[
                'name'  => 'mobile',
                'label' => trans('SECLANG::permissionmanager.mobile'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('SECLANG::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'name',
                'label' => trans('SECLANG::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'password',
                'label' => trans('SECLANG::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('SECLANG::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ]
        ]);
    }
    public static function SutupErros(){
        self::$error=new \stdClass();
        self::$error->number=401;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
        if(request()->ajax()){self::$returnType='json';}else{self::$returnType='html';}
    }
    /*
    public function fetchRoles()
    {
        self::SutupErros();
        $request=request();
        $validator=Validator::make($request->all(),[
            'type'=>'required',
            'UserId'=>'required',
        ]);
        if($validator->fails()){
            self::$error->message=$validator->messages();self::$error->line=__LINE__;
                return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $id=$request->input('UserId');
        $roleUsers=\DB::table('model_has_roles')->where(['model_id'=>$id])->get('role_id');
        $selectedRoles=[];
        if(count($roleUsers)){
            foreach ($roleUsers as $key => $value) {
                $selectedRoles[]=$value->role_id;
            }
        }
        $role_model = config('Amer.Permissionmanager.models.role');
        $role_model = $role_model::get();
        $allRoles=[];
        foreach($role_model as $key => $value) {
            $allRoles[$key]['id'] = $value->id;
            if(is_null($value->ArName)){$name=$value->name;}else{$name=$value->ArName;}
            $allRoles[$key]['name'] = $name;
        }
        return [$selectedRoles,$allRoles];
    }
    public function fetchAddRoles()
    {
        self::SutupErros();
        $request=request();
        $validator=Validator::make($request->all(),[
            'id'=>'required',
            'UserId'=>'required',
            'action'=>'required',
        ]);
        if($validator->fails()){
            self::$error->message=$validator->messages();self::$error->line=__LINE__;
                return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $id=$request->input('id');
        $UserId=$request->input('UserId');
        $action=$request->input('action');
        $user=\DB::table('model_has_roles')->where(['role_id'=>$id,'model_id'=>$UserId])->first();
        if(!$user){
                \DB::table('model_has_roles')->insert(['role_id' => $id,'model_id' => $UserId,'model_type'=>'Amerhendy\Security\App\Models\User']);
                return ['minus'];
        }else{
                \DB::table('model_has_roles')->where(['role_id'=>$id,'model_id'=>$UserId])->delete();
            return ['plus'];
        }
    }
    public function fetchPerms()
    {
        self::SutupErros();
        $request=request();
        $validator=Validator::make($request->all(),[
            'type'=>'required',
            'UserId'=>'required',
        ]);
        if($validator->fails()){
            self::$error->message=$validator->messages();self::$error->line=__LINE__;
                return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $UserId=$request->input('UserId');
        $model_has_roles=\DB::table('model_has_roles')->where(['model_id'=>$UserId])->get('role_id');
        $selectedRoles=[];
        if(count($model_has_roles)){
            foreach ($model_has_roles as $key => $value) {
                $selectedRoles[]=$value->role_id;
            }
        }
        $role_has_permissions=\DB::table('role_has_permissions')->whereIn('role_id',$selectedRoles)->get('permission_id');
        $selectedPermissions=[];
        if(count($role_has_permissions)){
            foreach ($role_has_permissions as $key => $value) {
                $selectedPermissions[]=$value->permission_id;
            }
        }
        ///////get user permissions/////
        $model_has_permissions=\DB::table('model_has_permissions')->where('model_id',$UserId)->get('permission_id');
        if(count($model_has_permissions)){
            foreach ($model_has_permissions as $key => $value) {
                $selectedPermissions[]=$value->permission_id;
            }
        }
        $selectedPermissions=array_unique($selectedPermissions);
        $permission_model = config('Amer.Permissionmanager.models.permission');
        $permission_model = $permission_model::get();
        $allPermissions=[];
        foreach($permission_model as $key => $value) {
            $allPermissions[$key]['id'] = $value->id;
            if(is_null($value->ArName)){$name=$value->name;}else{$name=$value->ArName;}
            $allPermissions[$key]['name'] = $name;
        }
        return [$selectedPermissions,$allPermissions];
    }
    public function fetchAddPers()
    {
        self::SutupErros();
        $request=request();
        $validator=Validator::make($request->all(),[
            'id'=>'required',
            'UserId'=>'required',
            'action'=>'required',
        ]);
        if($validator->fails()){
            self::$error->message=$validator->messages();self::$error->line=__LINE__;
                return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $id=$request->input('id');
        $UserId=$request->input('UserId');
        $action=$request->input('action');
        $user=\DB::table('model_has_permissions')->where(['permission_id'=>$id,'model_id'=>$UserId])->first();
        if(!$user){
                \DB::table('model_has_permissions')->insert(['permission_id' => $id,'model_id' => $UserId,'model_type'=>'Amerhendy\Security\App\Models\User']);
                return ['minus'];
        }else{
                \DB::table('model_has_permissions')->where(['permission_id'=>$id,'model_id'=>$UserId])->delete();
            return ['plus'];
        }
    }
    */
}
