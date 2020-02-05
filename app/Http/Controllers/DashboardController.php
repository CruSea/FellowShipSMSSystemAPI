<?php

namespace App\Http\Controllers;
use App\Contact;
use App\groups;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
	public function __construct() {

        $this->middleware('auth:api');
	}
	
	public function current_user(){

		$user=auth('api')->user('first_name','last_name');
		$fname=$user->first_name;
		$lname=$user->last_name;
        return response()->json([[$fname],[$lname]]);
	}

	public function current_univ(){

		$user=auth('api')->user('university','campus');
		$uni=$user->university;
		$camp=$user->campus;
        return response()->json([[$uni],[$camp]]);
	}

    public function underGraduateMembersNumber() {
    	try {
            $under_graduate_contact = new Contact();
    	
                $under_graduate_contact = Contact::all();
    			$count = $under_graduate_contact->count();
				return response()->json(['count' => $count], 200);
				
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	
    public function numberOfGroups() {
    	try {
            $count_group = new groups();
    	
                $count_group = groups::all();
    			//$under_graduate_contact = Contact::where(['is_under_graduate', '=', 1])->get();
    			$count = $count_group->count();
    			return response()->json(['count' => $count], 200);
    	
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	 public function campusTotalContact(){
		try {
            $count_group = new Contact();
                $count_group = Contact::all();
    			$count = $count_group->count();
    			return response()->json([$count], 200);
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	 }

	 public function getGenderCount() {
    
		try { 
		  
			$male = Contact::where([['is_under_graduate', '=', 1],['gender','=','male']])->count();
			$female = Contact::where([['is_under_graduate', '=', 1],['gender','=','female']])->count();

			return response()->json(['male' => $male, 'female' =>$female], 200);    
	   
		} catch(Exception $ex) {
			return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
		}
	}

	
}
