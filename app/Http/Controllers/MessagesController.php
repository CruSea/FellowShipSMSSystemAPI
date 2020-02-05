<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\SmsPort;
use App\Contact;

class MessagesController extends Controller
{
    protected $negarit_api_url;

    public function __construct() {
       // $this->middleware('auth:api');
        $this->negarit_api_url = 'https://api.negarit.net/api/';
    }

    public function sendContactMessage() {
        try {

            $request = request()->only('message', 'sent_to', 'port_name');

            $rule = [
                'message' => 'required|string|min:1',
                'port_name' => 'required|string|max:255',
                'sent_to' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:13',
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
            $setting = Setting::where('name', '=', 'API_KEY')->first();
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
            if(strlen($phone_number) > 13 || strlen($phone_number) < 13) {
                return response()->json(['message' => 'validation error', 'error' => 'phone number length is not valid'], 400);
            }

            $contains_name = Str::contains($request['message'], '{name}');
            $contact = Contact::where('phone', '=', $phone_number)->first();
            if($contact instanceof Contact) {
                if($contains_name) {
                    $replaceName = Str::replaceArray('{name}', [$contact->full_name], $request['message']);

                    $sentMessage = new SentMessage();
                    $sentMessage->message = $replaceName;
                    $sentMessage->sent_to = $contact->full_name;
                    $sentMessage->is_sent = false;
                    $sentMessage->is_delivered = false;
                    $sentMessage->sms_port_id = $getSmsPortId;
                    $sentMessage->fellowship_id = $user->fellowship_id;
                    $sentMessage->sent_by = $user;
                } else {
                    $sentMessage = new SentMessage();
                    $sentMessage->message = $request['message'];
                    $sentMessage->sent_to = $contact->full_name;
                    $sentMessage->is_sent = false;
                    $sentMessage->is_delivered = false;
                    $sentMessage->sms_port_id = $getSmsPortId;
                    $sentMessage->fellowship_id = $user->fellowship_id;
                    $sentMessage->sent_by = $user;
                }
            } else {
                $sentMessage = new SentMessage();
                $sentMessage->message = $request['message'];
                $sentMessage->sent_to = $phone_number;
                $sentMessage->is_sent = false;
                $sentMessage->is_delivered = false;
                $sentMessage->sms_port_id = $getSmsPortId;
                $sentMessage->fellowship_id = $user->fellowship_id;
                $sentMessage->sent_by = $user;
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
}
