<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categorie;
use Illuminate\Support\Facades\DB;
class categorieController extends Controller
{
    public function getAll()
    {
        $global = array();
        $final = array();

        $data =  categorie::get();
        foreach ($data as $value) {
            $tmp = DB::table('products')->orderBy('id_product','DESC')->where('id_categorie',$value->id_categorie)->limit(6)->get();
            $tmp[] = array('cat' => $value->name_categorie);
            $tmp = json_decode($tmp,true);
            array_push($global,$tmp);
        }
        foreach ($global as $value) {
            $value = array_reverse($value);
            array_push($final,$value);
        }
        return $final;
    }

    public function exists(request $req)
    {
       $categorie = categorie::where('name_categorie',$req->name)->get();
       if(count($categorie) > 0)
       {
           return $categorie;
       }else{
           return "no";
       }
    }

    public function getCtas()
    {
        return categorie::get();
    }
}
