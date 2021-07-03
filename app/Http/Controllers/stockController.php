<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class stockController extends Controller
{
    public function getAll()
    {
        return DB::table('stock')->orderBy('id_stock','DESC')->get();
    }
    
    public function addstock(request $req)
    {
        $check = DB::table('stock')->where('name_stock',$req->name)->get();

        if(count($check) > 0)
        {
            return "no";
        }else{
            $date = date('Y-m-d H:i:s');
            DB::table('stock')->insert([
                'name_stock' => $req->name,
                'statu' => 1,
                'date_creation' => $date
            ]);
            return "yes";
        }
    }

    public function updateStock(request $req)
    {
        $check = DB::table('stock')->where([['name_stock',$req->stock],['id_stock','<>',$req->id_stock]])->get();

        if(count($check) > 0)
        {
            return "no";
        }else{
            DB::table('stock')->where('id_stock',$req->id_stock)->update([
                'name_stock' => $req->name_stock,
            ]);
            return "yes";
        }
    }


    public function getStocksnegCurrent(request $req)
    {
        return DB::table('stock')->where('id_stock','<>',$req->id)->orderBy('id_stock','DESC')->get();
    }

    public function forward(request $req)
    {
        foreach ($req->ids as $value) {
            $check = DB::table('pls')->where('id_pls',$value)->first();
            $check2 = DB::table('pls')->where([['id_pl','=',$check->id_pl],['id_stock','=',$req->id]])->first();
            if($check2)
            {
                $check3 = DB::table('pl')->where('id_pl',$check2->id_pl)->first();
                $check4 = DB::table('lots')->where('id_lot',$check3->id_lot)->first();
                $comp = intval($check4->qt) - intval($check2->qt_pls);

                if(intval($req->qt) > $comp)
                {
                    return $check4->qt;
                }else{
                    if(intval($check2->qt_pls) - intval($req->qt) < 0)
                    {
                        return $check2->qt_pls;
                    }else{
                        $tmp = $check2->qt_pls + $req->qt;
                    DB::table('pls')->where('id_pls',$check2->id_pls)->update([
                        'qt_pls' => $tmp,
                    ]);

                    if(intval($check2->qt_pls) - intval($req->qt)  == 0)
                    {
                        DB::table('pls')->where('id_pls',$value)->delete();
                    }else{
                        DB::table('pls')->where('id_pls',$value)->update([
                            'qt_pls' =>intval($check2->qt_pls) -  intval($req->qt),
                        ]); 
                    }
                    return "yes";
                    }
                }
                
            }else{
                $check3 = DB::table('pl')->where('id_pl',$check->id_pl)->first();
                $check4 = DB::table('lots')->where('id_lot',$check3->id_lot)->first();
                $comp = intval($check4->qt) - intval($check->qt_pls);

                if(intval($req->qt) > $comp)
                {
                    return $check4->qt;
                }else{
                    if(intval($check->qt_pls) - intval($req->qt) < 0)
                    {
                        return $check->qt_pls;
                    }else{
                        DB::table('pls')->insert([
                            'id_stock' => $req->id,
                            'id_pl' => $check->id_pl,
                            'statu' => 0,
                            'qt_pls' => $req->qt,
                        ]);
                    if(intval($check->qt_pls) - intval($req->qt)  == 0)
                    {
                        DB::table('pls')->where('id_pls',$value)->delete();
                    }else{
                        DB::table('pls')->where('id_pls',$value)->update([
                            'qt_pls' =>intval($check->qt_pls) -  intval($req->qt),
                        ]); 
                    }
                    return "yes";
                    }
                } 
            }
        }
    }

    public function eye(request $req)
    {
        DB::table('pls')->where('id_pls',$req->id)->update([
            'statu' => 0
        ]);
        return "yes";
    }

    public function slash(request $req)
    {
        DB::table('pls')->where('id_pls',$req->id)->update([
            'statu' => 1
        ]);
        return "yes";
    }

    public function upqt(request $req)
    {
        DB::table('pls')->where('id_pls',$req->id_pls)->update([
            'qt_pls' => $req->qt_pls
        ]);
        return "yes";
    }
}
