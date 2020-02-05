<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\GroupContact;
use App\Contact;
use App\Fellowship;
use App\User;
use Input;
use Excel;
use DateTime;
use Carbon\Carbon;
use Auth;

class ContactController extends Controller
{
    public function __construct() {

        $this->middleware('auth:api');
    }

    public function addContact() {
        try{
    
            $request = request()->only('full_name', 'phone_number', 'email','acadamic_dep','fellow_dep', 'gender','graduate_year');
            $rule = [
                'full_name' => 'required|string|max:255',
                'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/',
                'email' => 'email|max:255|unique:contacts|nullable',
                'acadamic_dep' => 'string|max:255', 
                'fellow_dep' => 'required|string|max:255',
                'gender' => 'required|string|max:6',
                'graduate_year' => 'string',
            ]; 
            $validator = Validator::make($request, $rule);
            if($validator->fails()) {
                return response()->json(['error' => 'validation error' , 'message' => $validator->messages()], 400);
            }
            $phone_number = $request['phone_number'];
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
            $check_phone_existance = Contact::where('phone_number', $phone_number)->exists();
            if($check_phone_existance) {
                return response()->json(['error' => 'The phone has already been taken'], 400);
            } 

            $check_phone = GroupContact::where('phone', $phone_number)->exists();
            if($check_phone) {
                return response()->json(['error' => 'The phone has already been taken in Group Contact'], 400);
            } 

            // ((((((((((((((((((((( check whether contact is under graduate ))))))))))))))))))))) 

            $graduationYear = $request['graduation_year'].'-07-30';
            $parse_graduation_year = Carbon::parse($graduationYear);
            $today = Carbon::parse(date('Y-m-d'));
            $difference = $today->diffInDays($parse_graduation_year, false);
            
            if($difference <= 0) {
                return response()->json(['error' => 'graduation year is not valid for under graduate member'], 400);
            } else if($difference < 380 && $difference > 0) {
                $this_year_gc = true;
            }   

            $contact = new Contact();
            $contact->full_name = $request['full_name'];
            $contact->phone_number = $request['phone_number'];
            $contact->phone_number = $phone_number;
            $contact->email = $request['email'];
            $contact->acadamic_dep = $request['acadamic_dep'];
            $contact->fellow_dep = $request['fellow_dep'];
            $contact->gender = $request['gender'];
            $contact->graduate_year = $request['graduate_year'];
            $contact->is_under_graduate = true;
            $contact->is_this_year_gc = $this_year_gc;
           // $contact->created_by = $user->full_name;
           
           $contact_name = DB::table('groups')->select('group_id')->where([
            ['group_name', '=', $contact->fellow_dep],
        ])->value('group_id');

          // if($contact->fellow_dep == )

           $group_contact = new GroupContact();
           $group_contact->fullname = $request['full_name'];
           $group_contact->phone = $phone_number;
           $group_contact->email = $request['email'];
           $group_contact->acadamic_department = $request['acadamic_dep'];
           $group_contact->fellow_department = $request['fellow_dep'];
           $group_contact->gender = $request['gender'];
           $group_contact->graduation_year = $request['graduate_year'];
           $group_contact->contacts_id = $contact_name;
           $group_contact->save();
           

            if($contact->save()) {
                // if($contact->team_id != null) {
               /* if($team instanceof Team) {
                    $contact_team = new ContactTeam();
                    $contact_team->team_id = $team->id;
                    $contact_team->contact_id = $contact->id;
                    $contact_team->save();
                } */
                return response()->json(['message' => 'contact added successfully'], 200);
                 }
           
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'something went wrong, unable to save the contact'], 500);
        }catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }


    public function getContact($id) {
        try {
            // $contacts = Contact::all();
            $contacts = Contact::where([['is_under_graduate', '=', 1]])->orderBy('contact_id')->paginate(10);
            $countContact = Contact::count();
            $count_under_graduate = count($contacts);
            
            return response()->json(['contacts' => $contacts], 200);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
        }
    }
    public function getContacts($id) {
        try {
          
           // $contacts = Contact::where([['contact_id','=',$id],['is_under_graduate', '=', 1]])->orderBy('contact_id')->paginate(10);
            $contacts = DB::table('contacts')->select('full_name','phone_number','email','acadamic_dep',
            'fellow_dep','gender','graduate_year')->where([
                ['is_under_graduate', '=', 1],['contact_id','=',$id] 
            ])->first();   

            $fname=$contacts->full_name; 
            $ph_number=$contacts->phone_number; 
            $email=$contacts->email;                         
            $acadamic_dep=$contacts->acadamic_dep;      
            $fellow_dep=$contacts->fellow_dep; 
            $gender=$contacts->gender;
            $graduate_year=$contacts->graduate_year;

                return response()->json([[$fname],[$ph_number],[$email],[$acadamic_dep],[$fellow_dep],[$gender],[$graduate_year]]);
           
           // return response()->json(['contacts' => $contacts], 200);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
        }
    }

    public function exportContact() {
                
                return Excel::download(new UsersExport, 'contacts.xlsx');
    }


    public function deleteContact($contact_id) {
        try {
        
            $contact = Contact::find($contact_id);
            if($contact instanceof Contact) {
                if($contact->delete()) {
                    return response()->json(['message' => 'contact deleted successfully'], 200);
                }
                return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'unable to delete contact'], 500);
            } 
            return response()->json(['message' => 'an error occurred', 'error' => 'contact is not found'], 404);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function updateContact($id) {
        try {
            // Check User Token  

            $request = request()->only('full_name', 'phone_number', 'email','acadamic_dep','fellow_dep', 'graduate_year');
            $contact = Contact::find($id);
            
            if($contact instanceof Contact) {
                $rule = [
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:13|unique:contacts',
                'email' => 'email|max:255|nullable',
                'acadamic_dep' => 'string|max:255', 
                'fellow_dep' => 'required|string|max:255',               
                'graduate_year' => 'required|string',
                ];
                $validator = Validator::make($request, $rule);
                if($validator->fails()) {
                    return response()->json(['message' => 'validation error' , 'error' => $validator->messages()], 500);
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
                // check weather the phone exists before
                $check_phone_existance = Contact::where('phone_number', $phone_number)->exists();
                if($check_phone_existance && $phone_number != $contact->phone) {
                    return response()->json(['error' => 'The phone has already been taken'], 400);
                }
                // check email existance before
                if($request['email'] != null) {
                    $check_email_existance = Contact::where('email', '=',$request['email'])->exists();
                    if($check_email_existance && $request['email'] != $contact->email) {
                        return response()->json(['error' => 'The email has already been taken'], 400);
                    }
                }
        // >>>>>>>>>> ||| check whethe contact is under graduate ||| <<<<<<<<<<<

                $this_year_gc = false;
                $graduationYear = $request['graduate_year'].'-07-30';
                $parse_graduation_year = Carbon::parse($graduationYear);
                $today = Carbon::parse(date('Y-m-d'));
                $difference = $today->diffInDays($parse_graduation_year, false);
                
                if($difference <= 0) {
                    return response()->json(['error' => 'graduation year is not valid for under graduate member'], 400);
                } else if($difference < 380 && $difference > 0) {
                    $this_year_gc = true;
                }
                $contact->full_name = isset($request['full_name']) ? $request['full_name'] : $contact->full_name;
                $contact->phone_number = isset($request['phone_number']) ? $phone_number : $contact->phone_number;
                $contact->email = isset($request['email']) ? $request['email'] : $contact->email;
                $contact->acadamic_dep = isset($request['acadamic_dep']) ? $request['acadamic_dep'] : $contact->acadamic_dep;
                $contact->fellow_dep = isset($request['fellow_dep']) ? $request['fellow_dep'] : $contact->fellow_dep;
                $contact->graduate_year = isset($request['graduate_year']) ? $request['graduate_year'].'-07-30' : $contact->graduate_year;
                $contact->is_this_year_gc = $this_year_gc;
                if($contact->update()) {
                    return response()->json(['message' => 'contact updated seccessfully'], 200);
                } 
                return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'unable to update contact'], 500);
            }
            return response()->json(['message' => 'error found', 'error' => 'contact is not found'], 404);

        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function searchContact(Request $request) {
        try {
                $input = $request->all();
                $contacts = Contact::query();
                $search = Input::get('search');
                if($search) {
                    $contacts = $contacts->where([['full_name', 'LIKE', '%'.$search.'%'], ['is_under_graduate', '=', true]])->orWhere([['phone', 'LIKE','%'.$search.'%'], ['fellowship_id', '=', $user->fellowship_id], ['is_under_graduate', '=', true]])->get();
                    if(count($contacts) > 0) {
                        return $contacts;
                    }
                }
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }
    
    public function importContact() { 

        $count_add_contacts = 0;
		if(Input::hasFile('file')){
            $path = Input::file('file')->getRealPath();
			$data = Excel::load($path, function($reader) {
            })->get();
            $headerRow = $data->first()->keys();
            $request = request()->only($headerRow[0], $headerRow[1], $headerRow[2], $headerRow[3], $headerRow[4], $headerRow[5], $headerRow[6]);
			if(!empty($data) && $data->count()){
				foreach ($data as $key => $value) {
         // ============ || phone validation || ============
                    if($value->phone == null) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts.' contacts added yet','message' => "validation error", 'error' => "phone can't be null"], 403);
                        }
                        return response()->json(['message' => "validation error", 'error' => "phone can't be null"], 403);
                    }
                    if($value->full_name == null) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts. ' contacts added yet','message' => 'validation error', 'error' => "full name can't be null"], 403);
                        }
                        return response()->json(['message' => 'validation error', 'error' => "full name can't be null"], 403);
                    }
                    if($value->gender == null) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts. ' contacts added yet','message' => 'validation error', 'error' => "gender can't be null"], 403);
                        }
                        return response()->json(['message' => 'validation error', 'error' => "gender can't be null"], 403);
                    }
                    if($value->acadamic_dep == null) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts.' contacts added yet','message' => 'validation error', 'error' => "acadamic department year can't be null"], 404);
                        }
                        return response()->json(['message' => 'validation error', 'error' => "acadamic department year can't be null"], 404);
                    }
                    if($value->graduate_year == null) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts.' contacts added yet','message' => 'validation error', 'error' => "graduation year can't be null"], 404);
                        }
                        return response()->json(['message' => 'validation error', 'error' => "graduation year can't be null"], 404);
                    }
     // ===============||||||| check whethe contact is under graduate |||||||||==================
                    $this_year_gc = false;
                    $graduationYear = $value->graduation_year.'-07-30';
                    $parse_graduation_year = Carbon::parse($graduationYear);
                    $today = Carbon::parse(date('Y-m-d'));
                    $difference = $today->diffInDays($parse_graduation_year, false);
                    
                    if($difference <= 0) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts.' contacts added yet','error' => 'graduation year is not valid for under graduate member'], 400);
                        }
                        return response()->json(['error' => 'graduation year is not valid for under graduate member'], 400);
                    } else if($difference < 380 && $difference > 0) {
                        $this_year_gc = true;
                    }
                    $team = groups::where(['group_name', '=', $value->team])->first();
                    if($value->team != null && !$team) {
                        if($count_add_contacts > 0) {
                            return response()->json(['response' => $count_add_contacts.' contacts added yet','error' => $value->team.' team is not found, please add '.$value->team.' team first if you want to add contact to '.$value->team.' team'], 400);
                        }
                        return response()->json(['error' => $value->team.' team is not found, please add '.$value->team.' team first if you want to add contact to '.$value->team.' team'], 400);
                    }
                    $phone_number  = $value->phone;
                    $contact0 = Str::startsWith($value->phone, '0');
                    $contact9 = Str::startsWith($value->phone, '9');
                    $contact251 = Str::startsWith($value->phone, '251');
                    if($contact0) {
                        $phone_number = Str::replaceArray("0", ["+251"], $value->phone);
                    }
                    else if($contact9) {
                        $phone_number = Str::replaceArray("9", ["+2519"], $value->phone);
                    }
                    else if($contact251) {
                        $phone_number = Str::replaceArray("251", ['+251'], $value->phone);
                    }
                    if(strlen($phone_number) > 13 || strlen($phone_number) < 13) {
                    }
             // *********** check weather the phone exists before **************
                    $check_phone_existance = Contact::where('phone', $phone_number)->exists();
        
            // ************ check weather the email exists before ***************
                    $check_email_existance = Contact::where([['email', '=',$value->email],['email', '!=', null]])->exists();
                    if(!$check_phone_existance && !$check_email_existance && strlen($phone_number) == 13) {
                        $contact = new Contact();
                        $contact->full_name = $value->full_name;
                        $contact->phone = $phone_number;
                        $contact->email = $value->email;
                        $contact->acadamic_dep = $value->acadamic_dep;
                        $contact->fellow_dep = $value->fellow_dep;
                        $contact->gender = $value->gender;
                        $contact->graduate_year = $value->graduate_year.'-07-30';
                        $contact->is_under_graduate = true;
                        $contact->is_this_year_gc = $this_year_gc;
                       
                        if($contact->save()) {
                                return response()->json(['message' => $count_add_contacts.' contacts Imported successfully'], 200);
                            }
                            $count_add_contacts++;
                        }
                    }
                }
                if($count_add_contacts == 0) {
                    return response()->json(['message' => 'no contact is added'], 200);
                }
                return response()->json(['message' => $count_add_contacts.' contacts added successfully'], 200);
            }
            else {
                return response()->json(['message' => 'file is empty', 'error' => 'No contact is found in the file'], 404);
            }
        }
       // return response()->json(['message' => 'File not found', 'error' => 'Contact File is not provided'], 404);
    }

