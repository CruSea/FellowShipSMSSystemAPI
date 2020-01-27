<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Fellowship;
use App\Notification;

class FellowshipController extends Controller
{
    public function show() {
        try {
            
		  // Passpost Token here 
		  
            if($user instanceof User) {
                $fellowship_id = $user->fellowship_id;
                $fellowship = Fellowship::find($fellowship_id);
                if($fellowship instanceof Fellowship) {
                    return response()->json(['fellowship' => $fellowship], 200);
                } else {
                    return response()->json(['error' => 'Ooops! something went wrong, fellowship is not found'], 404);
                }
            } else {
                return response()->json(['error' => 'token expired'], 401);
            }
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}
	
	public function addFellow() {
        try{
           
            $request = request()->only('university_name', 'university_city','campus');
            $rule = [
                'university_name' => 'required|string|max:255',
                'university_city' => 'required|string', 
                'campus' => 'required|string',            
            ]; 
            $validator = Validator::make($request, $rule);
            
            $fellow = new Fellowship();
            $fellow->university_name = $request['university_name'];
            $fellow->university_city = $request['university_city'];
            $fellow->campus = $request['campus'];
        
            if($fellow->save()) {

                return response()->json(['message' => 'contact added successfully'], 200);
                 
            }
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'something went wrong, unable to save the contact'], 500);
        }catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
	}
	


    public function update() {
    	try {
    		$user = JWTAuth::parseToken()->toUser();
    		if($user instanceof User) {
    			$request = request()->only('university_name', 'university_city', 'specific_place');
    			$rule = [
    				'university_name' => 'required|string|min:10',
    				'university_city' => 'required|string|min:110',
    				'specific_place' => 'string|nullable',
    			];
    			$validator = Validator::make($request, $rule);
    			if($validator->fails()) {
    				return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 400);
    			}
                $notification = new Notification();
                $fellowship_id = $user->fellowship_id;
    			$fellowship = Fellowship::find($fellowship_id);
    			$fellowship->university_name = $request['university_name'];
    			$fellowship->university_city = $request['university_city'];
    			$fellowship->specific_place = $request['specific_place'];

    			if($fellowship->update()) {
                    $notification->notification = "Fellowship profile has been updated by ".$user->full_name;
                    $notification->fellowship_id = $fellowship_id;
                    $notification->save();
    				return response()->json(['message' => 'fellowship updated successfully'], 200);
    			} else {
    				return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'fellowship is not updated'], 500);
    			}
    		} else {
    			return response()->json(['error' => 'token expired'], 401);
    		}
    	} catch(Exception $ex) {
    		return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
    	}
    }
}
