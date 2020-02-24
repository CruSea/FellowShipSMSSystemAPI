<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Str;
use App\SmsPort;
use App\sentMessages;
use App\FellowMessages;
use App\Fellowship;
use App\Settings;
use App\Contact;
use App\smsVote;
use App\groups;
use App\ContactGroup;
use App\GroupMessage;
use App\RecievedMessage;
use Carbon\Carbon;

class MessagesController extends Controller
{
    protected $negarit_api_url;

    public function __construct() {
      //  $this->middleware('auth:api');
        $this->negarit_api_url = 'https://api.negarit.net/api/';
    }

    public function sendContactMessage() {
        try {

            $user=auth('api')->user();

            $request = request()->only('message', 'sent_to', 'port_name');

            $rule = [
                'message' => 'required|string|min:1',
                'port_name' => 'required|string|max:255',
                'sent_to' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:13',
            ];

            $validator = Validator::make($request, $rule);
            if($validator->fails()) {
                return response()->json(['response' => 'validation error', 'error' => $validator->messages()], 500);
            }
          
            // $getSmsPortName = SmsPort::find($request['port_name']);
            // $getSmsPortName = DB::table('sms_ports')->where('port_name', '=', $request['port_name'])->first();
            $getSmsPortName = SmsPort::where('port_name', '=', $request['port_name'])->first();
            if(!$getSmsPortName) {
                return response()->json(['error' => 'sms port is not found', 
                    'message' => 'add sms port first'], 404);
            }
            $getSmsPortId = $getSmsPortName->id;

            $getSmsPort = SmsPort::find($getSmsPortId);
            if(!$getSmsPort) {
                return response()->json(['message' => 'error found', 'error' => 'sms port is not found'], 404);
            }
            // get api key from setting table
            $setting = Settings::where('name', '=', 'API_KEY')->first();
            if(!$setting) {
                return response()->json(['message' => '404 error found', 'error' => 'Api Key is not found'], 404);
            }
            
            $phone_number  = $request['sent_to'];
            $contact0 = Str::startsWith($request['sent_to'], '0');
            $contact9 = Str::startsWith($request['sent_to'], '9');
            $contact251 = Str::startsWith($request['sent_to'], '251');
            if($contact0) {
                $phone_number = Str::replaceArray("0", ["+251"], $request['sent_to']);
            }
            else if($contact9) {
                $phone_number = Str::replaceArray("9", ["+2519"], $request['sent_to']);
            }
            else if($contact251) {
                $phone_number = Str::replaceArray("251", ['+251'], $request['sent_to']);
            }
            //|| strlen($phone_number) < 13
            if(strlen($phone_number) > 13) {
                return response()->json(['message' => 'validation error', 'error' => 'phone number length is not valid'], 400);
            }

            $contains_name = Str::contains($request['message'], '{name}');
            $contact = Contact::where([['phone_number', '=', $phone_number], ['fellowship_id', '=', $user->fellowship_id]])->first();
            if($contact instanceof Contact) {
                if($contains_name) {
                    $replaceName = Str::replaceArray('{name}', [$contact->full_name], $request['message']);

                    $sentMessage = new sentMessages();
                    $sentMessage->message = $replaceName;
                    $sentMessage->sent_to = $contact->full_name;
                    $sentMessage->is_sent = false;
                    $sentMessage->is_delivered = false;
                    $sentMessage->sms_port_id = $getSmsPortId;
                    $sentMessage->fellowship_id = $user->fellowship_id;
                    $sentMessage->sent_by = $user->first_name;
                } else {
                    $sentMessage = new sentMessages();
                    $sentMessage->message = $request['message'];
                    $sentMessage->sent_to = $phone_number;
                    $sentMessage->is_sent = false;
                    $sentMessage->is_delivered = false;
                    $sentMessage->sms_port_id = $getSmsPortId;
                    $sentMessage->fellowship_id = $user->fellowship_id;
                    $sentMessage->sent_by = $user->first_name;
                }
            } else {
                $sentMessage = new sentMessages();
                $sentMessage->message = $request['message'];
                $sentMessage->sent_to = $phone_number;
                $sentMessage->is_sent = false;
                $sentMessage->is_delivered = false;
                $sentMessage->sms_port_id = $getSmsPortId;
                $sentMessage->fellowship_id = $user->fellowship_id;
                $sentMessage->sent_by = $user->first_name;
            }

            if($sentMessage->save()) {
                
                $get_campaign_id = $getSmsPort->negarit_campaign_id;
                $get_api_key = $getSmsPort->negarit_sms_port_id;
                $get_message = $sentMessage->message;
                $get_phone = $phone_number;
                $get_sender = $sentMessage->sent_by;

                // to send a post request (message) for Negarit API 
                $message_send_request = array();
                $message_send_request['API_KEY'] = $setting->value;
                $message_send_request['message'] = $get_message;
                $message_send_request['sent_to'] = $get_phone;
                $message_send_request['campaign_id'] = $get_campaign_id;
                // return $get_campaign_id;
                // return $setting->value;
                $negarit_response = $this->sendPostRequest($this->negarit_api_url, 
                        'api_request/sent_message?API_KEY='.$setting->value, 
                        json_encode($message_send_request));
                $decoded_response = json_decode($negarit_response);
                if($decoded_response) { 
                    if(isset($decoded_response->status) && isset($decoded_response->sent_message)) {
                        $send_message = $decoded_response->sent_message;
                        $sentMessage->is_sent = true;
                        $sentMessage->is_delivered = true;
                        $sentMessage->update();
                        return response()->json(['message' => 'message sent successfully',
                        'sent_message' => $send_message], 200);
                    }
                    $sentMessage->is_sent = true;
                    $sentMessage->is_delivered = true;
                    $sentMessage->update();
                    return response()->json(['response' => $decoded_response], 500);
                }
                return response()->json(['sent_message' => [], 'response' => 'Ooops! something went wrong, message is not sent'], 500);
            }
            return response()->json(['response' => '!Ooops something went wrong, message is not sent', 'error' => 'message is not sent, please send again'], 500);
        } catch(Exception $ex) {
            return response()->json(['response' => '!Ooops something went wrong, message is not sent', 'error' => $ex->getMessage()], 500);
        }
    }


