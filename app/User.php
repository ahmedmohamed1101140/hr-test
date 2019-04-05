<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'initials',
        'last_name',
        'display_name',
        'description',
        'office',
        'telephone_number',
        'external_email',
        'user_name','user_id',
        'title',
        'memberof',
        'department',
        'status',
        'is_admin',
        'contract_start','contract_end',
        'gender', 'manager',
        'resort_id', 'department_id' ,
        'group_id', 'email', 'password',
    ];

    protected $memberOf = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    public function resort(){
        return $this->belongsTo(Resort::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }*/

    public function resort(){
        return $this->belongsTo(Resort::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function checkPermission($permissionName){
        foreach($this->permissions() as $permission){
            if($permission->url == $permissionName){
                return true;
            }
        }
        return false;
    }


    public function permissions(){
        //get usre group
        //get all user $roles
        //get all roles $permissions
        //filter all permissions to get the unique permissions

        $roles = UserData::select('role_id')->where('user_id',$this->id)->get();

        $permissions = RolePermissions::whereIn('role_id',$roles)->distinct()->get(['permission_id'])->toArray();
        $permissions = Permission::select('url','description')->whereIn('id',$permissions)->get();
        return $permissions;

        //return array of unique user permissions
    }
    //$user->group()->roles()->permissiosn();

    public function softwares(){
        return $this->hasMany(ComponentRequest::class)->where('component_type','Software');
    }

    public function hardwares(){
        return $this->hasMany(ComponentRequest::class)->where('component_type','Hardware');
    }

    public function files(){
        return $this->hasMany(ComponentRequest::class)->where('component_type','Files');
    }

    /*public function delete(){
        //delete requests
        //delete posts
        //delete DOMComme
        //delete imagearc//
        //delete the user
        //$this->destroy();
    }*/

}
