<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
use App\Role;

class AuthController extends Controller
{

    public function register(Request $request){

        try{
     
            $requests = request()->only('first_name', 'last_name','email','university','campus','phone_number');
            $rule = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string', 
                'email' => 'required|string|unique:users',
                'university' => 'required|string',  
                'campus'  => 'required|string',
                'phone_number'=> 'regex:/^([0-9\s\-\+\(\)]*)$/',          
            ]; 
            $validator = Validator::make($requests, $rule);

            if($validator->fails()) {
                return response()->json(['error' => 'validation error' , 'message' => $validator->messages()], 400);
            }
            $phone_number  = $request['phone_number'];
            $contact0 = Str::startsWith($request['phone_number'], '0');
            $contact9 = Str::startsWith($request['phone_number'], '9');
            $contact251 = Str::startsWith($request['phone_number'], '251');
            if($contact0) {
                $phone_number = Str::replaceArray("0", ["+251"], $request['phone_number']);
            }
            else if($contact9) {
                $phone_number = Str::replaceArray("9", ["+2519"], $request['phone_number']);
            }
            else if($contact251) {
                $phone_number = Str::replaceArray("251", ['+251'], $request['phone_number']);
            }
            if(strlen($phone_number) > 13 || strlen($phone_number) < 13) {
                return response()->json(['message' => 'validation error', 'error' => 'phone number length is not valid'], 400);
            }
            $check_phone_existance = User::where('phone_number', $phone_number)->exists();
            if($check_phone_existance) {
                return response()->json(['error' => 'The phone has already been taken'], 400);
            } 

        $user = new User();
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->email = $request['email'];
        $user->university = $request['university'];
        $user->campus = $request['campus'];
        $user->phone_number = $request['phone_number'];
        $user->password = $request['password'];
        $user->password = bcrypt($request->password);
        $user->save();
   
       // $user->roles()->attach(Role::where('name', 'User')->first());

       // $user = User::create();
        $accessToken = $user ->createToken('authToken')->accessToken;

        Auth::login($user);

        return response(['user' => $user, 'access_token' => $accessToken]);

    }catch(Exception $ex) {
        return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
    }
    }



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

            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            
            return response()->json(['status'=>true, 'message'=>'Authentication Successful','Logged In as','result'=>$user, 'token'=>$token],200);
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
