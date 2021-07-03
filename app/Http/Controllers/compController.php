<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class compController extends Controller
{
    public function stocksoritetotal()
    {
        $total = array();
        $data = DB::table('commandes')->where('statu',1)->get();
        foreach ($data as  $value) {
            $tmp = DB::table('cart')->where('id_commande',$value->id_commande)->get();
            foreach ($tmp as $vl) {
                $t = DB::table('pl')->where('id_lot',$vl->id_lot)->get();
                foreach ($t as $r) {
                    array_push($total,$r->id_product);
                }
            }
        }
        return count($total);
    }

    public function entrertotal()
    {
        $total = array();
        $data = DB::table('commandes_achat')->where('statu_achat',1)->get();
        foreach ($data as  $value) {
            $tmp = DB::table('cart_achat')->where('id_cartcommande',$value->id_commandeachat)->get();
            foreach ($tmp as $vl) {
                $t = DB::table('pl')->where('id_lot',$vl->id_cartlot)->get();
                foreach ($t as $r) {
                    array_push($total,$r->id_product);
                }
            }
        }
        return count($total);
    }

    public function stocks()
    {
        return DB::table('stock')->count();
    }

    public function chartsortie()
    {
        $year = date('Y');
        $total = array();
        $all = array();
        $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
        $data = DB::table('commandes')->where('statu',1)->get();
        foreach ($months as $mn) {
        foreach ($data as $value) {
            $bn = explode('-',$value->date_cmd);
            if($bn[0] == $year)
            {
                    if($bn[1] == $mn)
                    {
                        $tmp = DB::table('cart')->where('id_commande',$value->id_commande)->get();
                        foreach ($tmp as $vl) {
                            $t = DB::table('pl')->where('id_lot',$vl->id_lot)->get();
                            foreach ($t as $r) {
                                array_push($total,$r->id_product);
                            }
                        }  
                    }
            }else{
                continue;
            }
        }
        array_push($all,count($total));
        $total = array();
    }
    return $all;
    }

    public function chartenter()
    {
        $year = date('Y');
        $total = array();
        $all = array();
        $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
        $data = DB::table('commandes_achat')->where('statu_achat',1)->get();
        foreach ($months as $mn) {
        foreach ($data as $value) {
            $bn = explode('-',$value->date_achat);
            if($bn[0] == $year)
            {
                    if($bn[1] == $mn)
                    {
                        $tmp = DB::table('cart_achat')->where('id_cartcommande',$value->id_commandeachat)->get();
                        foreach ($tmp as $vl) {
                            $t = DB::table('pl')->where('id_lot',$vl->id_cartlot)->get();
                            foreach ($t as $r) {
                                array_push($total,$r->id_product);
                            }
                        }  
                    }
            }else{
                continue;
            }
        }
        array_push($all,count($total));
        $total = array();
    }
    return $all;
    }
}
