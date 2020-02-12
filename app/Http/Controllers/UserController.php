<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Fellowship;
use App\User;

class UserController extends Controller
{
    public function register(Request $request){

        try{
     
            $requests = request()->only('first_name', 'last_name','email','university_name','university_city','campus','phone_number','password');
            $rule = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string', 
                'email' => 'required|string|unique:users',
                'university_name' => 'required|string',  
                'university_city' => 'required|string',  
                'campus'  => 'required|string',
                'phone_number'=> 'regex:/^([0-9\s\-\+\(\)]*)$/', 
                'password'  => 'required|string',         
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

            $fellowship = new Fellowship();
            $fellowship->university_name = $request->input('university_name');
            $fellowship->university_city = $request->input('university_city');
            $fellowship->campus = $request->input('campus');
 
        if($fellowship->save()) {    
        $user = new User();
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->email = $request['email'];
        $user->phone_number = $request['phone_number'];
        $user->fellowship_id = $fellowship->fellow_id;
        $user->password = $request['password'];
        $user->password = bcrypt($request->password);
        $user->save();
   
       // $user->roles()->attach(Role::where('name', 'User')->first());

       // $user = User::create();
        $accessToken = $user ->createToken('authToken')->accessToken;

        Auth::login($user);

        return response(['user' => $user, 'access_token' => $accessToken]);
        } else {
            $fellowship->delete();
            return response()->json(['error' => 'something went wrong unable to register'], 500);
        }
    }catch(Exception $ex) {
        return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
    }
    }
}
