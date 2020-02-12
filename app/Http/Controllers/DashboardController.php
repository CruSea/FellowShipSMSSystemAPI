<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Contact;
use App\groups;
use App\Fellowship;
use App\sentMessages;


use Illuminate\Http\Request;

class DashboardController extends Controller
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

	public function current_univ(){

		$user=auth('api')->user();
        
		$uni=Fellowship::where('fellow_id', '=', $user->fellowship_id)->first();
		$university=$uni->university_name;
		$campus=$uni->campus;
        return response()->json([[$university],[$campus]]);
	}

    public function underGraduateMembersNumber() {
    	try {
			$user=auth('api')->user();

           // $under_graduate_contact = new Contact();
    	
                $under_graduate_contact = Contact::where([['fellowship_id', '=', $user->fellowship_id],['is_under_graduate','=',true]])->get();
    			$count = $under_graduate_contact->count();
				return response()->json(['count' => $count], 200);
				
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	
    public function numberOfGroups() {
    	try {
			$user=auth('api')->user();

                $count_group = groups::where('fellowship_id', '=', $user->fellowship_id)->get();
    			$count = $count_group->count();
    			return response()->json(['count' => $count], 200);
    	
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	}

	 public function campusTotalContact(){
		try {
			$user=auth('api')->user();

            $count_group = new Contact();
                $count_group = Contact::where('fellowship_id', '=', $user->fellowship_id)->get();
    			$count = $count_group->count();
    			return response()->json([$count], 200);
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
	 }

	 public function getGenderCount() {
		$user=auth('api')->user();
		try { 
		  
			$male = Contact::where([['is_under_graduate', '=', 1],['gender','=','male'],['fellowship_id', '=', $user->fellowship_id]])->count();
			$female = Contact::where([['is_under_graduate', '=', 1],['gender','=','female'],['fellowship_id', '=', $user->fellowship_id]])->count();

			return response()->json(['male' => $male, 'female' =>$female], 200);    
	   
		} catch(Exception $ex) {
			return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
		}
	}

	public function count_sentMessage(){

		$user=auth('api')->user();

		$contactMessage = sentMessages::where([['is_removed', '=', false],['fellowship_id', '=', $user->fellowship_id]])->orderBy('id', 'desc')->paginate(10);
            $countMessages = $contactMessage->count();
            if($countMessages == 0) {
                return response()->json(['No Mssages Available'], 200);
            }else{
				return response()->json(['messages'=>$countMessages], 200);
			}
	}

	public function get_msg(){
		$user=auth('api')->user();

		$month1 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '01')
       // ->whereDate('created_at', '2020-02-08')
		->count();
		$month2 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '02')
		->count();
		$month3 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '03')
		->count();
		$month4 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '04')
		->count();
		$month5 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '05')
		->count();
		$month6 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '06')
		->count();
		$month7 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '07')
		->count();
		$month8 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '08')
		->count();
		$month9 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '09')
		->count();
		$month10 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '10')
		->count();
		$month11 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '11')
		->count();
		$month12 = DB::table('sent_messages')
		->select('message')
		->where('fellowship_id','=',$user->fellowship_id)
		->whereMonth('created_at', '12')
		->count();

		return response()->json([[$month1],[$month2],[$month3],[$month4],[$month5],[$month6],[$month7],[$month8]
		,[$month9],[$month10],[$month11],[$month12]]);

		// $users = DB::table('users')
        // ->whereMonth('created_at', '10')
		// ->get();
		
		// The whereDay() method may be used to compare a column's value against a specific day of a month:

		// 	$users = DB::table('users')
		// 			->whereDay('created_at', '20')
		// 			->get();
	}

	
}
