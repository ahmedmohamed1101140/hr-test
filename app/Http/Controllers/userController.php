<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\User;
use App\Department;
use App\Resort;
use App\Role;
use App\UserData;



use App\ldapUsers;
use App\ldapHelperMethods;

use App\Http\Requests\userDataValidation;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

	

		//$users = User::latest()->get();
    $users = User::latest()->where('user_name','!=','0')->where('status','Enabled')->get();
	
	$ldap = new ldapUsers();
	
        //dd($ldap->user_enable($users[10]->user_name));


	return view('users.index', compact('users'));
    }


    public function getDisbleUser(){
        $users = User::latest()->where('user_name','!=','0')->where('status','Disabled')->get();
        return view('users.index', compact('users'));
    }

    public function changeStatusApproved(Request $request, $id){

        // $request->id("18") -> It's Selected
        // $id -> It's user id
        //$userData = UserData::where('user_id',$request->id)->get();

        //$userData = UserData::where('user_id',$id)->get();
        //dd($request->all());

        $userData = UserData::findOrFail($request->id);
        if ($userData->is_approved == 1) {
            $userData->is_approved = 0;
        }else{
            $userData->is_approved = 1;
        }

        $userData->save();
        session()->flash('success','User Updated Successfully');
        return redirect()->back();
    }

    public function changeStatus(Request $request){

	dd("dfdf");
        $user = User::where('user_id',$request->id)->first();
        if(!$user){
            session()->flash('warning','User Not Found');
            return redirect()->back();
        }

        $ldap = new ldapUsers();
	dd($ldap->user_disable($user->user_name));
        if($user->status == 'Enabled'){
            $user->status = 'Disabled';
            $ldap->user_disable($user->user_name);
        }
        else{
            $user->status = 'Enabled';
            $ldap->user_enable($user->user_name);
            //dd($ldap->user_enable($user->user_name));
        }
        $user->save();
        session()->flash('success','User Updated Successfully');
        return redirect()->back();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $departments = Department::all();
        $groups = Group::all();
        $authUserID = User::where('user_id',Session::get('user')[0]->user_id)->first();

        $userData = UserData::select('resort_id')->where('user_id',$authUserID->id)->get();
        if(count($userData) != 0){
            $resorts = Resort::whereIn("id",$userData)->get();
        }
        else{
            $resorts = Resort::where('id','=','0')->get();
        }
        $roles = Role::all();

	return view('users.create', compact(
            'departments',
             'resorts',
             'groups',
             'roles'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request->all());
       //store to data base
       $ldap = new ldapUsers();

       $first_username = strtolower(mb_substr($request['first_name'], 0, 2, "UTF-8") .
       mb_substr($request['last_name'], 0, 2, "UTF-8"));

       $second_username = strtolower(mb_substr($request['first_name'], 0, 1, "UTF-8") .
       mb_substr($request['last_name'], 0, 3, "UTF-8"));


       $all_username = $ldap->all_users();

       foreach ($all_username as $username) {
           if ($first_username == $username) {
               $request['user_name'] = $second_username;
               dump($request['user_name']);
           }else{
               $request['user_name'] = $first_username;
           }
       }


        $new_user = $ldap->user_create(
           array(
                "user_name"     => $request['user_name'],
                "first_name"    => $request['first_name'],
                "last_name"     => $request['last_name'],
                "email"         => $request['user_name']."@regenbogen-ag.de",
                "container"     => array("CN=Users")
           ));

       if(!$new_user){
           session()->flash('warning','Failed to create user');
           return redirect(route('user.index'));

       }

        $request['user_id'] = Str::random(6);
        $user = User::create($request->all());
        $userData = UserData::create([
            'user_id' => $user->id,
            'group_id' => $request['group_id'],
            'role_id' => $request['role_id'],
            'resort_id' => $request['resort_id']
        ]);

        session()->flash('success','User Added Successfully');
        return redirect(route('user.edit',$user->id));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $user = User::where('user_id',$id)->first();
        if($user == null){
            session()->flash('warning','User Not Found');
            return redirect(route('user.index'));
        }
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $user_data = UserData::where('user_id',$id)->get();
        $groups = Group::all();
        $roles = Role::all();
        $resorts = Resort::all();


        return view('users.edit', compact(
            'groups',
            'user',
            'user_data',
            'resorts',
            'roles'
        ));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //1-get user DataTable
        //2-Update
        //return true
        $authUserID = User::where('user_id',Session::get('user')[0]->user_id)->get();
        //dd($request->all());
        $user = User::where('user_id',$id)->first();
        if ($authUserID[0]->is_admin) {

            if($user){
                $userData = UserData::where('user_id',$user->id)->get();
                foreach ($userData as $key) {
                    if($key->resort_id == $request->resort_id &&
                    $key->group_id == $request->group_id &&
                    $key->role_id == $request->role_id)
                    {
                        session()->flash('warning','User allready has this data');
                        return redirect()->back();
                    }
                }
                $request['is_approved'] = 1;
                $user->update($request->all());

            }
            else{
                session()->flash('warning','User Not Found');
                return redirect()->back();
            }


            $userData = new UserData();
            $userData->user_id = $user->id;
            $userData->group_id = $request->group_id;
            $userData->resort_id = $request->resort_id;
            $userData->role_id = $request->role_id;
            $userData->is_approved = $request->is_approved;
            $userData->save();
            session()->flash('success','User Updated Successfully');
            return redirect()->back();
        } // end is_admin
        else {
            if($user){
                $userData = UserData::where('user_id',$user->id)->get();
                foreach ($userData as $key) {
                    if($key->resort_id == $request->resort_id &&
                    $key->group_id == $request->group_id &&
                    $key->role_id == $request->role_id)
                    {
                        session()->flash('warning','User allready has this data');
                        return redirect()->back();
                    }
                }
                $request['is_approved'] = 0;
                $user->update($request->all());

            }
            else{
                session()->flash('warning','User Not Found');
                return redirect()->back();
            }


            $userData = new UserData();
            $userData->user_id = $user->id;
            $userData->group_id = $request->group_id;
            $userData->resort_id = $request->resort_id;
            $userData->role_id = $request->role_id;
            $userData->is_approved = $request->is_approved;
            $userData->save();
            session()->flash('success','User Updated Successfully');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $ldap = new ldapUsers();
        $ldap->user_disable($id);

        $user = User::where('user_name',$id)->first();
        $user->delete();

        session()->flash('success','User Deleted Successfully');
        return redirect()->back();

    }

    public function deleteUserData($id){

	    $userData = UserData::findOrFail($id);
        $userData->delete();
        session()->flash('success','User Data Deleted Successfully');
        return redirect()->back();
    }

}
