<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\DB;
use App\User;

class sendMailController extends Controller
{
    //>>>>>>>>>>>>>|||| Send Registration Link |||||<<<<<<<<<<<<<<
    
    public function sendMail($email) {
    	
        Mail::send(['text'=>'mail'],['name','ruth'],function($message) use ($email){
            $message->to($email)->subject('test Email');
            $message->from('smssolutionsystem@gmail.com','ruth');

        });
        return response(['email'=>$email]);
       
    }

    // >>>>>>>>>>>>|||| Check mail existance and send Password ResetLink||||<<<<<<<<<<

    public function sendResetLink($email){

        if($admin_email = User::where('email', '=', $email)->exists()){
               
        Mail::send(['text'=>'reset_mail'],['name','ruth'],function($message) use ($email){
            $message->to($email)->subject('Password Reset ');
            $message->from('smssolutionsystem@gmail.com','DS Fellow System');
        });
        return response(['Success']);
        }else{
            return response(['Email'=>$email,'Does not exit']);
            //exit();
        } 
  }
}
