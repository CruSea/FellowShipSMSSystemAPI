<?php

namespace App\Http\Controllers\User;
use App\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class UserController extends Controller
{


    ///public function register (request $request)
    //{
 // $validateData = $request->validate([
   //     'name'=> 'required|max:55',
     //   'email'=> 'email|required|unique:users',
     //   'password'=>'required|confirmed',
   // ]);
  //  $validateData ['password'] = dcrypt($request ->password);
       // $user = user::create($validatedata);
       // $accessToken = $user->createToken('authToken')->acessToken;

       // return response(['user'=> $user, 'access_token' =>accessToken]);

    //}


    public function register(Request $request) {    
        $validator = Validator::make($request->all(), 
                     [ 
                     'name' => 'required',
                     'email' => 'required|email|unique:users',
                     'password' => 'required',  
                     'c_password' => 'required|same:password', 
                    ]);   
        if ($validator->fails()) {          
              return response()->json(['error'=>$validator->errors()], 401);                        }    
        $input = $request->all();  
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input); 
        $success['token'] =  $user->createToken('AppName')->accessToken;
        return response()->json(['success'=>$success]); 
       }
  
    
 //   public function login (request $request)
   // {
//$loginData = $request->validate([

//'email'=> 'email|required',
//'password'=>'required',
 // ]);    
   //if(!auth()->attempt($loginData)){
     //   return response(['message' =>'invalid']);
    //}

    //$accessToken = auth()->user()->createToken('authToken')->acessToken;


    //return response(['user'=>auth()->user(), 'access_token' =>accessToken]);
    //}



   public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
           $user = auth::user(); 
           $success['token'] =  $user->createToken('AppName')-> accessToken; 
            return response()->json(['success' => $success]); 
          } else{ 
           return response()->json(['error'=>'Unauthorised'], 401); 
           } 
       }
          
       // public function getUser() {
        // $user = Auth::user();
        // return response()->json(['success' => $user]); 
         //}
}

