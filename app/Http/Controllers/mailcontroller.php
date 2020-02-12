<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class mailcontroller extends Controller
{
    public function send ($email)
    {
    
        Mail::send(['text'=>'mail'],['name','campus SMS system'],function($message) use ($email){
            $message->to($email)->subject('Register Admin');
            $message->from('smssolutionsystem@gmail.com','ruth');

        });
        return response(['email'=>$email]);
    }

  // Mail::send(new  sendmail());
/*
public function email()
{
    return view('mail');
 }*/
}
