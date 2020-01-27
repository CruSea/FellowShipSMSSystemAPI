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
	
    public function underGraduateMembersNumber() {
    	try {
            $under_graduate_contact = new Contact();
    		//$user = JWTAuth::parseToken()->toUser();
    	//	if($user) {
                $under_graduate_contact = Contact::all();
    			//$under_graduate_contact = Contact::where(['is_under_graduate', '=', 1])->get();
    			$count = $under_graduate_contact->count();
    			return response()->json(['count' => $count], 200);
    		/*} else {
    			return response()->json(['error' => 'token expired'], 401);
    		}*/
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	
    public function numberOfGroups() {
    	try {
            $count_group = new groups();
    		//$user = JWTAuth::parseToken()->toUser();
    	//	if($user) {
                $count_group = groups::all();
    			//$under_graduate_contact = Contact::where(['is_under_graduate', '=', 1])->get();
    			$count = $count_group->count();
    			return response()->json(['count' => $count], 200);
    		/*} else {
    			return response()->json(['error' => 'token expired'], 401);
    		}*/
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
