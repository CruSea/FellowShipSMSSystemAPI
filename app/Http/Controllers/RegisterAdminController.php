<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\User;


class RegisterAdminController extends Controller
{
    public function __construct() {

        //$this->middleware('auth:api');

    }
        public function getAdmins() {
            try {
                 $admins = User::all();
               //  $contacts = Users::where([['is_under_graduate', '=', 1]])->orderBy('contact_id')->paginate(10);
                return response()->json(['contacts' => $admins], 200);
            } catch(Exception $ex) {
                return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex], 500);
            }
        }

        public function deleteAdmin($id){
            try {
                
                  $admin = User::find($id);
      
                  if($admin instanceof User) {
      
                      if($admin->delete()) {
                          return response()->json(['message' => 'Admin deleted successfully'], 200);
                      }
                      return response()->json(['message' => 'Ooops! something went wrong', 'error' => 'unable to delete Admin'], 500);
                  } 
                  return response()->json(['message' => 'an error occurred', 'error' => 'Admin is not found'], 404);
              } catch(Exception $ex) {
                  return response()->json(['message' => 'Ooops! something went wrong', 'error' => $ex->getMessage()], 500);
              }
        }


    }
