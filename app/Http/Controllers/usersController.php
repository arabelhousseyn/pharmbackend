<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class usersController extends Controller
{
    public function checkUser(request $req)
    {
        $data = DB::table('users')->where('username_user',$req->username)->get();
        if(count($data) > 0)
        {
            if($data[0]->active == 0)
            {
                if(Hash::check($req->password,$data[0]->password))
            {
                return $data;
            }else{
                return "nopass";
            }
            }else{
                return "noactive";
            }
        }else{
            return "no";
        }
    }

    public function users(request $req)
    {
        return DB::table('users')->where('username_user','<>',$req->username)->orderBy('id_user','DESC')->get();
    }

    public function addAdmin(request $req)
    {
        $date = date('Y-m-d H:i:s');
        $checkUser = DB::table('users')->where('username_user',$req->username)->get();
        $checkph = DB::table('users')->where('phone',$req->phone)->get();

        if(@count($checkUser) > 0)
        {
           return "nousername";
        }else{
            if(@count($checkph) > 0)
        {
           return "nophone";
        }else{
            DB::table('users')->insert([
                'username_user' => $req->username,
                'email' => $req->email,
                'phone' => $req->phone,
                'password' => Hash::make($req->password),
                'statu' => $req->type,
                'active' => 0,
                'creation_date' => $date
            ]);
            return "yes";
        }
        }
    }

    public function removeAdmins(request $req)
    {
        
        foreach ($req->ids as  $value) {
            DB::table('users')->where('id_user',strval($value))->update([
                'active' => 1,
            ]);
        }
           return "yes";
    }

    public function updateUser(request $req)
    {
        $date = date('Y-m-d H:i:s');
        $checkUser = DB::table('users')->where([['username_user',$req->username_user],['id_user','<>',$req->id_user]])->get();
        $checkph = DB::table('users')->where([['phone',$req->phone],['id_user','<>',$req->id_user]])->get();

        if(@count($checkUser) > 0)
        {
           return "nousername";
        }else{
            if(@count($checkph) > 0)
        {
           return "nophone";
        }else{
            DB::table('users')->where('id_user',$req->id_user)->update([
                'username_user' => $req->username_user,
                'email' => $req->email,
                'phone' => $req->phone
            ]);
            return "yes";
        }
        }
    }

    public function removeadmin(request $req)
    {
        DB::table('users')->where('id_user',$req->id_user)->update([
            'active' => 1
        ]);
        return "yes";
    }

    public function reactive(request $req)
    {
        DB::table('users')->where('id_user',$req->id)->update([
            'active' => 0
        ]);
        return "yes";
    }
}
