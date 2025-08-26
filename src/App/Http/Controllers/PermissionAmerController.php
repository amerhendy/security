<?php
namespace Amerhendy\Security\App\Http\Controllers;
use Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use Amerhendy\Security\App\Http\Requests\PermissionStoreAmerRequest as StoreRequest;
use Amerhendy\Security\App\Http\Requests\PermissionUpdateAmerRequest as UpdateRequest;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Request;
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
        $this->role_model = $role_model = config('Amer.Permissionmanager.models.role');
        $this->permission_model = $permission_model = config('Amer.Permissionmanager.models.permission');
        $this->Amer->setModel($permission_model);
        $this->Amer->setEntityNameStrings(trans('SECLANG::permissionmanager.permission_singular'), trans('SECLANG::permissionmanager.permission_plural'));
        $this->Amer->setRoute(Sec_url('permission'));
        /*
        if(Amer_user()->can('permission-show') == 0){$this->Amer->denyAccess('show');}
        if(Amer_user()->can('permission-add') == 0){$this->Amer->denyAccess('create');}
        if(Amer_user()->can('permission-update') == 0){$this->Amer->denyAccess('update');}
        if(Amer_user()->can('permission-trash') == 1){$this->Amer->addButtonFromView('line', 'softdelete', 'softdelete', 'beginning');}
        if(Amer_user()->can('permission-delete') == 0){$this->Amer->denyAccess('delete');}
        */
        // deny access according to configuration file
        //dd(config('Amer.Permissionmanager'));
        if (config('Amer.Permissionmanager.allow_permission_create') == false) {
            $this->Amer->denyAccess('create');
        }
        if (config('Amer.Permissionmanager.allow_permission_update') == false) {
            $this->Amer->denyAccess('update');
        }
        if (config('Amer.Permissionmanager.allow_permission_delete') == false) {
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
    public static function newPermissions(Request $request){
        //dd($request->all());
        $model=config('Amer.Permissionmanager.models.permission');
        //$modds=$model::get(['id','name','ArName'])->toArray();
        $accesslist=['update','list', 'show','trash','bulkClone','reorder','delete','create','clone','BulkDelete','print'];
        $list=[];$link=[
            ['class'=>'Cities','arabic'=>__('AMER::Cities.plural')],
            ['class'=>'Governorates','arabic'=>__('AMER::Governorates.plural')],
            ['class'=>'Menu','arabic'=>__('AMER::Menu.plural')],
            ['class'=>'ShortUrls','arabic'=>trans('الروابط القصيرة')],
            ['class'=>'offics_employersmamorias','arabic'=>trans('ODLANG::MyOffice.offics_employersmamorias.offics_employersmamorias')],
            ['class'=>'office_cars','arabic'=>trans('ODLANG::MyOffice.cars.cars')],
            ['class'=>'office_chairmen','arabic'=>trans('ODLANG::MyOffice.office_chairmen.office_chairmen')],
            ['class'=>'office_degrees','arabic'=>trans('ODLANG::MyOffice.office_degrees.office_degrees')],
            ['class'=>'office_drivers','arabic'=>trans('ODLANG::MyOffice.office_drivers.office_drivers')],
            ['class'=>'office_employers','arabic'=>trans('ODLANG::MyOffice.office_employers.office_employers')],
            ['class'=>'offics_chairmenmamorias','arabic'=>trans('ODLANG::MyOffice.offics_chairmenmamorias.offics_chairmenmamorias')],
            ['class'=>'offics_driversmamorias','arabic'=>trans('ODLANG::MyOffice.offics_driversmamorias.offics_driversmamorias')],
            ['class'=>'Employers_trainings','arabic'=>trans('EMPLANG::Employers_trainings.plural')],
            ['class'=>'Employers_CareerPathes','arabic'=>trans('EMPLANG::Employers_CareerPathes.plural')],
            ['class'=>'Employers_CareerPathFiles','arabic'=>trans('EMPLANG::Employers_CareerPathFiles.plural')],
            ['class'=>'Regulations','arabic'=>trans('EMPLANG::Regulations.plural')],
            ['class'=>'Regulations_Topics','arabic'=>trans('EMPLANG::Regulations_Topics.plural')],
            ['class'=>'Regulations_Articles','arabic'=>trans('EMPLANG::Regulations_Articles.plural')],
            ['class'=>'OrgStru_Areas','arabic'=>trans('EMPLANG::OrgStru_Areas.plural')],
            ['class'=>'OrgStru_Mahatas','arabic'=>trans('EMPLANG::OrgStru_Mahatas.plural')],
            ['class'=>'OrgStru_Sections','arabic'=>trans('EMPLANG::OrgStru_Sections.plural')],
            ['class'=>'OrgStru_Types','arabic'=>trans('EMPLANG::OrgStru_Types.plural')],
            ['class'=>'Employers','arabic'=>trans('EMPLANG::Employers.plural')],
            ['class'=>'Mosama_Competencies','arabic'=>trans('EMPLANG::Mosama_Competencies.plural')],
            ['class'=>'Mosama_Connections','arabic'=>trans('EMPLANG::Mosama_Connections.plural')],
            ['class'=>'Mosama_Degrees','arabic'=>trans('EMPLANG::Mosama_Degrees.plural')],
            ['class'=>'Mosama_Experiences','arabic'=>trans('EMPLANG::Mosama_Experiences.plural')],
            ['class'=>'Mosama_Goals','arabic'=>trans('EMPLANG::Mosama_Goals.plural')],
            ['class'=>'Mosama_Groups','arabic'=>trans('EMPLANG::Mosama_Groups.plural')],
            ['class'=>'Mosama_OrgStruses','arabic'=>trans('EMPLANG::Mosama_OrgStruses.plural')],
            ['class'=>'Mosama_Managers','arabic'=>trans('EMPLANG::Mosama_Managers.plural')],
            ['class'=>'Mosama_JobTitles','arabic'=>trans('EMPLANG::Mosama_JobTitles.plural')],
            ['class'=>'Mosama_JobNames','arabic'=>trans('EMPLANG::Mosama_JobNames.plural')],
            ['class'=>'Mosama_Skills','arabic'=>trans('EMPLANG::Mosama_Skills.plural')],
            ['class'=>'Mosama_Tasks','arabic'=>trans('EMPLANG::Mosama_Tasks.plural')],
            ['class'=>'Employment_StaticPages','arabic'=>trans('JOBLANG::Employment_StaticPages.plural')],
            ['class'=>'Employment_Ama','arabic'=>trans('JOBLANG::Employment_Ama.plural')],
            ['class'=>'Employment_Army','arabic'=>trans('JOBLANG::Employment_Army.plural')],
            ['class'=>'Employment_Committee','arabic'=>trans('JOBLANG::Employment_Committee.plural')],
            ['class'=>'Employment_DinamicPages','arabic'=>trans('JOBLANG::Employment_DinamicPages.plural')],
            ['class'=>'Employment_Drivers','arabic'=>trans('JOBLANG::Employment_Drivers.plural')],
            ['class'=>'Employment_Health','arabic'=>trans('JOBLANG::Employment_Health.plural')],
            ['class'=>'Employment_IncludedFiles','arabic'=>trans('JOBLANG::Employment_IncludedFiles.plural')],
            ['class'=>'Employment_Instructions','arabic'=>trans('JOBLANG::Employment_Instructions.plural')],
            ['class'=>'Employment_Jobs','arabic'=>trans('JOBLANG::Employment_Jobs.plural')],
            ['class'=>'Employment_LagnaPersons','arabic'=>trans('JOBLANG::Employment_LagnaPersons.plural')],
            ['class'=>'Employment_MaritalStatus','arabic'=>trans('JOBLANG::Employment_MaritalStatus.plural')],
            ['class'=>'Employment_Qualifications','arabic'=>trans('JOBLANG::Employment_Qualifications.plural')],
            ['class'=>'Employment_Stages','arabic'=>trans('JOBLANG::Employment_Stages.plural')],
            ['class'=>'Employment_StartAnnonces','arabic'=>trans('JOBLANG::Employment_StartAnnonces.plural')],
            ['class'=>'Pages','arabic'=>trans('PAGELANG::Pages.plural')],
        ];
        foreach ($link as $k => $v) {
            foreach ($accesslist as $l => $m) {
                $tr=__('AMER::Base.accesslist');
                $list[]=['id'=>\Str::uuid()->toString(),'name'=>$v['class'].'-'.$m,'ar_name'=>$v['arabic'].'-'.$tr[$m],'guard_name'=>'Amer','created_at'=>now()];
            }
        }
        $model::insert($list);
        return(\Arr::query($link));
        foreach ($accesslist as $key => $value) {
            foreach ($request->input('class') as $k => $v) {
                $list[]=$request->input('class').'-'.$v;
            }

        }
        dd($list);
        dd($model::where('name','LIKE',$request->input('class').'%')->get());

    }
}
