<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('sample-restful-apis', 'EmailController@mail');


// >>>>>>>>>>>>>>>>>>>> Authentication Routes <<<<<<<<<<<<<<<<<<<<

Route::post('/register', [
    'uses'=> 'UserController@register'
 ]);
 
 
 Route::post('/login', [
     'uses'=> 'Api\AuthController@login',
  ]);

  // *********||||||||| Guard ||||||||||************

/*Route::middleware('auth:api')->get() {
   
});*/
 Route::post('/assignRole/{value}/{id}', [
     'uses' => 'AppController@postAdminAssignRoles',
 ]);
 
 // >>>>>>>>>>>>>>>>>>> Admin Route <<<<<<<<<<<<<<<<<<<<<<<<

 Route::get('/getAdmin',[
     'uses' => 'RegisterAdminController@getAdmins'
 ]);

 Route::delete('/deleteAdmin/{id}',[
     'uses' => 'RegisterAdminController@deleteAdmin'
 ]);
     
 Route::get('/admin',[
     'uses' => 'AppController@adminPage',
     'as' => 'admin',
     'middleware' => 'roles',
     'roles' => ['Admin']
 ]);

// >>>>>>>>>>>>>>>>  contact Routes   <<<<<<<<<<<<<<<<<<<<<<<

Route::group(['prefix' => 'contact'], function() {
    Route::post('/', [
        'uses' => 'ContactController@addContact'
    ]); 

    Route::get('/{id}', [
        'uses' => 'ContactController@getContact'
    ]);
    Route::delete('/{id}', [
        'uses' => 'ContactController@deleteContact'
    ]);

    Route::patch('/{id}', [
        'uses' => 'ContactController@updateContact',
    ]);
});


Route::get('/contacts/{id}', [
    'uses' => 'ContactController@getContacts'
]);

// >>>>>>>>>>>>>>>>>>> Get Admins Route <<<<<<<<<<<<<<<<<<<<<<<

Route::get('/getAdmins',[
        'uses' => 'RegisterAdminController@getAdmins',
]);

// >>>>>>>>>>>>>>>>> Group Routes  <<<<<<<<<<<<<<<<<<<<<<<<<<<

Route::group(['prefix' => 'group'], function() {

    Route::post('/', [
        'uses' => 'GroupController@addGroup',
    ]);
    Route::get('/{id}', [
        'uses' => 'GroupController@getGroup',
    ]);
    Route::delete('/{id}',[
        'uses' => 'GroupController@deleteGroup',
    ]);
   
 });

 Route::group(['prefix' => 'groupcontact'], function(){
    Route::post('/{id}', [
        'uses' => 'GroupedContactController@addGroupedContact',
    ]);
    Route::get('/{id}', [
        'uses' => 'GroupedContactController@getGroupedContact',
    ]);
    Route::delete('/{id}',[
        'uses' => 'GroupedContactController@deleteGroupedContact',
    ]);
    Route::patch('/{id}', [
        'uses' => 'GroupedContactController@updateGroupedContact'
    ]);

 });


 Route::get('/groups', [
    'uses' => 'GroupController@getGroups',
]);

Route::get('/getGender',[
     'uses' => 'GroupedContactController@getGenders'
]);

 // >>>>>>>>>>>>>>>>>>> Fellow Routes  <<<<<<<<<<<<<<<<<<<<<<<

 Route::group(['prefix'=> 'fellow'],function() {
      
    Route::post('/', [
        'uses' => 'FellowshipController@addFellow'
    ]);
 });
/*Route::post('/register', [
        'uses' => 'RegisterAdminController@RegisterAdmin'
    ]); */

// >>>>>>>>>>>>>>>>>>>>> Dashboard  <<<<<<<<<<<<<<<<<<<<<<<<<<

Route::get('/under_graduates_number', [
        'uses' => 'DashboardController@underGraduateMembersNumber',
    ]);   
Route::get('/totalGroupedContact/{id}',[
       'uses' => 'GroupContactCountController@GroupMemberCount'
]);    
 
Route::get('/totalGroups',[
    'uses' => 'DashboardController@numberOfGroups',
]);

Route::get('/gendercount',[
    'uses' => 'DashboardController@getGenderCount'
]);

Route::get('/current_user',[
    'uses' => 'DashboardController@current_user'
]);

Route::get('/current_univ',[
    'uses' => 'DashboardController@current_univ'
]);

Route::get('/count_sentMessage',[
     'uses' => 'DashboardController@count_sentMessage'
]);

Route::get('/messageCost',[
     'uses' => 'DashboardController@messageCost'
]);
// --------------------- Super Dashboard ----------------- //

Route::get('/TotalnumberOfGroups',[
      'uses' => 'SuperDashboardController@TotalnumberOfGroups'
]);
Route::get('/campusTotalContact',[
    'uses' => 'SuperDashboardController@campusTotalContact'
]);
Route::get('/total_sentMessage',[
    'uses' => 'SuperDashboardController@count_sentMessage'
]);
Route::get('/all_group_message',[
    'uses' => 'SuperDashboardController@total_group_message'
]);
Route::get('/TotalunderGraduateMembers',[
    'uses' => 'SuperDashboardController@TotalunderGraduateMembersNumber'
]);

