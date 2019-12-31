<?php

namespace App\Http\Controllers\Authentication;
use App\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function register (request $request)
    {
  $validateData = $request->validate([
'name'=> 'required|max:55',
'email'=> 'email|required|unique:users',
'password'=>'required|confirmed',
    ]);
    $validateData ['password'] = dcrypt($request ->password);
        $user = user::create($validatedata);
        $accessToken = $user->createToken('authToken')->acessToken;

        return response(['user'=> $user, 'access_token' =>accessToken]);

    }
   


    public function login (request $request)
    {
$loginData = $request->validate([

'email'=> 'email|required',
'password'=>'required',
    ]);    
    if(!auth()->attempt($loginData)){
        return response(['message' =>'invalid']);
    }

    $accessToken = auth()->user()->createToken('authToken')->acessToken;


    return response(['user'=>auth()->user(), 'access_token' =>accessToken]);
    }
}