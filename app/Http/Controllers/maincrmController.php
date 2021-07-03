<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class maincrmController extends Controller
{
    public function vente()
    {
        $year = date('Y');
        $final = array();
        $count = 0;

        $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
       $data = DB::table('commandes')->where('statu',1)->get();

       foreach ($months as $month) {
           foreach ($data as  $value) {
               $bin = explode('-',$value->date_cmd);
              $yr = $bin[0];
              if($yr == $year)
              {
                  $mn =  $bin[1];
                  if($mn == $month)
                  {
                      $count++;
                  }
              }else{
                  continue;
              }
           }
           array_push($final,$count);
           $count = 0;
       }
     return $final;  
    }


    public function revenu()
    {
        $total = 0;
        $data = DB::table('commandes')->where('statu',1)->orderBy('id_commande','DESC')->get();
        $date = date('Y');
        foreach ($data as  $value) {
            $t = explode('-',$value->date_cmd);
            if($t[0] == $date)
            {
                $tmp = DB::table('cart')->where('id_commande',$value->id_commande)
            ->join('lots','cart.id_lot','=','lots.id_lot')->get();
            foreach ($tmp as $vl) {
                $total += $vl->qt_cart * $vl->price;
            }
            }
        }
        return $total;
    }

    public function dacaht()
    {
        $total = 0;
        $data = DB::table('commandes_achat')->where('statu_achat',1)->orderBy('id_commandeachat','DESC')->get();
        $date = date('Y');
        foreach ($data as  $value) {
            $t = explode('-',$value->date_achat);
            if($t[0] == $date)
            {
                $tmp = DB::table('cart_achat')->where('id_cartcommande',$value->id_commandeachat)
            ->join('lots','cart_achat.id_cartlot','=','lots.id_lot')->get();
            foreach ($tmp as  $vl) {
                $total += $vl->qts_achat * $vl->prix_achat;
            }
            }
        }
        return $total;
    }

    public function nbrproduct()
    {
        return DB::table('products')->count();
    }

    public function maincrmacaht()
    {
        $year = date('Y');
        $final = array();
        $count = 0;

        $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
       $data = DB::table('commandes_achat')->where('statu_achat',1)->get();

       foreach ($months as $month) {
           foreach ($data as  $value) {
               $bin = explode('-',$value->date_achat);
              $yr = $bin[0];
              if($yr == $year)
              {
                  $mn =  $bin[1];
                  if($mn == $month)
                  {
                      $count++;
                  }
              }else{
                  continue;
              }
           }
           array_push($final,$count);
           $count = 0;
       }
     return $final;     
    }
}