Route::get('/totalMessageCost',[
    'uses'=> 'SuperDashboardController@totalMessageCost'
]);

Route::get('/get_sentmsg',[
    'uses'=> 'SuperDashboardController@get_sentmsg'
]);

Route::get('/get_recivemsgs',[
    'uses' => 'SuperDashboardController@get_Recivemsgs'
]);

//...........................................................
Route::post('/importContact',[
     'uses' => 'ContactController@importContact' 
]);

Route::get('/exportContact',[
     'uses' => 'ContactController@exportContact',

]);
Route::get('/importContact',[
     'uses'=> 'ContactController@importContact',
]);

Route::get('/exportGroupedContact',[
    'uses' => 'GroupedContactController@exportGroupedContact'
]);

Route::get('/getprofile/{id}',[
    'uses'=> 'ContactController@getProfile',
]);

Route::post('/upload_photo/{id}',[
    'uses'=> 'ContactController@upload_photo'
]);

Route::delete('/deleteProfile/{id}',[
    'uses'=>'ContactController@deleteProfile'
]);
// ************* |||| Email |||| ************


Route::get("/email", [
    'uses' => 'sendMailController@sendMail'
]);

Route::get('/sendResetLink/{email}',[
    'uses' => 'sendMailController@sendResetLink'
]);

Route::get('/sendmail/{email}',[
    'uses' => 'mailcontroller@send'
]);
//  {{password Reset}}
Route::get('/passwordReset/{email}/{pass}',[
     'uses' => 'PasswordResetController@passwordReset'
]);

// **************** ||||||| Messaging Routes ||||||| ***************


Route::group(['prefix' => 'message'], function(){
    Route::post('/', [
        'uses' => 'MessageController@sendContactMessage',
    ]);
    Route::get('/{id}', [
        'uses' => 'MessageController@getContactMessage',
    ]);
    Route::delete('/{id}', [
        'uses' => 'MessageController@removeContactMessage',
    ]);
    Route::post('/search', [
        'uses' => "MessageController@searchContactMessage",
    ]);
});

//******************** |||||||| Messaging ||||||||| ********************

Route::group(['prefix' => 'message'], function(){
    Route::post('/', [
        'uses' => 'MessagesController@sendContactMessage',
    ]);
    Route::get('/', [
        'uses' => 'MessagesController@getContactsMessage',
    ]);
    Route::delete('/{id}', [
        'uses' => 'MessagesController@removeContactMessage',
    ]);
    Route::post('/search', [
        'uses' => "MessagesController@searchContactMessage",
    ]);
});


//---------------||||||||| Group Message ||||||||||-------------------

Route::group(['prefix' => 'group-message'], function() {
    Route::post('/', [
        'uses' => 'MessagesController@sendGroupMessage'
    ]);
    Route::get('/', [
        'uses' => 'MessagesController@getGroupMessage'
    ]);
    Route::delete('/{id}', [
        'uses' => 'MessagesController@deleteTeamMessage'
    ]);
    Route::post('/search', [
        'uses' => 'MessagesController@searchTeamMessage',
    ]);
});
Route::get('/group-messages', [
    'uses' => 'MessagesController@getTeamMessage'
]);

//************Bulk Message**************/

Route::post('/sendBulkMessage', [
    'uses' => 'MessagesController@sendBulkMessage'
]);

Route::get('/getBulkMessage',[
    'uses' => 'MessagesController@getBulkMessage'
]);

Route::post('/smsVote',[
    'uses' => 'MessagesController@smsVote'
]);

Route::get('/getVote',[
     'uses' => 'MessagesController@getVote'
]);

//************** Negarit Recieved Messages****************/

Route::post('/recieveNegaritMessage',[
    'uses' => 'MessagesController@getNegaritRecievedMessage'
]);  
//****************>>>>>>>>> Messaging Port <<<<<<<<<<<<*****************/

Route::post('/storeSmsPort',[
        'uses' => 'NegaritController@storeSmsPort'
]);

Route::get('/sms-ports', [
    'uses' => 'NegaritController@getSmsPorts',
]);

Route::get('/port_name', [
    'uses' => 'SettingController@getSmsPortName',
]);
// *****************>>>>>>>>> Settings <<<<<<<<<<<<<******************/

Route::group(['prefix' => 'setting'], function () {
    Route::post('/', [
        'uses' => 'SettingController@createSetting', // worked
    ]);
    Route::get('/{id}', [
        'uses' => 'SettingController@getSetting', // worked
    ]);
    Route::patch('/{id}', [
        'uses' => 'SettingController@updateSetting',
    ]);
    Route::delete('/{id}', [
        'uses' => 'SettingController@removeSetting',
    ]);
});

Route::get('/settings', [
    'uses' => 'SettingController@getSettings', // worked
]);
Route::get('/campaigns', [
    'uses' => 'SettingController@getCampaigns',
]);
Route::get('/get-sms-ports', [
    'uses' => 'SettingController@getSmsPorts',
]);


// *********** || *** Scheduled Message *** || *******

Route::post('/addMessageForGroup',[
    'uses' => 'ScheduledMessageController@addMessageForGroup'
]);

Route::get('/get_msg',[
    'uses' => 'DashboardController@get_msg'
]);