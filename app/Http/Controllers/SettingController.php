<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\SmsPort;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Settings;

class SettingController extends Controller
{
    protected $root_url;
    public function __construct() {
         // $this->middleware('auth:api');
        $this->root_url = "https://api.negarit.net/api/";

    }

    public function createSetting() {
        try {
            $request = request()->only('value');
            $rule = [
                // 'name' => 'required|string|unique:settings',
                'value' => 'required|string|min:1'
            ];
            $validator = Validator::make($request, $rule);
            if($validator->fails()) {
                return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 500);
            }
            $old_setting = Settings::where('name', '=', "API_KEY")->first();
            if($old_setting instanceof Setting) {
                $old_setting->name = $old_setting->name;
                $old_setting->fellowship_id = $old_setting->fellowship_id;
                $old_setting->value = $request['value'];
                if($old_setting->update()) {
                    return response()->json(['message' => 'setting successfully updated'], 200);
                } 
                else {
                    return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'failed to create setting'], 500);
                }
            } else {
                $new_setting = new Settings();
                $new_setting->name = "API_KEY";
              //  $new_setting->fellowship_id = $user->fellowship_id;
                $new_setting->value = $request['value'];
                if($new_setting->save()) {
                    return response()->json(['message' => 'setting successfully created'], 200);
                } 
                else { 
                    return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'failed to create setting'], 500);
                }
            }
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getSetting($id) {
        try {
            $setting = Settings::find($id);
            if($setting instanceof Settings) {
                return response()->json(['setting',$setting], 200);
            }
            return response()->json(['message' => '404 error found', 'error' => 'setting was not fuond'], 404);
        } catch(Exception $ex) {
             return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getSettings() {
        try {
           
            $settings = Settings::all();
            $countSetting = $settings->count();
            if($countSetting == 0) {
                return response()->json(['settings' => $settings], 200);
            }
            return response()->json(['settings' => $settings], 200);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }
    public function updateSetting($id) {
        try {
           
            $request = request()->only('value');
            $old_setting = Settings::find($id);

            
            if($old_setting instanceof Settings) {
                $rule = [
                    'value' => 'required|string|min:1',
                ];
                $validator = Validator::make($request, $rule);
                if($validator->fails()) {
                    return response()->json(['message' => 'validation error', 'error' => $validator->messages()], 500);
                }
                $old_setting->value = isset($request['value']) ? $request['value'] : $old_setting->value;
                if($old_setting->update()) {
                    return response()->json(['message' => 'setting updated successfully'], 200);
                }
                return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'setting is not updated successfully'], 500);
            }
            return response()->json(['message' => '404 error found', 'error' => 'setting was not found'], 404);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    // ==================================================================

    public function getCampaigns() {
        try {
          
            $API_KEY = Settings::where(['name', '=', 'API_KEY'])->first();
            // return $this->root_url.'api_request/campaigns?API_KEY='.$API_KEY->value;
            if($API_KEY instanceof Settings) {
                $response = $this->sendGetRequest('https://api.negarit.net/api/', 'api_request/campaigns?API_KEY='.$API_KEY->value);
                $decoded_response = json_decode($response);
                if($decoded_response) {
                    if(isset($decoded_response->campaigns)) {
                        $campaigns = $decoded_response->campaigns;
                        for($i = 0; $i < count($campaigns); $i++) {
                            $campaign[] = ['id' => $campaigns[$i]->id, 'name' => $campaigns[$i]->name];
                        }
                        return response()->json(['campaigns' => $campaign], 200);
                    } else {
                        return response()->json(['response' => count($decoded_response)], 500);
                    }
                } else {
                    return response()->json(['message' => 'Ooops! something went wrong', 'response' => $decoded_response], 500);
                }
            } else {
                return response()->json(['message' => '404 error found', 'error' => 'API Key was not found'], 404);
            }
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }

    public function getSmsPorts() {
        try {
            
            $setting = Settings::where(['name', '=', 'API_KEY'])->first();
            if($setting instanceof Settings) {
                $API_KEY = $setting->value;
                $negarit_response = $this->sendGetRequest($this->root_url, 'api_request/sms_ports?API_KEY='.$API_KEY);
                $decoded_response = json_decode($negarit_response);
                if($decoded_response) {
                    if(isset($decoded_response->sms_ports)) {
                        $smsPorts = $decoded_response->sms_ports;
                        for($i = 0; $i < count($smsPorts); $i++) {
                            $sms_name[] = ['id' => $smsPorts[$i]->id, 'name' => $smsPorts[$i]->name];
                        }
                        return response()->json(['sms ports' => $sms_name], 200);
                    }
                    return response()->json(['response' => $decoded_response], 500);
                }
                return response()->json(['message' => 'error found', 'error' => $decoded_response], 500);
            }
            return response()->json(['message' => 'error found', 'error' => 'setting was not found'], 404);
        } catch(Exception $ex) {
            return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }
}