    // ................................... 

    public function getContactsMessage() {
        try{
            $user=auth('api')->user();
           
            $contactMessage = sentMessages::where([['is_removed', '=', false],['fellowship_id', '=', $user->fellowship_id]])->orderBy('id', 'desc')->paginate(10);
            $countMessages = $contactMessage->count();
            if($countMessages == 0) {
                return response()->json(['messages' => $contactMessage], 200);
            }
            for($i = 0; $i < $countMessages; $i++) {
                $contactMessage[$i]->sent_by = $contactMessage[$i]->sent_by;
            }
            return response()->json(['messages' => $contactMessage, 'number_of_messages' => $countMessages], 200);
        } catch(Exception $x) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function removeContactMessage($id) {
        try {
            $user=auth('api')->user();

            $sentMessage = sentMessages::find($id);
         //   && $sentMessage->fellowship_id == $user->fellowship_id
            if($sentMessage instanceof sentMessages) {
              
              //  if($sentMessage->delete()) {
                $sentMessage->is_removed = 1;
               // }
                
                if($sentMessage->update()) {
                    return response()->json(['message' => 'message removed successfully'], 200);
                }
                else {
                    return response()->json(['message' => 'Ooops! something went wrong, message is not removed'], 500);
                }
            } else {
                return response()->json(['error' => 'message is not available'], 404);
            }
            
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }
// """""""""""""""""""""""""""""""""""""""" Group Message """""""""""""""""""""""""""
    public function sendGroupMessage() {
        try {
            $user=auth('api')->user();

            $team_message = new GroupMessage();
            $request = request()->only('port_name', 'group', 'message');
            $rule = [
                'port_name' => 'required|string|max:250',
                'group' => 'required|string|max:250',
                'message' => 'required|string|min:1',
                
            ];
            $validator = Validator::make($request, $rule);
            if($validator->fails()) {
                return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 500);
            }   
            $group = groups::where([['group_name', '=', $request['group']],['fellowship_id', '=', $user->fellowship_id]])->first();
            if(!$group) {
                return response()->json(['message' => 'Group is not found'], 404);
            }
                  //, ['fellowship_id', '=', $user->fellowship_id]
            $getSmsPortName = SmsPort::where('port_name', '=', $request['port_name'])->first();
            if(!$getSmsPortName) {
                return response()->json(['message' => 'error found', 'error' => 'sms port is not found'], 404);
            }
           
            $getSmsPortId = $getSmsPortName->id;
           // $fellowship_id = $user->fellowship_id;

            $team_id = $group->group_id;

            $contacts = Contact::whereIn('contact_id', ContactGroup::where('group_id','=', 
            $team_id)->select('contact_id')->get())->get();
            
            if(count($contacts) == 0) {
                return response()->json(['message' => 'member is not found in '.$group->name. ' team'], 404);
            } 

            $team_message->message = $request['message'];
            $team_message->group_id = $team_id;
            $team_message->sent_by = $user->first_name;
            $team_message->under_graduate = true;
            $team_message->fellowship_id = $user->fellowship_id;
            $team_message->save();
            

            // get phones that recieve the message and not recieve the message
            // $get_successfull_sent_phones = array();
            // , ['fellowship_id', '=', $user->fellowship_id]]
            $setting = Settings::where('name', '=', 'API_KEY')->first();
        
            if(!$setting) {
                return response()->json(['message' => '404 error found', 'error' => 'Api Key is not found'], 404);
            }
            $insert = [];
            $contains_name = Str::contains($request['message'], '{name}');

           // return response()->json(['message' => $contains_name]);

            if($contains_name) {
                for($i = 0; $i < count($contacts); $i++) {
                    $contact = $contacts[$i];
                    $replaceName = Str::replaceArray('{name}', [$contact->full_name], $request['message']);
                    // $under_graduate = Contact::where([['id', $contacts[$i]->id], ['is_under_graduate', 0]])->get();
                    if($contact->is_under_graduate) {
                        $sent_message = new sentMessages();
                        $sent_message->message = $replaceName;
                        $sent_message->sent_to = $contact->$phone_number;;
                        $sent_message->is_sent = false;
                        $sent_message->is_delivered = false;
                        $sent_message->sms_port_id = $getSmsPortId;
                        $sent_message->is_removed=false;
                        $sent_message->fellowship_id = $user->fellowship_id;
                        $sent_message->sent_by = $user->first_name;

                        if(!$sent_message->save()) {
                            $sent_message = new sentMessages();
                            $sent_message->message = $replaceName;
                            $sent_message->sent_to = $contact->$phone_number;;
                            $sent_message->is_sent = false;
                            $sent_message->is_delivered = false;
                            $sent_message->sms_port_id = $getSmsPortId;
                            $sent_message->is_removed=false;
                            $sent_message->fellowship_id = $user->fellowship_id;
                            $sent_message->sent_by = $user->first_name;
                            $sent_message->save();
                        }
                        $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone_number' => $contact->phone];
                    }
                }
            } else {
                for($i = 0; $i < count($contacts); $i++) {
                    $contact = $contacts[$i];
                    // $under_graduate = Contact::where([['id', $contacts[$i]->id], ['is_under_graduate', 0]])->get();
                    if($contact->is_under_graduate) {
                        $sent_message = new sentMessages();
                        $sent_message->message = $request['message'];
                        $sent_message->sent_to = $contact->phone_number;
                        $sent_message->is_sent = false;
                        $sent_message->is_delivered = false;
                        $sent_message->sms_port_id = $getSmsPortId;
                        $sent_message->fellowship_id = $user->fellowship_id;
                        $sent_message->sent_by = $user->first_name;

                        if(!$sent_message->save()) {
                            $sent_message = new sentMessages();
                            $sent_message->message = $request['message'];
                            $sent_message->sent_to = $contact->phone_number;
                            $sent_message->is_sent = false;
                            $sent_message->is_delivered = false;
                            $sent_message->sms_port_id = $getSmsPortId;
                            $sent_message->fellowship_id = $user->fellowship_id;
                            $sent_message->sent_by = $user->first_name;
                            $sent_message->save();
                        }
                        $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone' => $contact->phone_number];
                    }
                }
            }
            if($insert == []) {
                $team_message->delete();
                return response()->json(['message' => 'under graduate member is not found in '.$group->name. ' Group'], 404);
            }
            $negarit_message_request = array();
            $negarit_message_request['API_KEY'] = $setting->value;
            $negarit_message_request['campaign_id'] = $getSmsPortName->negarit_campaign_id;
            $negarit_message_request['messages'] = $insert;

        //    return response()->json(['message' => $negarit_message_request]);

                    //   sent_multiple_messages 
            $negarit_response = $this->sendPostRequest($this->negarit_api_url, 
                'api_request/sent_multiple_messages', 
                json_encode($negarit_message_request));
             $decoded_response = json_decode($negarit_response);

          // return response()->json(['message' => $decoded_response]);

            if($decoded_response) {
               
                if(isset($decoded_response->status)) {
                    $sent_message->is_sent = true;
                    $sent_message->is_delivered = true;
                    $sent_message->update();
                    return response()->json(['response' => $decoded_response], 200);
                } 
                else {
                    $sent_message->is_sent = true;
                    $sent_message->is_delivered = true;
                    $sent_message->update();
                    return response()->json(['response' => $decoded_response], 500);
                }
               // return response()->json(['message' => $decoded_response]);
            } else {
                return response()->json(['message' => 'Ooops! something went wrong', 'response' => $decoded_response], 500);
            }

        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getGroupMessage() {
        try {
            $user=auth('api')->user();
                   
                $group_message = GroupMessage::where([['under_graduate', '=', true], ['is_removed', '=', false],['fellowship_id', '=', $user->fellowship_id]])->orderBy('id', 'desc')->paginate(10);
                $count_group_message= $group_message->count();
                  //  return response()->json(['group_message' => $group_message], 200); json_decode($group_message[$i]->sent_by

                for($i = 0; $i < $count_group_message; $i++) {
                    $group = groups::find($group_message[$i]->group_id);
                    $group_message[$i]->sent_by = $user->first_name;
                    $group_message[$i]->group_id = $group->group_name;
                }
                return response()->json(['group_message' => $group_message,'count'=>$count_group_message], 200);
            } catch(Exception $ex) {
            return response()->json(['messag' => 'Ooops! something went wrong', 
                'error' => $ex->getMessage()], 500);
        } 
    }

    public function deleteGroupMessage($id) {
        try {
            $user=auth('api')->user();

                $group_message = GroupMessage::find($id);
                if($group_message instanceof getGroupMessage) { //&& $group_message->fellowship_id == $user->fellowship_id
                    $group_message->is_removed = 1;
                    if($group_message->update()) {
                        return response()->json(['message' => 'Group message removed successfully'], 200);
                    }
                    return response()->json(['message' => 'Ooops! something went wrong, please try again'], 500);
                }
                return response()->json(['error' => 'Group message is not found'], 404);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    // ||||||||**** Bulk Message ****||||||||||

    public function sendBulkMessage() {
        try {
                $user=auth('api')->user();

                $request = request()->only('port_name','message');
                $fellowship_message = new FellowMessages();
                $rule = [
                    'port_name' => 'required|string|max:250',
                    'message' => 'required|string|min:1',
                ];
                $validator = Validator::make($request, $rule);
                if($validator->fails()) {
                    return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 400);
                }           //  ['fellowship_id', '=', $user->fellowship_id]]
                $getSmsPortName = SmsPort::where('port_name', '=', $request['port_name'])->first();
                if(!$getSmsPortName) {
                    return response()->json(['message' => 'error found', 'error' => 'sms port is not found'], 404);
                }
                $getSmsPortId = $getSmsPortName->id;

                $fellowship_id = $user->fellowship_id;

                $fellowship = Fellowship::find($fellowship_id);
                if(!$fellowship) {
                    return response()->json(['message' => "can't send a fellowship message", 'error' => 'fellowship is not found'], 404);
                }

                $fellowship_message->message = $request['message'];
                $fellowship_message->fellowship_id = $fellowship_id;
                $fellowship_message->sent_by = $user->first_name;
                $fellowship_message->under_graduate = true;
                $fellowship_message->save();
     
                $contacts = Contact::where('fellowship_id', '=', $user->fellowship_id)->get();

                if(count($contacts) == 0) {
                    return response()->json(['message' => 'member is not found in '. $fellowship->university_name. ' fellowship'], 404);
                }
                                  
                $setting = Settings::where('name', '=', 'API_KEY')->first();
                if(!$setting) {
                    return response()->json(['message' => '404 error found', 'error' => 'Api Key is not found'], 404);
                }
                $insert = [];
                $contains_name = Str::contains($request['message'], '{name}');
                if($contains_name) {
                    for($i = 0; $i < count($contacts); $i++) {
                        $contact = $contacts[$i];
                        $replaceName = Str::replaceArray('{name}', [$contact->full_name], $request['message']);

                        if($contact->is_under_graduate) {

                            $sent_message = new sentMessages();
                            $sent_message->message = $replaceName;
                            $sent_message->sent_to = $contact->full_name;
                            $sent_message->is_sent = false;
                            $sent_message->is_delivered = false;
                            $sent_message->sms_port_id = $getSmsPortId;
                            $sent_message->fellowship_id = $user->fellowship_id;
                            $sent_message->sent_by = $user->first_name;

                            if(!$sent_message->save()) {

                                $sent_message = new sentMessages();
                                $sent_message->message = $replaceName;
                                $sent_message->sent_to = $contact->full_name;
                                $sent_message->is_sent = false;
                                $sent_message->is_delivered = false;
                                $sent_message->sms_port_id = $getSmsPortId;
                                $sent_message->fellowship_id = $user->fellowship_id;
                                $sent_message->sent_by = $user->first_name;
                                $sent_message->save();
                            }
                            $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone' => $contact->phone];
                        }
                    }
                } else {
                    for($i = 0; $i < count($contacts); $i++) {
                        $contact = $contacts[$i];

                        if($contact->is_under_graduate) {

                            $sent_message = new sentMessages();
                            $sent_message->message = $request['message'];
                            $sent_message->sent_to = $contact->full_name;
                            $sent_message->is_sent = false;
                            $sent_message->is_delivered = false;
                            $sent_message->sms_port_id = $getSmsPortId;
                            $sent_message->fellowship_id = $user->fellowship_id;
                            $sent_message->sent_by = $user->first_name;
                            if(!$sent_message->save()) {

                                $sent_message = new sentMessages();
                                $sent_message->message = $request['message'];
                                $sent_message->sent_to = $contact->full_name;
                                $sent_message->is_sent = false;
                                $sent_message->is_delivered = false;
                                $sent_message->sms_port_id = $getSmsPortId;
                                $sent_message->fellowship_id = $user->fellowship_id;
                                $sent_message->sent_by = $user->first_name;
                                $sent_message->save();
                            }
                            $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone' => $contact->phone_number];
                        }
                    }
                   // return response()->json(['message' => $sent_message]);
                }
                if($insert == []) {
                    $fellowship_message->delete();
                    return response()->json(['message' => 'under graduate members are not found in this fellowship'], 404);
                }
                $negarit_message_request = array();
                $negarit_message_request['API_KEY'] = $setting->value;
                $negarit_message_request['campaign_id'] = $getSmsPortName->negarit_campaign_id;
                $negarit_message_request['messages'] = $insert;

                $negarit_response = $this->sendPostRequest($this->negarit_api_url, 
                    'api_request/sent_multiple_messages', 
                    json_encode($negarit_message_request));
                $decoded_response = json_decode($negarit_response);
 
               // return response()->json(['message' => $decoded_response]);

                if($decoded_response) {
                    if(isset($decoded_response->status)) {
                        $sent_message->is_sent = true;
                        $sent_message->is_delivered = true;
                        $sent_message->update();
                        return response()->json(['response' => $decoded_response], 200);
                    } 
                    else {
                        $sent_message->is_sent = true;
                        $sent_message->is_delivered = true;
                        $sent_message->update();
                        return response()->json(['response' => $decoded_response], 500);
                    }
                    return response()->json(['message' => $decoded_response]);
                } else {
                    return response()->json(['message' => 'Ooops! something went wrong', 'response' => $decoded_response], 500);
                }
           
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getBulkMessage(){
        try{
            $user=auth('api')->user();
           
            $contactMessage = sentMessages::where([['is_removed', '=', false],['fellowship_id', '=', $user->fellowship_id]])->orderBy('id', 'desc')->paginate(10);
            $countMessages = $contactMessage->count();
            if($countMessages == 0) {
                return response()->json(['messages' => $contactMessage], 200);
            }
            for($i = 0; $i < $countMessages; $i++) {
                $contactMessage[$i]->sent_by = $contactMessage[$i]->sent_by;
            }
            return response()->json(['messages' => $contactMessage, 'number_of_messages' => $countMessages], 200);
        } catch(Exception $x) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    // ******** || *** Recieve Message from client *** || ************ 

    public function getNegaritRecievedMessage() {
         try{
        Logger('message', ['data'=>request()->all()]);
        // $user=auth('api')->user();
          $request = request()->only('message','sent_from','sender_name','received_date');
         $received = new RecievedMessage();
         $received->message = $request['message'];
         $received->sent_from =$request['sent_from'];
         $received->sender_name =$request['sender_name'];
         $received->received_date =$request['received_date'];
       //  $received->fellowship_id = $user->fellowship_id; 
         $received->save(); 
         return response()->json($request['message']);   
         }catch(Exception $e){
             return response()->json(['Error'=>'double message from same client']);
         }   
 }

 public function smsVote(){
    try {
        $user=auth('api')->user();

        $request = request()->only('port_name','message','start_date','end_date');
        $fellowship_message = new FellowMessages();
        $rule = [
            'port_name' => 'required|string|max:250',
            'message' => 'required|string|min:1',
            'start_date'=> 'required',
            'end_date' => 'required'
        ];
        $validator = Validator::make($request, $rule);
        if($validator->fails()) {
            return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 400);
        }         
        $getSmsPortName = SmsPort::where('port_name', '=', $request['port_name'])->first();
        if(!$getSmsPortName) {
            return response()->json(['message' => 'error found', 'error' => 'sms port is not found'], 404);
        }
        $getSmsPortId = $getSmsPortName->id;

        $fellowship_id = $user->fellowship_id;

        $fellowship = Fellowship::find($fellowship_id);
        if(!$fellowship) {
            return response()->json(['message' => "can't send a voting campaign", 'error' => 'fellowship is not found'], 404);
        }
       // $today = Carbon::parse(date('Y-m-d'));
       $date = date('Y-m-d'); 
       $date = $request['start_date'];
       $date2 = date('Y-m-d'); 
       $date2 = $request['end_date'];

        $vote = new smsVote();
        $vote->message = $request['message'];
        $vote->start_date = $date;
        $vote->end_date = $date2;
        $vote->save();
 
        $fellowship_message->message = $request['message'] && $request['start_date'];
        $fellowship_message->fellowship_id = $fellowship_id;
        $fellowship_message->sent_by = $user->first_name;
        $fellowship_message->under_graduate = true;
        $fellowship_message->save();

        $contacts = Contact::where('fellowship_id', '=', $user->fellowship_id)->get();

        if(count($contacts) == 0) {
            return response()->json(['message' => 'member is not found in '. $fellowship->university_name. ' fellowship'], 404);
        }
                          
        $setting = Settings::where('name', '=', 'API_KEY')->first();
        if(!$setting) {
            return response()->json(['message' => '404 error found', 'error' => 'Api Key is not found'], 404);
        } 
        $insert = [];
        $contains_name = Str::contains($request['message'], '{name}');
        if($contains_name) {
            for($i = 0; $i < count($contacts); $i++) {
                $contact = $contacts[$i];
                $replaceName = Str::replaceArray('{name}', [$contact->full_name], $request['message']);

                if($contact->is_under_graduate) {

                    $sent_message = new sentMessages();
                    $sent_message->message = $replaceName;
                    $sent_message->sent_to = $contact->full_name;
                    $sent_message->is_sent = false;
                    $sent_message->is_delivered = false;
                    $sent_message->sms_port_id = $getSmsPortId;
                    $sent_message->fellowship_id = $user->fellowship_id;
                    $sent_message->sent_by = $user->first_name;
                    
                    if(!$sent_message->save()) {

                        $sent_message = new sentMessages();
                        $sent_message->message = $replaceName;
                        $sent_message->sent_to = $contact->full_name;
                        $sent_message->is_sent = false;
                        $sent_message->is_delivered = false;
                        $sent_message->sms_port_id = $getSmsPortId;
                        $sent_message->fellowship_id = $user->fellowship_id;
                        $sent_message->sent_by = $user->first_name;
                        $sent_message->save();
                    }
                    $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone' => $contact->phone];
                }
            }
        } else {
            for($i = 0; $i < count($contacts); $i++) {
                $contact = $contacts[$i];

                if($contact->is_under_graduate) {

                    $sent_message = new sentMessages();
                    $sent_message->message = $request['message'];
                    $sent_message->sent_to = $contact->full_name;
                    $sent_message->is_sent = false;
                    $sent_message->is_delivered = false;
                    $sent_message->sms_port_id = $getSmsPortId;
                    $sent_message->fellowship_id = $user->fellowship_id;
                    $sent_message->sent_by = $user->first_name;
                    if(!$sent_message->save()) {

                        $sent_message = new sentMessages();
                        $sent_message->message = $request['message'];
                        $sent_message->sent_to = $contact->full_name;
                        $sent_message->is_sent = false;
                        $sent_message->is_delivered = false;
                        $sent_message->sms_port_id = $getSmsPortId;
                        $sent_message->fellowship_id = $user->fellowship_id;
                        $sent_message->sent_by = $user->first_name;
                        $sent_message->save();
                    }
                    $insert[] = ['id' => $i+1, 'message' => $sent_message->message, 'phone' => $contact->phone_number];
                }
            }
           // return response()->json(['message' => $sent_message]);
        }
        if($insert == []) {
            $fellowship_message->delete();
            return response()->json(['message' => 'under graduate members are not found in this fellowship'], 404);
        }
        // start date if condition with end date 
        if($sent_message->created_at >= $request['start_date']){

        $negarit_message_request = array();
        $negarit_message_request['API_KEY'] = $setting->value;
        $negarit_message_request['campaign_id'] = $getSmsPortName->negarit_campaign_id;
        $negarit_message_request['messages'] = $insert;
          
        $negarit_response = $this->sendPostRequest($this->negarit_api_url, 
            'api_request/sent_multiple_messages', 
            json_encode($negarit_message_request));
        $decoded_response = json_decode($negarit_response);
        }
       // return response()->json(['message' => $decoded_response]);

        if($decoded_response) {
            if(isset($decoded_response->status)) {
                $sent_message->is_sent = true;
                $sent_message->is_delivered = true;
                $sent_message->update();
                return response()->json(['response' => $decoded_response], 200);
            } 
            else {
                $sent_message->is_sent = true;
                $sent_message->is_delivered = true;
                $sent_message->update();
                return response()->json(['response' => $decoded_response], 500);
            }
            return response()->json(['message' => $decoded_response]);
        } else {
            return response()->json(['message' => 'Ooops! something went wrong', 'response' => $decoded_response], 500);
        }
   
} catch(Exception $ex) {
    return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
}
 }

 public function getVote(){
     // from Recieved message table 
     // get messages between start date and end date
     // double message(vote) from same contact is not allowed
     // vote from non contact is not allowed
     // use matching keyword.
     $getvote =  DB::table('sms_votes')
     ->select('start_date','end_date')
     ->first();
     $start_date = $getvote->start_date;
     $end_date = $getvote->end_date;
          //$collect = RecievedMessage::where('start_date','>=',);
          $message = DB::table('recieved_messages')
          ->select('message')
          ->whereBetween('created_at', [$start_date, $end_date])
          ->value('message');

          $keyA = DB::table('recieved_messages')
          ->where('message','=','A')
          ->whereBetween('created_at', [$start_date, $end_date])
          ->count();
  
          $keyB = DB::table('recieved_messages')
          ->where('message','=','B')
          ->whereBetween('created_at', [$start_date, $end_date])
          ->count();

          $keyC = DB::table('recieved_messages')
          ->where('message','=','C')
          ->whereBetween('created_at', [$start_date, $end_date])
          ->count();


          return response()->json([[$message],[$keyA],[$keyB],[$keyC],[$start_date],[$end_date]]);
          
 }
}

