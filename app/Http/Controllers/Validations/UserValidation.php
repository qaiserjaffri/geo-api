<?php

namespace App\Http\Controllers\Validations;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserValidation extends Controller
{
    

    
    public static function validateUser($data = [], $type = 'register') {
        // dd($type);
        $rules = [];

        switch ($type) {
            case 'register':
                $rules['email'] = 'required|unique:users,email,NULL,id,deleted_at,NULL';
                $rules['password'] = 'required|min:6';
                $rules['confirmedPassword'] = 'required_with:password|min:6|same:password';
                break;
            case 'update':
                $rules['user_id'] = 'required|exists:users,id,deleted_at,NULL';
                break;
            case 'list':
                $rules['pagination'] = 'required';
                $rules['per_page'] = 'required|numeric|min:10|max:100';
                $rules['page'] = 'required|numeric';
                break;
            case 'single' || 'delete':
                $rules['id'] = 'required|exists:users,id,deleted_at,NULL';
                break;
            
            default:
                //nothing to do here
                break;
        }

        $validator = Validator::make($data, $rules);

        return $validator;

    }

    public static function uploadImage($data) {

        $rules = array(
            'user_id' => 'required|numeric|exists:users,id,deleted_at,NULL',
        );


        $validator = Validator::make($data, $rules);
        return $validator;
    }

    public static function validateMedicalIdentifier($data = [], $update = false){
        $rules = array(
            'wcb_authorization' => 'nullable|required_with:wcb_date_of_issue',
            'wcb_date_of_issue' => 'nullable|required_with:wcb_authorization',
        );

        if ($update){
            // $rules['user_id'] =  'required|exists:users,id|unique:medical_identifiers,user_id,'.$data['id'];
            // $rules['id'] = 'required|numeric|exists:medical_identifiers,id,deleted_at,NULL';
        }else{

            $rules['user_id'] =  'required|exists:users,id|unique:medical_identifiers,user_id,NULL,id,deleted_at,NULL';
        }


        $validator = Validator::make($data, $rules);
        return $validator;
    }

    public static function validateGetMedicalIdentifier($data){
        $rules = array(
            'user_id' => 'required|numeric|exists:users,id,deleted_at,NULL',
            );
        $validator = Validator::make($data, $rules);
        return $validator;
    }


    public static function validateTiming($data){
        $rules = array(
            'user_id' => 'required|numeric|exists:users,id,deleted_at,NULL',
            'facility_location_id' => 'required|numeric|exists:facility_locations,id,deleted_at,NULL',
        );

        $validator = Validator::make($data, $rules);
        return $validator;
    }

    
}
