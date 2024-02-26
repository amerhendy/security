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
class User extends Authenticatable
{
    use HasApiTokens,HasFactory,AmerTrait, Notifiable;
    use HasRoles; // <------ and this
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable=['name','email','first_name','last_name','title','mobile'];

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
    $AllPermissions=amer_user()->getAllPermissions()->toArray();
    foreach($AllPermissions as $a=>$b) {
        if($text == $b['name']){
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
