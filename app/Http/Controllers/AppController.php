<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Role;

class AppController extends Controller
{
    public function __construct() {
         $this->middleware('auth:api');
     }
     
     public function postAdminAssignRoles($value,$id)
     {
        ############ check box condition #######################
            
        // $user->roles()->attach(Role::where('name', 'User')->first());
 
         if($value == true){
 
        $user = User::where([['id','=', $id]])->first();
        $user->roles()->attach(Role::where('name', 'User')->first());
 
        $accessToken = $user ->createToken('authToken')->accessToken;
        return response(['Role Added to user' => $user,'access_token' => $accessToken]);
 
        }else{
         $user = User::where([['id','=', $id]])->first();   
         $user->roles()->detach();
         return response(['Role detached from user' => $user]);
        } 
 
        /* if ($request['role_user']) {
             $user->roles()->attach(Role::where('name', 'User')->first());
         }
         return redirect()->back(); */
     }
 
     public function adminPage(){
         return response(['Admin page']);
     }
}
