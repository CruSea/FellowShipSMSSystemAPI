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
    'uses'=> 'Api\AuthController@register'
 ]);
 
 
 Route::post('/login', [
     'uses'=> 'Api\AuthController@login'
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

Route::get('/campusTotalContact',[
    'uses' => 'DashboardController@campusTotalContact',
]);

Route::get('/gendercount',[
    'uses' => 'DashboardController@getGenderCount'
]);

Route::post('/importContact',[
     'uses' => 'ContactController@importContact' 
]);

Route::get('/exportContact',[
     'uses' => 'ContactController@exportContact'
]);

Route::get('/exportGroupedContact',[
    'uses' => 'GroupedContactController@exportGroupedContact'
]);

Route::get('/sendmail',[
    'uses' => 'sendMailController@sendMail'
]);


Route::get("/email", [
    'uses' => 'sendMailController@sendMail'
]);

