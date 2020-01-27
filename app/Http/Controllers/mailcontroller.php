<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\sendmail;

class mailcontroller extends Controller
{
    public function send ($email)
    {
    
        Mail::send(['text'=>'mail'],['name','ruth'],function($message) use ($email){
            $message->to($email)->subject('test Email');
            $message->from('ruthdereje80@gmail.com','ruth');

        });
        return response(['email'=>$email]);
    }

  // Mail::send(new  sendmail());

public function email()
{
    return view('email');
}
}