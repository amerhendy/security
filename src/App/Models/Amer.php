<?php

namespace Amerhendy\Security\App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Spatie\Permission\Traits\HasPermissions;// <---------------------- and this one
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Amer extends Authenticatable
{
    use HasApiTokens,HasFactory,AmerTrait, Notifiable,HasUuids;
    use HasRoles; // <------ and this
    use HasPermissions;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $table='users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable=['name','email','first_name','last_name','title','mobile','gender','birthdate','username','note','current_team_id','status'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public $incrementing=true;
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function canper($text){
        //list rolesPers
        $UserId=amer_user()->id;
        $roleUsers=\DB::table('model_has_roles')->where(['model_id'=>$UserId])->get('role_id');
        $selectedRoles=[];
        if(count($roleUsers)){
            foreach ($roleUsers as $key => $value) {
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
        $model_has_permissions=\DB::table('model_has_permissions')->where('model_id',$UserId)->get('permission_id');
        if(count($model_has_permissions)){
            foreach ($model_has_permissions as $key => $value) {
                $selectedPermissions[]=$value->permission_id;
            }
        }
        $selectedPermissions=array_unique($selectedPermissions);
        $permission_model = config('Amer.Permissionmanager.models.permission');
        $permission_model = $permission_model::whereIn('id',$selectedPermissions)->get('name')->toArray();
        foreach ($permission_model as $key => $value) {
            if($value['name']=$text){
                return true;
            }
        }
        return false;
    }
    public function getsort(){
        if(count(amer_user()['roles'])){
            $sort=amer_user()['roles'][0]['sort'];
        }else{
            $sort=1;
        }
        return $sort;
    }
    public function Teams()
    {
        return $this->belongsToMany('Amerhendy\Security\App\Models\Teams', 'team_user','user_id','team_id');
    }
    public function Team()
    {
        return $this->belongsTo('Amerhendy\Security\App\Models\Teams', 'user_id','current_team_id');
    }

    public function team_user()
    {
        return $this->belongsToMany('Amerhendy\Security\App\Models\Teams', 'User','id','id');
    }
    public function roles()
    {
        return $this->MorphToMany(
            //getModelForGuard('Amer'),
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }
}
