<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\GroupContact;

use Illuminate\Http\Request;

class GroupContactCountController extends Controller
{
    public function GroupMemberCount($id) {
    	try {
            $count_member = new GroupContact();
    		
           // $count_member = GroupContact::all();
            //$under_graduate_contact = Contact::where(['is_under_graduate', '=', 1])->get();
           // $count = $count_member->count();
            $_count = DB::table('group_contacts')->where('contacts_id','=',$id)->count();
    			return response()->json(['count' => $_count], 200);
    		
    	} catch(Exception $ex) {
            return response()->json(['message' => 'somthing went wrong', 'error' => $ex->getMessage()], $ex->getStatusCode());
        }
    }
}
