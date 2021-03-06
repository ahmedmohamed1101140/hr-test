<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Department;
use App\Resort;
use App\Group;
use App\ldapUsers;
use App\ldapHelperMethods;

class DepartmentUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //latest()->where('user_name','!=','0')->get();

        $authUserID = User::where('user_id',Session::get('user')[0]->user_id)->first();
        $users = User::where('department_id', $authUserID->department_id)->where('user_name','!=','0')->get();
        return view('departmentUser.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $resorts = Resort::all();
        $groups = Group::all();

        return view('departmentUser.create', compact(
            'resorts',
             'groups'
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
        //

        $request['user_id'] = Str::random(6);
        $request['resort_id'] = User::where('user_id',Session::get('user')[0]->user_id)->first()->resort_id;
       $user = User::create($request->all());

        session()->flash('success','User Added Successfully');
        return redirect(route('department-users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::where('user_id',$id)->first();
        if($user == null){
            session()->flash('warning','User Not Found');
            return redirect(route('user.index'));
        }
        return view('users.show',compact('user'));
        //find or fail el users
        //return to view users.show and $user
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $resorts = Resort::all();
        $groups = Group::all();

        $user = User::where('user_id', $id)->first();

        return view('departmentUser.edit', compact(
                'resorts',
                'groups',
                'user'
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
        //
        $request['department_id'] = User::where('user_id',Session::get('user')[0]->user_id)->first()->department_id;

        $user = User::where('user_id',$id)->first();
        $user->update($request->all());
        session()->flash('success','User Updated Successfully');
        return redirect(route('department-users.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $ldap = new ldapUsers();
        $ldap->user_delete($id);
         $user = User::where('user_name',$id)->first();
         $user->delete();
         session()->flash('success','User Deleted Successfully');
         return redirect()->back();
    }
}
