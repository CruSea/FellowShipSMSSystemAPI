<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class PasswordResetController extends Controller
{
    
    public function passwordReset($email,$pass){
            
        if($admin_email = User::where('email', '=', $email)->exists()){

        DB::table('users')->where("email", '=',$email)
                          ->update(['users.password'=>$pass]);

              return response('Password Updates Successfully');        

        }else{
              return response('Woops Failed operation !!!');
        }
    }
}
