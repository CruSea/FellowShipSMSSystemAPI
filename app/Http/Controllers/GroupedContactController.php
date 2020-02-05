<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exports\GroupExport;
use App\GroupContact;
use App\Contact;
use App\User;
use App\groups;
use App\Fellowship;
use Input;
use Excel;
use DateTime;
use Carbon\Carbon;

class GroupedContactController extends Controller
{
    public function __construct() {
         $this->middleware('auth:api');
    }

    public function addGroupedContact($id) {
        try{
            $request = request()->only('fullname', 'phone', 'email', 'acadamic_department','gender','graduation_year');
            $rule = [
                'fullname' => 'required|string|max:255',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:13|unique:grouped_contacts',
                'email' => 'required|string',
                'acadamic_department' => 'required|string',  
                'gender' => 'required|string',
                'graduation_year' => 'required|date'          
            ];                                      
            $validator = Validator::make($request, $rule);
            $phone  = $request['phone'];
              //********** check weather the phone exists before ************
              $check_phone_existance = GroupContact::where('phone', $phone)->exists();
              $check_cphone_existance = Contact::where('phone_number', $phone)->exists();
              if($check_phone_existance) {
                  return response()->json(['error' => 'The phone has already been taken'], 400);
              } 

            $contact0 = Str::startsWith($request['phone'], '0');
            $contact9 = Str::startsWith($request['phone'], '9');
            $contact251 = Str::startsWith($request['phone'], '251');
            if($contact0) {
                $phone = Str::replaceArray("0", ["+251"], $request['phone']);
            }
            else if($contact9) {
                $phone = Str::replaceArray("9", ["+2519"], $request['phone']);
            }
            else if($contact251) {
                $phone = Str::replaceArray("251", ['+251'], $request['phone']);
            }
            if(strlen($phone) > 13 || strlen($phone) < 13) {
                return response()->json(['message' => 'validation error', 'error' => 'phone number length is not valid'], 400);
            }
          
            // check mail existance before
            if($request['email'] != null) {
                $check_email_existance = GroupContact::where('email', '=',$request['email'])->exists();
                if($check_email_existance) {
                    return response()->json(['error' => 'The email has already been taken'], 400);
                }
            }

            $check_cphone_existance = Contact::where('phone_number', $phone)->exists();
            if($check_cphone_existance) {
                return response()->json(['error' => 'The phone has already been taken in Contact table'], 400);
            } 

            $graduationYear = $request['graduation_year'].'-07-30';
            $parse_graduation_year = Carbon::parse($graduationYear);
            $today = Carbon::parse(date('Y-m-d'));
            $difference = $today->diffInDays($parse_graduation_year, false);
            
            if($difference <= 0) {
                return response()->json(['error' => 'graduation year is not valid for under graduate member'], 400);
            } else if($difference < 380 && $difference > 0) {
                $this_year_gc = true;
            }  

           // $contact = Contact::find($phone_number);
            $group_contact = new GroupContact();
            $contact = new Contact();
           // $group = groups::where([['group_id', '=', $id]]);
           $group_name = DB::table('groups')->select('group_name')->where([
            ['group_id', '=', $id],
        ])->value('group_name');
            
            $group_contact->fullname = $request['fullname'];
            $group_contact->phone = $request['phone'];
            $group_contact->email = $request['email'];
            $group_contact->acadamic_department = $request['acadamic_department'];
            $group_contact->fellow_department = $group_name;
            $group_contact->gender = $request['gender'];
            $group_contact->graduation_year = $request['graduation_year'];
            $group_contact->contacts_id = $id;

             
            $contact->full_name = $request['fullname'];
            $contact->phone_number = $request['phone'];
            $contact->email = $request['email'];
            $contact->acadamic_dep = $request['acadamic_department'];
            $contact->fellow_dep = $group_name;
            $contact->gender = $request['gender'];
            $contact->graduate_year = $request['graduation_year'];
            $contact->is_under_graduate=true;
            $contact->is_this_year_gc = $this_year_gc;
            $contact->save();
           // $contact->contacts_id = $id;
                
            if($group_contact->save()) {

                return response()->json(['message' => 'contact added successfully'], 200);
            }
           
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'something went wrong, unable to save the contact'], 500);
        }catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }


    
    public function getGroupedContact($id) {
        try {
          
            // some foreign key conditions here
            $group_contact = GroupContact::where([['contacts_id', '=', $id]])->orderBy('id')->paginate(10);
           
            return response()->json(['Group_Contacts' => $group_contact], 200);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
        }
    }

    public function deleteGroupedContact($id) {
        try {
         
            $group = GroupContact::find($id);

            if($group instanceof GroupContact) {

                if($group->delete()) {
                    return response()->json(['message' => 'contact deleted successfully'], 200);
                }
                return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'unable to delete contact'], 500);
            } 
            return response()->json(['message' => 'an error occurred', 'error' => 'contact is not found'], 404);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getGenders(){
        $group_contact = new GroupContact();

        $group_contacts = GroupContact::where(['gender', '=', 'male'])->orderBy('group_id')->paginate(10);
          
    }

    public function exportGroupedContact() {
                
        return Excel::download(new GroupExport, 'group_contacts.xlsx');
}

}
