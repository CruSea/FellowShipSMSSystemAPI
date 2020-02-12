<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\groups;
use App\ScheduleMessage;
use App\GroupContact;
use App\SmsPort;
use App\Settings;
use App\Contact;
use Carbon\Carbon;
use Input;

class ScheduledMessageController extends Controller
{
    public function addMessageForGroup() {
    	try {
            $user=auth('api')->user();

    			$request = request()->only('port_name', 'type', 'start_date', 'end_date', 'sent_time', 'group','message');
    			$rule = [
					'port_name' => 'required|string|max:255',
    				'type' => 'required|string|min:1',
    				'start_date' => 'required|date_format:Y-m-d|after:today',
    				'end_date' => 'required|date_format:Y-m-d|after:tomorrow',
    				'sent_time' => 'required|date_format:H:i',
    				'group' => 'required|string|min:1',
    				'message' => 'required|string|min:1',
    			];
    			$validator = Validator::make($request, $rule);
    			if($validator->fails()) {
    				return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 400);
    			}
    			$group = groups::where([['group_name', '=', $request['group']], ['fellowship_id', '=', $user->fellowship_id]])->first();
    			if(!$group) {
    				return response()->json(['error' => 'group is not found'], 404);
    			}                       //  ['fellowship_id', '=', $user->fellowship_id]
    			$sms_port = SmsPort::where('port_name', '=', $request['port_name'])->first();
    			if(!$sms_port) {
    				return  response()->json(['error' => 'sms port is not found'], 404);
    			}
    			if(Carbon::parse($request['start_date'])->diffInDays(Carbon::parse($request['end_date']), false) < 0) {
    				return response()->json(['error' => "end date can't be sooner than start date"], 400);
    			}
    			$group_id = $group->group_id;
    			$sms_port_id = $sms_port->id;

                $contacts = Contact::whereIn('contact_id', GroupContact::where('contacts_id','=', 
                $group_id)->select('contacts_id')->get())->get();

               // return response()->json(['message' => $contacts]);

                if(count($contacts) == 0) {
                    return response()->json(['message' => 'member is not found in '.$group->group_name. ' group'], 404);
                }

                $api_key = $sms_port->api_key;
                // check setting existance
                $setting = Settings::where([['name', '=', 'API_KEY'],['value', '=', $api_key]])->exists();
                if(!$setting) {
                    return response()->json(['error' => 'API_KEY is not found'], 404);
                }

    			$shceduled_message = new ScheduleMessage();
    			$shceduled_message->type = $request['type'];
    			$shceduled_message->start_date = $request['start_date'];
    			$shceduled_message->end_date = $request['end_date'];
    			$shceduled_message->sent_time = $request['sent_time'];
    			$shceduled_message->message = $request['message'];
                $shceduled_message->group_id = $group_id;
                $shceduled_message->fellowship_id = $user->fellowship_id;
    			$shceduled_message->sent_to = $group->group_name.' group';
    			$shceduled_message->get_fellowship_id = $user->fellowship_id;
    			$shceduled_message->sms_port_id = $sms_port_id;
    			// $shceduled_message->key = $key;
    			$shceduled_message->sent_by = $user->first_name;
    			if($shceduled_message->save()) {
    				return response()->json(['message' => 'message scheduled successfully'], 200);
    			} else {
    				return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'scheduled message is not sent, please try again'], 500);
    			}
    	
    	} catch(Exception $ex) {
    		return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
    	}
    }
}
