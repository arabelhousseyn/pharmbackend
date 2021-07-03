<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use PDF;
class commandeachatController extends Controller
{
     public function getAll()
     {
         return DB::table('commandes_achat')->join('fournisseurs','commandes_achat.id_fournisseur','=','fournisseurs.id_fournisseur')->join('users','commandes_achat.created_by','=','users.id_user')->orderBy('id_commandeachat','DESC')->get();
     }

     public function save(request $req)
     {
         if($req->cmd == null)
         {
             $user = DB::table('users')->where('username_user',$req->user)->get();

             $date = date('Y');
             $date_achat = date('Y-m-d');
             $check = DB::table('commandes_achat')->orderBy('id_commandeachat','DESC')->get();
             if(@count($check) > 0)
             {
                DB::table('commandes_achat')->insert([
                    'ref_commandeachat' => $date . 'a' . strval($check[0]->id_commandeachat + 1),
                    'date_achat' => $date_achat,
                    'date_validachat' => null,
                    'id_fournisseur' => $req->fournis,
                    'statu_achat' => 3,
                    'created_by' => $user[0]->id_user,
                    'payment' => null,
                ]);
             }else{
                 DB::table('commandes_achat')->insert([
                     'ref_commandeachat' => $date . 'a1',
                     'date_achat' => $date_achat,
                     'date_validachat' => null,
                     'id_fournisseur' => $req->fournis,
                     'statu_achat' => 3,
                     'created_by' => $user[0]->id_user,
                     'payment' => null,
                 ]);
             }

             $data = DB::table('commandes_achat')->orderBy('id_commandeachat','DESC')->LIMIT(1)->get();
             DB::table('cart_tmpachat')->insert([
                'id_cartcommande' => $data[0]->id_commandeachat,
                'id_cartlot' => $req->lot,
                'prix_achat' => $req->price,
                'qts_achat' => $req->qt
            ]);
            return $data;

         }else{
             DB::table('cart_tmpachat')->insert([
                 'id_cartcommande' => $req->cmd,
                 'id_cartlot' => $req->lot,
                 'prix_achat' => $req->price,
                 'qts_achat' => $req->qt
             ]);
             return DB::table('commandes_achat')->where('id_commandeachat',$req->cmd)->get();
         }
     }

     public function order(request $req)
     {
        $data = DB::table('cart_tmpachat')->where('id_cartcommande',$req->cmd)->get();
        foreach ($data as $value) {
            DB::table('cart_achat')->insert([
                'id_cartcommande' => $req->cmd,
                'id_cartlot' => $value->id_cartlot,
                'prix_achat' => $value->prix_achat,
                'qts_achat' => $value->qts_achat
            ]);

            DB::table('commandes_achat')->where('id_commandeachat',$req->cmd)->update([
                'statu_achat' => 0
            ]);
            DB::table('cart_tmpachat')->where('id_cartcommande',$req->cmd)->delete();
        }

       
        return "yes";
     }

     public function deleteByGroupe(request $req)
     {
         for ($i=0; $i <count($req->cmd) ; $i++) { 
            DB::table('commandes_achat')->where('id_commandeachat',$req->cmd[$i])->delete();
            DB::table('cart_tmpachat')->where('id_cartcommande',$req->cmd[$i])->delete();
            DB::table('cart_achat')->where('id_cartcommande',$req->cmd[$i])->delete();
         }
         return "yes";
     }

     public function deleteSingle(request $req)
     {
        DB::table('commandes_achat')->where('id_commandeachat',$req->cmd)->delete();
        DB::table('cart_tmpachat')->where('id_cartcommande',$req->cmd)->delete();
        DB::table('cart_achat')->where('id_cartcommande',$req->cmd)->delete();
        return "yes";
     }

     public function yes(request $req)
     {
         $date = date('Y-m-d');
         $year = date('Y');
         $data2 = DB::table('facture_achat')->get();

         $format = "pharm/achat/" . strval(count($data2) + 1) . '/' . $year;

         DB::table('commandes_achat')->where('id_commandeachat',$req->cmd)->update([
             'statu_achat' => 1,
             'date_validachat' => $date
         ]);

         DB::table('facture_achat')->insert([
             'id_commandeachat' => $req->cmd,
             'num_facture' => $format,
             'date' => $date
         ]);

        $data =  DB::table('cart_achat')->where('id_cartcommande',$req->cmd)->get();
        
        foreach ($data as  $value) {

            $trait = DB::table('lots')->where('id_lot',$value->id_cartlot)->get();

            $max = $trait[0]->qt + $value->qts_achat;

            DB::table('lots')->where('id_lot',$value->id_cartlot)->update([
                'qt' => $max
            ]);
        }
        return "yes";
     }

     public function no(request $req)
     {
        DB::table('commandes_achat')->where('id_commandeachat',$req->cmd)->update([
            'statu_achat' => 2,
        ]);
        return "yes";
     }

     public function detailsCommande(request $req)
     {
         $all  = array();
         $data = DB::table('cart_achat')->where('id_cartcommande',$req->key1)->join('lots','cart_achat.id_cartlot','=','lots.id_lot')->get();
         foreach ($data as  $value) {
             $tmp = DB::table('pl')->where('id_lot',$value->id_cartlot)->join('products','pl.id_product','=','products.id_product')->get();
             $tmp2 = DB::table('commandes_achat')->where('id_commandeachat',$req->key1)->join('fournisseurs','commandes_achat.id_fournisseur','=','fournisseurs.id_fournisseur')->get();
             $value = array('name_product' => $tmp[0]->name_product,'code_lot'=>$value->code_lot,'prix_achat'=>$value->prix_achat,'qts_achat'=>$value->qts_achat,'name_fourni'=>$tmp2[0]->name_fournisseur);
             array_push($all,$value);
         }
         return $all;
     }

