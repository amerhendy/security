<?php
namespace Amerhendy\Security\App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Amerhendy\Security\App\Models\Role;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Teams extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasUuids;
    protected $table ="teams";
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => [],
            ],
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function remove_force($id){
        $data=self::withTrashed()->find($id);
            if(!$data){return 0;}
        return $data::forceDelete();
        return 1;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function roles(){
        return $this->belongsTo(Role::class,'team_id','id');
    }
    public function User()
    {
        return $this->belongsTo('Amerhendy\Security\App\Models\User', 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany('Amerhendy\Security\App\Models\User', 'team_user','team_id', 'user_id');
    }

    public function team_user()
    {
        return $this->belongsToMany('Amerhendy\Security\App\Models\User', 'Teams','id','id');
    }
}
