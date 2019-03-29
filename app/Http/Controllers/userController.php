<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Group;
use Illuminate\Http\Request;
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

    $ldap = new ldapUsers();
    $ldapH = new ldapHelperMethods();


    //dd($ldapH->get_all_disabled_user());

    $users = User::latest()->where('user_name','!=','0')->get();
	return view('users.index', compact('users'));
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
        $resorts = Resort::all();
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
       //store to data base
        $ldap = new ldapUsers();

        //$request['user_id'] = Str::random(6);

        $first_username = strtolower(mb_substr($request['first_name'], 0, 2, "UTF-8") .
        mb_substr($request['last_name'], 0, 2, "UTF-8"));

        $second_username = strtolower(mb_substr($request['first_name'], 0, 1, "UTF-8") .
        mb_substr($request['last_name'], 0, 3, "UTF-8"));

        $request['user_name'] = $second_username;
        //dd($second_username,$first_username);
        $userCreated = false;
        for($i=0 ; $i < 2; $i ++){
            $new_user = $ldap->user_create(
                array(
                    "user_name"     => $request['user_name'],
                    "first_name"    => $request['first_name'],
                    "last_name"     => $request['last_name'],
                    "email"         => $request['user_name']."@regenbogen-ag.de",
                    "container"     => array("CN=Users")
            ));
            dump($new_user,$request['user_name']);
            if($new_user != false){
                dd('sda');
                $userCreated = true;
                break;
            }

        $request['user_name'] = $second_username;
        //dd($request['user_name']);
        }
        if(!$userCreated){
            session()->flash('warning','Failed to create user');
            return redirect(route('user.index'));

        }
        $request['user_id'] = 'ahmeed';
        $user = User::create($request->all());

        session()->flash('success','User Added Successfully');
        return redirect(route('user.index'));

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

        $departments = Department::all();
        $resorts = Resort::all();
        $groups = Group::all();
        $user = User::where('user_id',$id)->first();
        $roles = Role::all();

        if($user == null){
            session()->flash('warning','User Not Found');
            return redirect()->back();
        }
        return view('users.edit', compact(
            'departments',
            'groups',
            'resorts',
            'user',
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
        $user = User::where('user_id',$id)->first();
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
        $userData->save();
        session()->flash('success','User Updated Successfully');
        return redirect()->back();
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

}
