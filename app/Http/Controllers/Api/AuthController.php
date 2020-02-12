<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Fellowship;
use App\user_role;
use App\User;
use App\Role;

class AuthController extends Controller
{

    public function login(){

 //  ---------->>>>>>>>>>><<<<<<<<<<<-------------

        try{
            $credentials = request()->only('email','password');
            $rules = [
                'email' => 'required|max:255',
                'password' => 'required|min:4'];
            $validator = Validator::make($credentials, $rules);
    
            if($validator->fails()){
                $error = $validator->messages();
                return response()->json(['status'=>false, 'result'=>null, 'message'=>null, 'error'=> $error],500);
            }
            if(!Auth::attempt($credentials)) {
               // return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! invalid credential has been used!','error'=>$exception->getMessage()], 401);
            }
            // ########## Fetch user id from user table
            // ########## Match it in user->role table
            // ########## Then use value and return role table name
            $contacts_id = DB::table('users')->select('id')->where([
                ['email', '=', $credentials['email']]
            ])->value('id');
            
            $role = DB::table('user_role')->select('role_id')->where([
                ['user_id', '=', $contacts_id]
            ])->value('role_id');

            $role_name = DB::table('roles')->select('name')->where([
                ['id', '=', $role]
            ])->value('name');
            
           // >>>>>>>>>>>>>>||||||| Check Role for Login ||||||||<<<<<<<<<<<<<<<<<<<

               if($role_name == 'Admin' || $role_name == 'User'){
                $user = Auth::user();
                $token = $user->createToken('authToken')->accessToken;
                 
               // $user_id=auth('api')->user()->id;
               // $id=$user_id->id;
                $id=$user->id;
                $role=User::find($id)->roles;
                
                return response()->json(['status'=>true, 'message'=>'Authentication Successful','User_role_id'=>$role,'result'=>$user, 'token'=>$token],200);
               }else{

                return response()->json(['status'=>false, 'message'=>'Woops UnAuthenticated!!!!'],500);
               }
           
        }catch (Exception $exception){
            return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! exception has occurred', 'error'=>$exception->getMessage()],500);
        }
    }


    public function logout (Request $request) {

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    
    }

    public function loginSys(){
        
    }
}
