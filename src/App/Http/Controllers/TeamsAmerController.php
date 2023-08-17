<?php
namespace Amerhendy\Security\App\Http\Controllers;
use \Amerhendy\Security\App\Models\Teams as Teams;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Security\App\Http\Requests\TeamsRequest as TeamsRequest;

class TeamsAmerController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation  {store as traitStore;}
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ShowOperation;
    public function setup()
    {
        AMER::setModel(Teams::class);
        AMER::setRoute(config('amer.route_prefix') . '/Teams');
        AMER::setEntityNameStrings(trans('SECLANG::Teams.singular'), trans('SECLANG::Teams.plural'));
        $this->Amer->setTitle(trans('SECLANG::Teams.create'), 'create');
        $this->Amer->setHeading(trans('SECLANG::Teams.create'), 'create');
        $this->Amer->setSubheading(trans('SECLANG::Teams.create'), 'create');
        $this->Amer->setTitle(trans('SECLANG::Teams.edit'), 'edit');
        $this->Amer->setHeading(trans('SECLANG::Teams.edit'), 'edit');
        $this->Amer->setSubheading(trans('SECLANG::Teams.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        AMER::allowAccess ('details_row');
        AMER::allowAccess ('create');
        /*
        if(amer_user()->can('Teams-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Teams-trash') == 1){$this->Amer->addButtonFromView('line', 'softdelete', 'softdelete', 'beginning');}
        if(amer_user()->can('Teams-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Teams-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Teams-show') == 0){$this->Amer->denyAccess('show');}
        */
    }

    protected function setupListOperation(){
        //AMER::setFromDb();
        AMER::addColumns([
            [
                'name'=>'user_id',
                'type'=>'select',
                'entity'=>'User',
                'model'=>'Amerhendy\Security\App\Models\User',
                'attribute'=>'name',
                'label'=>trans('SECLANG::Teams.User'),
            ],
            [
                'name'=>'name',
                'type'=>'text',
                'label'=>trans('SECLANG::Teams.teamname'),
            ],
            [
                'name'=>'personal_team',
                'type'=>'boolean',
                'label'=>trans('SECLANG::Teams.personal_team'),
            ]
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(TeamsRequest::class);
        //AMER::setFromDb();
        AMER::addFields([
            [
                'name'=>'user_id',
                'type'=>'select2',
                'entity'=>'User',
                'model'=>'Amerhendy\Security\App\Models\User',
                'attribute'=>'name',
                'label'=>trans('SECLANG::Teams.User'),
            ],
            [
                'name'=>'users',
                'type'=>'select2_multiple',
                'multiple'=>'multiple',
                'select_all'=>'select_all',
                'entity'=>'users',
                'model'=>'Amerhendy\Security\App\Models\User',
                'attribute'=>'name',
                'label'=>trans('SECLANG::Teams.User'),
            ],
            [
                'name'=>'name',
                'type'=>'text',
                'label'=>trans('SECLANG::Teams.teamname'),
            ],
            [
                'name'=>'personal_team',
                'type'=>'boolean',
                'label'=>trans('SECLANG::Teams.personal_team'),
            ]
        ]);
    }
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function store(TeamsRequest $request)
    {
        $table=$this->Amer->model->getTable();
        $lsid=DB::table($table)->get()->max('id');
        $id=$lsid+1;
        $this->Amer->addField(['type' => 'hidden', 'name' => 'id', 'value'=>$id]);
        $this->Amer->getRequest()->request->add(['id'=> $id]);
        $this->Amer->setRequest($this->Amer->validateRequest());
        $this->Amer->unsetValidation();
        return $this->traitStore();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=model::remove_force($id);
        return $data;
    }
}