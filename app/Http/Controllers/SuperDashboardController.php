<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
		if($group_message == 0){
			//return response()->json('no group message available',200);
		}else{
			return response()->json([$group_message],200);
		}
	}

	public function totalMessageCost(){
		$contactMessage = sentMessages::where('is_removed', '=', false)->orderBy('id', 'desc')->paginate(10);
            $countMessages = $contactMessage->count();
            if($countMessages == 0) {
                return response()->json(['No Mssages Available'], 200);
            }else{
				$totalCost = $countMessages*0.25;
				return response()->json(['cost'=>$totalCost], 200);
			}
	}

	public function get_Recivemsgs(){

		$month1 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '01')
       // ->whereDate('created_at', '2020-02-08')
		->count();
		$month2 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '02')
		->count();
		$month3 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '03')
		->count();
		$month4 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '04')
		->count();
		$month5 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '05')
		->count();
		$month6 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '06')
		->count();
		$month7 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '07')
		->count();
		$month8 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '08')
		->count();
		$month9 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '09')
		->count();
		$month10 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '10')
		->count();
		$month11 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '11')
		->count();
		$month12 = DB::table('recieved_messages')
		->select('message')
		->whereMonth('created_at', '12')
		->count();

		return response()->json([[$month1],[$month2],[$month3],[$month4],[$month5],[$month6],[$month7],[$month8]
		,[$month9],[$month10],[$month11],[$month12]]);

	}

	
	public function get_sentmsg(){

		$month1 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '01')
       // ->whereDate('created_at', '2020-02-08')
		->count();
		$month2 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '02')
		->count();
		$month3 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '03')
		->count();
		$month4 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '04')
		->count();
		$month5 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '05')
		->count();
		$month6 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '06')
		->count();
		$month7 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '07')
		->count();
		$month8 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '08')
		->count();
		$month9 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '09')
		->count();
		$month10 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '10')
		->count();
		$month11 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '11')
		->count();
		$month12 = DB::table('sent_messages')
		->select('message')
		->whereMonth('created_at', '12')
		->count();

		return response()->json([[$month1],[$month2],[$month3],[$month4],[$month5],[$month6],[$month7],[$month8]
		,[$month9],[$month10],[$month11],[$month12]]);

	}
}
