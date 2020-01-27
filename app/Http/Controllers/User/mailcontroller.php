<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\sendmail;

class mailcontroller extends Controller
{
    public function send ()
    {
      //  Mail::send(['text'=>'mail'],['name','ruth'],function($message){

           // $message->to('ruthdereje80@gmail.com','to_ruth')->subject('test Email');
           // $message->from('ruthdereje80@gmail.com','ruth');

      //  });
   // }

   Mail::send(new  sendmail());
}
public function email()
{
    return view('email');
}
}