     public function detailFactureCommande(request $req)
     {
         $all  = array();
         $data = DB::table('cart_achat')->where('id_cartcommande',$req->key2)->join('lots','cart_achat.id_cartlot','=','lots.id_lot')->get();
         $tmp2 = DB::table('commandes_achat')->where('id_commandeachat',$req->key2)->join('fournisseurs','commandes_achat.id_fournisseur','=','fournisseurs.id_fournisseur')->get();
         $tmp3 = DB::table('facture_achat')->where('id_commandeachat',$req->key2)->get();
         foreach ($data as  $value) {
             $tmp = DB::table('pl')->where('id_lot',$value->id_cartlot)->join('products','pl.id_product','=','products.id_product')->get();
             $value = array('name_product' => $tmp[0]->name_product,'code_lot'=>$value->code_lot,'prix_achat'=>$value->prix_achat,'qts_achat'=>$value->qts_achat,'name_fourni'=>$tmp2[0]->name_fournisseur);
             array_push($all,$value);
         }
         $t = array('num_facture'=> $tmp3[0]->num_facture);
         $t2 = array('date'=> $tmp3[0]->date);
         array_unshift($all,$t);
         array_unshift($all,$t2);
         return $all;
     }

     public function payment(request $req)
     {
         $date = date('Y-m-d');
          if($req->mode == 0)
          {
              if($req->number == null)
              {
                  return "no";
              }else{
                DB::table('payment_achat')->insert([
                    'id_commandeachat' => $req->id,
                    'type_payment' => $req->mode,
                    'bn_number' => $req->number,
                    'date' => $date,
                ]);
                return "yes";
              }
          }elseif($req->mode == 1 || $req->mode == 2){
            DB::table('payment_achat')->insert([
                'id_commandeachat' => $req->id,
                'type_payment' => $req->mode,
                'bn_number' => null,
                'date' => $date,
            ]);

            DB::table('commandes_achat')->where('id_commandeachat',$req->id)->update([
                'payment' => 0,
            ]);

            return "yes";
          }
     }

     public function getpayment(request $req)
     {
         return DB::table('payment_achat')->where('id_commandeachat',$req->id)->get();
     }

     public function countAll()
     {
         return DB::table('commandes_achat')->count();
     }

     public function achatjour()
     {
        $date = date('Y-m-d');
        $total = 0;
        $data = DB::table('commandes_achat')->where([['date_achat',$date],['statu_achat',1],['payment',0]])->get();
        foreach ($data as  $value) {
           $tmp = DB::table('cart_achat')->where('id_cartcommande',$value->id_commandeachat)->join('lots','cart_achat.id_cartlot','=','lots.id_lot')->first();
           $total += ($tmp->qts_achat * $tmp->prix_achat);
        }
        return $total;
     }

     public function achatmois()
     {
        $year = date('Y');
        $month = date('m');
        $total = 0;
        $data = DB::table('commandes_achat')->where([['statu_achat',1],['payment',0]])->orderBy('id_commandeachat','DESC')->get();
        foreach ($data as $value) {
            $tmp =explode('-',$value->date_achat);
            $compyear =  $tmp[0];
            $compmonth =  $tmp[1];

            if($compyear == $year)
            {
                if($compmonth == $month)
                {
                    $tmp = DB::table('cart_achat')->where('id_cartcommande',$value->id_commandeachat)->join('lots','cart_achat.id_cartlot','=','lots.id_lot')->first();
                    $total += ($tmp->qts_achat * $tmp->prix_achat);
                }
            }

        }
        return $total;
     }

     public function getdataforedit(request $req)
     {
         $final  = array();
         $data = DB::table('cart_tmpachat')->where('id_cartcommande',$req->id)->join('lots','cart_tmpachat.id_cartlot','=','lots.id_lot')->get();
         foreach ($data as  $value) {
             $tmp = DB::table('pl')->where('id_lot',$value->id_lot)->join('products','products.id_product','=','pl.id_product')->first();
             $tmp2 = DB::table('commandes_achat')->where('id_commandeachat',$req->id)->join('fournisseurs','fournisseurs.id_fournisseur','=','commandes_achat.id_fournisseur')->first();

             $final[] = array('statu_achat' => $tmp2->statu_achat,'id_cartachat' => $value->id_cartachat,'ref_commandeachat' => $tmp2->ref_commandeachat,
            'name_fournisseur' => $tmp2->name_fournisseur,'name_product'=> $tmp->name_product,
        'code_lot'=>$value->code_lot,'prix_achat'=>$value->prix_achat,'qts_achat'=>$value->qts_achat,
    'id_fournisseur' => $tmp2->id_fournisseur);

         }

         return $final;
     }

     public function removecartachat(request $req)
     {
         foreach ($req->dds as $id) {
             DB::table('cart_tmpachat')->where('id_cartachat',$id)->delete();
         }
         return "yes";
     }

     public function TotalcommandesAchatPaidandnotpaid()
     {
         $paid = DB::table('commandes_achat')->where('payment',0)->count();
         $notpaid = DB::table('commandes_achat')->where('payment',null)->count();
         return $paid . ',' . $notpaid;
     }

     public function searchcommande(request $req)
     {
         $final = array();
         $data = DB::table('commandes_achat')->join('fournisseurs','commandes_achat.id_fournisseur','=','fournisseurs.id_fournisseur')->join('users','commandes_achat.created_by','=','users.id_user')->orderBy('id_commandeachat','DESC')->get();
         foreach ($data as  $value) {
            if($value->date_achat >= $req->start && $value->date_achat <= $req->end)
            {
                $final[] = $value;
            }
         }
         return $final;
     }
}
