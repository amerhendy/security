<?php
namespace Amerhendy\Security\App\Http\Controllers;
use Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use Amerhendy\Security\App\Http\Requests\RoleStoreAmerRequest as StoreRequest;
use Amerhendy\Security\App\Http\Requests\RoleUpdateAmerRequest as UpdateRequest;
use Illuminate\Http\Request;
// VALIDATION

class RoleAmerController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\FetchOperation;

    public function setup()
    {
        $this->role_model = $role_model = config('Amer.Permissionmanager.models.role');
        $this->permission_model = $permission_model = config('Amer.Permissionmanager.models.permission');
        $this->Amer->setModel($role_model);
        $this->Amer->setEntityNameStrings(trans('SECLANG::permissionmanager.role'), trans('SECLANG::permissionmanager.roles'));
        $this->Amer->addButton('line', 'btnRolesAddpermission','view', 'SEC::.admin.btn-roles-addPermession', 'beginning');
        $this->Amer->addButton('line', 'btnaddRolesUsers','view', 'SEC::.admin.btn-roles-addRolesUsers', 'beginning');
        $this->Amer->setRoute(Sec_url('role'));
        // deny access according to configuration file
        if (config('Amer.Permissionmanager.allow_role_create') == false) {
            $this->Amer->denyAccess('create');
        }
        if (config('Amer.Permissionmanager.allow_role_update') == false) {
            $this->Amer->denyAccess('update');
        }
        if (config('Amer.Permissionmanager.allow_role_delete') == false) {
            $this->Amer->denyAccess('delete');
        }
    }

    public function setupListOperation()
    {
        /**
         * Show a column for the name of the role.
         */
        $this->Amer->addColumn([
            'name'  => 'name',
            'label' => trans('SECLANG::permissionmanager.name'),
            'type'  => 'text',
        ]);
        $this->Amer->addColumn([
                'name'  => 'ar_name',
                'label' => trans('AMER::Base.ArName'),
                'type'  => 'text',
            ]);
            \Amerhendy\Amer\App\Helpers\Widget::add()->type('script')->stack('after_scripts')->content(url('js/Security/roles.js'));

        /*$this->Amer->query->withCount('users');
        $this->Amer->addColumn([
            'label'     => trans('SECLANG::permissionmanager.users'),
            'type'      => 'text',
            'name'      => 'users_count',
            'wrapper'   => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return Amerurl('user?role='.$entry->getKey());
                },
            ],
            'suffix'    => ' users',
        ]);*/
        /**
         * In case multiple guards are used, show a column for the guard.
         */

        if (config('permissionmanager.multiple_guards')) {
            $this->Amer->addColumn([
                'name'  => 'guard_name',
                'label' => trans('SECLANG::permissionmanager.guard_type'),
                'type'  => 'text',
            ]);
        }

        /**
         * Show the exact permissions that role has.
         */
        /*
        $this->Amer->addColumn([
            // n-n relationship (with pivot table)
            'label'     => ucfirst(trans('SECLANG::permissionmanager.permission_plural')),
            'type'      => 'select_multiple',
            'name'      => 'permissions', // the method that defines the relationship in your Model
            'entity'    => 'permissions', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => $this->permission_model, // foreign key model
            'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
        ]);
        */
    }

    public function setupCreateOperation()
    {
        $this->addFields();
        $this->Amer->setValidation(StoreRequest::class);

        //otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');
    }

    public function setupUpdateOperation()
    {
        $this->addFields();
        $this->Amer->setValidation(UpdateRequest::class);

        //otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');
    }

    private function addFields()
    {
        $this->Amer->addField([
            'name'  => 'name',
            'label' => trans('SECLANG::permissionmanager.name'),
            'type'  => 'text',
        ]);
        $this->Amer->addField([
                'name'  => 'ar_name',
                'label' => trans('AMER::Base.ArName'),
                'type'  => 'text',
            ]);
        if (config('permissionmanager.multiple_guards')) {
            $this->Amer->addField([
                'name'    => 'guard_name',
                'label'   => trans('SECLANG::permissionmanager.guard_type'),
                'type'    => 'select_from_array',
                'options' => $this->getGuardTypes(),
            ]);
        }
        $this->Amer->addField([
            'label'     => ucfirst(trans('SECLANG::permissionmanager.permission_plural')),
            'type'      => 'SEC::Admin.checklistpermissions',
            'name'      => 'permissions',
            'entity'    => 'permissions',
            'attribute' => ['ar_name','name'],
            'model'     => $this->permission_model,
            'pivot'     => true,
        ]);
    }

    /*
     * Get an array list of all available guard types
     * that have been defined in app/config/auth.php
     *
     * @return array
     **/
    private function getGuardTypes()
    {
        $guards = config('auth.guards');

        $returnable = [];
        foreach ($guards as $key => $details) {
            $returnable[$key] = $key;
        }
        return $returnable;
    }
    public function fetchPerms(Request $request)
    {
        if($request->has('RoleId')){$id=$request->input('RoleId');}else{return [];}
        $rolePerms=\DB::table(config('permission.table_names.role_has_permissions') ?? 'role_has_permissions')
        ->where(['role_id'=>$id])
        ->get('permission_id');
        $selectedPermissions=[];
        if(count($rolePerms)){
            foreach ($rolePerms as $key => $value) {
                $selectedPermissions[]=$value->permission_id;
            }
        }
        $permission_model = config('Amer.Permissionmanager.models.permission');
        $permission_model = $permission_model::get();
        $allperms=[];
        foreach($permission_model as $key => $value) {
            $allperms[$key]['id'] = $value->id;
            if(is_null($value->ar_name)){$name=$value->name;}else{$name=$value->ar_name;}
            $allperms[$key]['name'] = $name;
        }
        return [$selectedPermissions,$allperms];
    }
    public function fetchUsers(Request $request)
    {
        if(!$request->has('RoleId')){return [];}
        if($request->input("RoleId") == ''){return [];}
        $id=$request->input("RoleId");
        $roleUsers=\DB::table('model_has_roles')->where(['role_id'=>$id])->get('model_id');
        $selectedusers=[];
        if(count($roleUsers)){
            foreach ($roleUsers as $key => $value) {
                $selectedusers[]=$value->model_id;
            }
        }
        $Users_model = \Amerhendy\Security\App\Models\User::class;
        $Users_model = $Users_model::get();
        $allUsers=[];
        foreach($Users_model as $key => $value) {
            $allUsers[$key]['id'] = $value->id;
            $allUsers[$key]['name'] = $value->name;
            $allUsers[$key]['email'] = $value->email;
        }
        return [$selectedusers,$allUsers];
    }
    public function fetchAddPerms(Request $request)
    {
        if(!$request->has('RoleId')){return [];}else{$RoleId=$request->input('RoleId');}
        if(!$request->has('id')){return [];}else{$id=$request->input('id');}
        if(!$request->has('action')){return null;}else{$action=$request->input('action');}
        $RoleId=$request->input("RoleId");
        $id=$request->input("id");
        $action=$request->input("action");
        $sert=[];
        if(!is_array($id)){$id=[$id];}
        $user=\DB::table(config('permission.table_names.role_has_permissions') ?? 'role_has_permissions')->where('role_id',$RoleId)->whereIn('permission_id',$id)->get();
        if($action === 'minus'){
            $user=\DB::table(config('permission.table_names.role_has_permissions') ?? 'role_has_permissions')->where('role_id',$RoleId)->whereIn('permission_id',$id)->delete();
            return $id;
        }elseif($action === 'plus'){
            $vc=[];
            foreach ($user as $key => $value) {
                if(in_array($value->permission_id,$id)){
                    $vc[]=\AmerHelper::getkeyByValue($id,$value->permission_id);
                }
            }
            foreach ($vc as $v) {
                unset($id[$v]);
            }
            if(empty($id)){return [];}
            foreach ($id as $key => $value) {
                \DB::table(config('permission.table_names.role_has_permissions') ?? 'role_has_permissions')->insert(['role_id' => $RoleId,'permission_id' => $value]);
                $sert[]=$value;
            }
            return $sert;
        }
    }
    public function fetchAddUsers(Request $request)
    {

        if(!$request->has('RoleId')){return [];}
        if(!$request->has('id')){return [];}
        if(!$request->has('action')){return [];}
        $RoleId=$request->input("RoleId");
        $id=$request->input("id");
        $action=$request->input("action");
        $sert=[];
        if(!is_array($id)){$id=[$id];}
        $user=\DB::table('model_has_roles')->where('role_id',$RoleId)->whereIn('model_id',$id)->get();
        if($action === 'minus'){
            $user=\DB::table('model_has_roles')->where('role_id',$RoleId)->whereIn('model_id',$id)->delete();
            return $id;
            //remove
        }elseif($action === 'plus'){
            $vc=[];
            foreach ($user as $key => $value) {
                if(in_array($value->model_id,$id)){
                    $vc[]=\AmerHelper::getkeyByValue($id,$value->model_id);
                }
            }
            foreach ($vc as $v) {
                unset($id[$v]);
            }
            if(empty($id)){return [];}
            foreach ($id as $key => $value) {
                $user=\DB::table('model_has_roles')->insert(['role_id' => $RoleId,'model_id' => $value,'model_type'=>'Amerhendy\Security\App\Models\User']);
                $sert[]=$value;
            }
            return $sert;
        }
    }
}
