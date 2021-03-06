<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
    //return view('welcome');
})->name('master');

Route::post('/user-update-components/{id}','userController@updateComponents')->name('user.updateComponents');


Route::group(['middleware' => ['web','check_auth','shared_variables','route_permissions']],function(){
    Route::get('/user-disabled','userController@getDisbleUser')->name('user.disabled');
    Route::post('/user-change-status','userController@changeStatus')->name('user.changeStatus');
    Route::post('/user-change-status-approved/{id}','userController@changeStatusApproved')->name('user.changeStatusApproved');

        Route::resource('/user','userController');
        Route::resource('/permission', 'PermissionController');
        Route::resource('/group', 'GroupController');
        Route::resource('/role', 'RoleController');
        Route::resource('/resort', 'ResortController');
        Route::resource('/resort-users', 'ResortUserController');
        Route::get('/resort-groups/{id}','ResortController@resortGroup')->name('resortGroup.index');
        Route::get('/resort-creategroups/{id}','ResortController@resortCreateGroup')->name('resortGroup.create');
        Route::get('/group-roles/{id}','GroupController@groupCreateRoles')->name('groupRoles.create');
        Route::get('/group-createroles/{id}','GroupController@groupRoles')->name('groupRoles.index');
        Route::delete('/user-data/{id}','userController@deleteUserData')->name('userData.destroy');

        Route::get('dropdownlist','DropdownController@index');
        Route::get('get-group-list/{id}','DropdownController@getGroupList');
        Route::get('get-role-list/{id}','DropdownController@getRoleList');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
