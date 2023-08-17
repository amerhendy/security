<?php
namespace Amerhendy\Security\App\Http\Controllers;
use Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use Amerhendy\Security\App\Http\Requests\PermissionStoreAmerRequest as StoreRequest;
use Amerhendy\Security\App\Http\Requests\PermissionUpdateAmerRequest as UpdateRequest;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Role;
use App\Models\Permission;
// VALIDATION

class PermissionAmerController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
        use HasRoles;
    public function setup()
    {
        $this->role_model = $role_model = config('permissionmanager.models.role');
        $this->permission_model = $permission_model = config('permissionmanager.models.permission');
        $this->Amer->setModel($permission_model);
        $this->Amer->setEntityNameStrings(trans('SECLANG::permissionmanager.permission_singular'), trans('SECLANG::permissionmanager.permission_plural'));
        $this->Amer->setRoute(Amerurl('permission'));
        /*
        if(Amer_user()->can('permission-show') == 0){$this->Amer->denyAccess('show');}
        if(Amer_user()->can('permission-add') == 0){$this->Amer->denyAccess('create');}
        if(Amer_user()->can('permission-update') == 0){$this->Amer->denyAccess('update');}
        if(Amer_user()->can('permission-trash') == 1){$this->Amer->addButtonFromView('line', 'softdelete', 'softdelete', 'beginning');}
        if(Amer_user()->can('permission-delete') == 0){$this->Amer->denyAccess('delete');}
        */
        // deny access according to configuration file
        if (config('permissionmanager.allow_permission_create') == false) {
            $this->Amer->denyAccess('create');
        }
        if (config('permissionmanager.allow_permission_update') == false) {
            $this->Amer->denyAccess('update');
        }
        if (config('permissionmanager.allow_permission_delete') == false) {
            $this->Amer->denyAccess('delete');
        }
    }

    public function setupListOperation()
    {
        $this->Amer->addColumn([
            'name'  => 'name',
            'label' => trans('SECLANG::permissionmanager.name'),
            'type'  => 'text',
        ]);
        $this->Amer->addColumn([
            'name'  => 'ArName',
            'label' => trans('AMER::Base.ArName'),
            'type'  => 'text',
        ]);
        if (config('permissionmanager.multiple_guards')) {
            $this->Amer->addColumn([
                'name'  => 'guard_name',
                'label' => trans('SECLANG::permissionmanager.guard_type'),
                'type'  => 'text',
            ]);
        }
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
                'name'  => 'ArName',
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
}
