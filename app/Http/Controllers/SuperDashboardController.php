<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\groups;
use App\Fellowship;
use App\sentMessages;
use App\GroupMessage;

class SuperDashboardController extends Controller
{
    public function __construct() {

        $this->middleware('auth:api');
	}
	
	public function current_user(){

		$user=auth('api')->user();

		$fname=$user->first_name;
		$lname=$user->last_name;
        return response()->json([[$fname],[$lname]]);
	}

	public function total_univ(){

	}

    public function TotalunderGraduateMembersNumber() {
    	try {
			
                $under_graduate_contact = Contact::where('is_under_graduate','=',true)->get();
    			$count = $under_graduate_contact->count();
				return response()->json(['count' => $count], 200);
				
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	
    public function TotalnumberOfGroups() {
    	try {
			
                $count_group = groups::all();
    			$count = $count_group->count();
    			return response()->json(['count' => $count], 200);
    	
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	 public function campusTotalContact(){
		try {
                $count_group = Contact::all();
    			$count = $count_group->count();
    			return response()->json([$count], 200);
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	 }

	 public function getGenderCount() {
		$user=auth('api')->user();
		try { 
		  
			$male = Contact::where([['is_under_graduate', '=', 1],['gender','=','male']])->count();
			$female = Contact::where([['is_under_graduate', '=', 1],['gender','=','female']])->count();

			return response()->json(['male' => $male, 'female' =>$female], 200);    
	   
		} catch(Exception $ex) {
			return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
		}
	}

	public function count_sentMessage(){

		    $contactMessage = sentMessages::where('is_removed', '=', false)->get();
            $countMessages = $contactMessage->count();
            if($countMessages == 0) {
                return response()->json(['No Mssages Available'], 200);
            }else{
				return response()->json(['messages'=>$countMessages], 200);
			}
	}

	public function total_group_message(){
		$group_message = GroupMessage::where('is_removed', '=',false)->count();
		//$count_group_msg = $group_message->count();
		if($group_message == 0){
			return response()->json('no group message available',200);
		}else{
			return response()->json([$group_message],200);
		}
	}
}
