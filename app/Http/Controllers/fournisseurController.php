<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class fournisseurController extends Controller
{

    public function getAll()
    {
    return DB::table('fournisseurs')->orderBy('id_fournisseur','DESC')->get();
    }

    public function getactivefournisseur()
    {
        return DB::table('fournisseurs')->where('statu_fournisseur',0)->orderBy('id_fournisseur','DESC')->get(); 
    }

    public function addFourniseur(request $req)
    {
        $check = DB::table('fournisseurs')->where('phone_fournisseur',$req->phone)->get();
        if(count($check) > 0)
        {
            return "no";
        }else{
            DB::table('fournisseurs')->insert([
                'name_fournisseur' => $req->name,
                'phone_fournisseur' => $req->phone,
                'statu_fournisseur' => 0,
                'creation_date' => date('Y-m-d H:i:s')
            ]);
            return "yes";
        }
    }

    public function updateFournisseur(request $req)
    {
        $checkph = DB::table('fournisseurs')->where([['phone_fournisseur',$req->phone_fournisseur],['id_fournisseur','<>',$req->id_fournisseur]])->get();

        if(@count($checkph) > 0)
        {
           return "nophone";
        }else{
            DB::table('fournisseurs')->where('id_fournisseur',$req->id_fournisseur)->update([
                'name_fournisseur' => $req->name_fournisseur,
                'phone_fournisseur' => $req->phone_fournisseur,
            ]);
            return "yes";
        }
        
    }

    public function removebyselect(request $req)
    {
        foreach ($req->ids as  $value) {
            DB::table('fournisseurs')->where('id_fournisseur',strval($value))->delete();
        }
           return "yes";
    }


    public function removefournisseur(request $req)
    {
        DB::table('fournisseurs')->where('id_fournisseur',$req->id_fournisseur)->update([
            'statu_fournisseur' => 1,
        ]);
        return "yes";
    }

    public function reactivefourni(request $req)
    {
        DB::table('fournisseurs')->where('id_fournisseur',$req->id_fournisseur)->update([
            'statu_fournisseur' => 0,
        ]);
        return "yes";
    }
}
