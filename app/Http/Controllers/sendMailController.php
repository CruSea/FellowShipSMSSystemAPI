<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class sendMailController extends Controller
{
    public function sendMail() {
    	

            Mail::raw('Now I know how to send emails with Laravel', function($message)
             {
                 $message->subject('Hi There!!');
                 $message->from(config('mail.from.address'), config("app.name"));
                 $message->to('yididiya127@gmail.com');
             });
       
    }
}
