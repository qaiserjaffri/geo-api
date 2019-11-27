<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email', 'password', 'mobile_phone', 'status', 'avatar', 'address', 'created_by', 'updated_by', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','auth_token', 'is_loggedIn', 'deleted_at', 'reset_key'
    ];

    
    static function findById($id){
        return User::find($id);
    }
        
    public static function saveUser($user){
        // dd($user);
        return User::create($user);
    }

    public static function filter($name = Null, $email = Null, $role_id = Null, $per_page = Null, $pagination = Null){

        
        $users = User::where('status', 1);
        
        if(!is_null($name)){
            $users->where('name', 'like', '%' . $name . '%');
        }

        //if email is not null
        if(!is_null($email)){
            $users = $users->where('email', 'like', '%' . $email . '%');
        }


        $users = $users->orderby('users.id', 'desc');

        if ($pagination){

            if($per_page){
                return $users->paginate($per_page);
            }else{
                return $users->paginate(20);
                }
        }else{
            return $users->get();
             
        }
    }

    
}

