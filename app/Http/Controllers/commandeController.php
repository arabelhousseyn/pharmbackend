<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use PDF;
class commandeController extends Controller
{
    public function order(request $req)
    {
        $data = DB::table('cart_tmp')->where('id_client',$req->id)->get();
        $client = DB::table('clients')->where('id_clients',$req->id)->get();
        $date = date('Y');
        $dateNotif = date('Y-m-d H:i:s');
        $date2 = date('Y-m-d');

        $datta = DB::table('commandes')->orderBy('id_commande','DESC')->get();

         if($req->first != null)
         {
            $push = DB::table('commandes')->insert([
                'ref_commande' => $date .'v'.(count($datta) + 1),
                'date_cmd' => $date2,
                'date_valid' => null,
                'id_client' => $req->id,
                'adresse_liv' => $client[0]->adresse,
                'statu' => 0,
                'payment' => null,
                'receive' => 0
            ]);
         }else if($req->second != null)
         {
            $push = DB::table('commandes')->insert([
                'ref_commande' => $date .'v' . (count($datta) + 1),
                'date_cmd' => $date2,
                'date_valid' => null,
                'id_client' => $req->id,
                'adresse_liv' => null,
                'statu' => 0,
                'payment' => null,
                'receive' => 0
            ]);
         }

         $cmd = DB::table('commandes')->orderBy('id_commande','DESC')->LIMIT(1)->get();

         for ($i=0; $i < count($data) ; $i++) { 
             DB::table('cart')->insert([
                 'id_commande' => $cmd[0]->id_commande,
                 'id_lot' => $data[$i]->id_lot,
                 'qt_cart' => $data[$i]->qts  
             ]);
         }

         DB::table('notifications')->insert([
             'id_commande' => $cmd[0]->id_commande,
             'notif_statu' => 0,
             'pushed' => 0,
             'notif_date' => $dateNotif
         ]);

         DB::table('cart_tmp')->where('id_client',$req->id)->delete();
         return "yes";
    }

    public function getcommandeByClinet(request $req)
    {
        return DB::table('commandes')->where([['id_client',$req->id],['statu','<>','3']])->orderBy('id_commande','DESC')->get();
    }

    public function getlotsByCommande(request $req)
    {
        $global = array();

        $data = DB::table('commandes')->where('ref_commande',$req->ref)->get();

        $fetch =  DB::table('cart')->where('id_commande',$data[0]->id_commande)->get();
        foreach($fetch as $feth)
        {
            $id = $feth->id_lot;
            $tmp = DB::table('pl')->join('products','pl.id_product','=','products.id_product')
            ->join('lots', function ($join) use($id) {
                $join->on('pl.id_lot', '=', 'lots.id_lot')
                     ->where('pl.id_lot', '=', $id);
            })->get();
           
            $tmp[] = array('plus' => $feth->qt_cart);
            $tmp = json_decode($tmp,true);
            array_push($global,$tmp);
        }
        return $global;
    }

    public function deleteCommandeByClient(request $req)
    {
        DB::table('commandes')->where('id_commande',$req->id)->update([
            'statu' => 3
        ]);
        return "yes";
    }

    public function getcommndesByClientall(request $req)
    {
        return DB::table('commandes')->where('id_client',$req->id)->orderBy('id_commande','DESC')->get();
    }

    public function getstatu(request $req)
    {
        $data = DB::table('commandes')->where('ref_commande',$req->ref)->get();
        return $data[0]->statu;
    }

    public function updateQuantityOfCommandeLots(request $req)
    {
        $commandes = DB::table('commandes')->where('ref_commande',$req->ref)->get();
        $up = DB::table('cart')->where([['id_commande',$commandes[0]->id_commande],['id_lot',$req->id_lot]])->update([
            'qt_cart' => $req->qts
        ]);
        if(@$up)
        {
            return "yes";
        }else{
            return "no";
        }
    }

    public function deletelotforcommande(request $req)
    {
         $commandes = DB::table('commandes')->where('ref_commande',$req->ref)->get();
        $delete = DB::table('cart')->where([['id_commande',$commandes[0]->id_commande],['id_lot',$req->id_lot]])->delete();
        if(@$delete)
        {
            return "yes";
        }else{
            return "no";
        }    
    }

    public function getAllCommandes()
    {
        return DB::table('commandes')->join('clients','commandes.id_client','=','clients.id_clients')
        ->orderBy('id_commande','DESC')->get();
    }

    public function getlotsadminByCommandes(request $req)
    {
        $global = array();

      $data = DB::table('commandes')->where('id_commande',$req->ref)->get();
      $data2 = DB::table('cart')->where('id_commande',$req->ref)->join('lots','cart.id_lot','=','lots.id_lot')->get();
      foreach ($data2 as $value) {
          $tmp = DB::table('pl')->where('id_lot',$value->id_lot)->join('products','pl.id_product','=','products.id_product')->get();
          $global[] = array('id_cart'=>$value->id_cart,'name_product'=>$tmp[0]->name_product , 'code_lot' => $value->code_lot, 'qt_cart' => $value->qt_cart);
      }
      $t = array('statu' => $data[0]->statu);
      array_unshift($global,$t);
      return $global;   
    }

    public function updateLotsCommande(request $req)
    {
        DB::table('cart')->where('id_cart',$req->id_cart)->update([
            'qt_cart' => $req->qt_cart
        ]);
        return "yes";
    }

    public function dellotcommande(request $req)
    {
        DB::table('cart')->where('id_cart',$req->id_cart)->delete();
        return "yes";
    }

    public function delcmdvente(request $req)
    {
        foreach ($req->ids as  $value) {
            DB::table('commandes')->where('id_commande',$value)->update([
                'statu' => 3,
            ]);
        }
        return "yes";
    }

    public function no(request $req)
    {
        DB::table('commandes')->where('id_commande',$req->id_commande)->update([
            'statu' => 2,
        ]);
        return "yes";
    }

    public function yes(request $req)
    {
        $date = date('Y-m-d');
        $year = date('Y');
        $format = "";
        $format1 = "";
        $data = DB::table('cart')->where('id_commande',$req->id_commande)->get();

        foreach ($data as  $value) {
            $tmp = DB::table('lots')->where('id_lot',$value->id_lot)->get();
            $min = $tmp[0]->qt - $value->qt_cart;
            DB::table('lots')->where('id_lot',$value->id_lot)->update([
                'qt' => $min
            ]);
        }
        $s = DB::table('bonliv_vente')->get();
        $s1 = DB::table('facture_vente')->get();
        $format = "pharm/vente_li/" . strval(count($s) + 1) . "/" . $year;
        $format1 = "pharm/vente_fact/" . strval(count($s1) + 1) . "/" . $year;

        DB::table('bonliv_vente')->insert([
            'id_commande' => $req->id_commande,
            'num_bon' =>$format,
            'date' => $date
        ]);

        DB::table('facture_vente')->insert([
            'id_commande' => $req->id_commande,
            'num_facture' =>$format,
            'date' => $date
        ]);


        DB::table('commandes')->where('id_commande',$req->id_commande)->update([
            'date_valid' => $date,
            'statu' => 1,
        ]);
        return "yes";
    }

    public function getliv(request $req)
    {
        $global = array();
        $data = DB::table('bonliv_vente')->where('id_commande',$req->id)->get();
      $data2 = DB::table('cart')->where('id_commande',$req->id)->join('lots','cart.id_lot','=','lots.id_lot')->get();
      foreach ($data2 as $value) {
          $tmp = DB::table('pl')->where('id_lot',$value->id_lot)->join('products','pl.id_product','=','products.id_product')->get();
          $global[] = array('name_product'=>$tmp[0]->name_product , 'code_lot' => $value->code_lot, 'qt_cart' => $value->qt_cart);
      }
      $t = array('num_bon' => $data[0]->num_bon);
      $t1 = array('date' => $data[0]->date);
      array_unshift($global,$t);
      array_unshift($global,$t1);
      return $global; 

    }

    public function getFact(request  $req)
    {
        $global = array();
        $data = DB::table('facture_vente')->where('id_commande',$req->id)->get();
      $data2 = DB::table('cart')->where('id_commande',$req->id)->join('lots','cart.id_lot','=','lots.id_lot')->get();
      foreach ($data2 as $value) {
          $tmp = DB::table('pl')->where('id_lot',$value->id_lot)->join('products','pl.id_product','=','products.id_product')->get();
          $global[] = array('name_product'=>$tmp[0]->name_product , 'code_lot' => $value->code_lot, 'qt_cart' => $value->qt_cart,'price' => $value->price);
      }
      $t = array('num_facture' => $data[0]->num_facture);
      $t1 = array('date' => $data[0]->date);
      array_unshift($global,$t);
      array_unshift($global,$t1);
      return $global; 
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
                DB::table('payment_vente')->insert([
                    'id_commande' => $req->id,
                    'type_payment' => $req->mode,
                    'bn_number' => $req->number,
                    'date' => $date,
                ]);
                return "yes";
              }
          }elseif($req->mode == 1 || $req->mode == 2){
            DB::table('payment_vente')->insert([
                'id_commande' => $req->id,
                'type_payment' => $req->mode,
                'bn_number' => null,
                'date' => $date,
            ]);

            DB::table('commandes')->where('id_commande',$req->id)->update([
                'payment' => 0,
            ]);
            
            return "yes";
          }
     }

     public function getpayment(request $req)
     {
         return DB::table('payment_vente')->where('id_commande',$req->id)->get();
     }

     public function receive(request $req)
     {
         DB::table('commandes')->where('id_commande',$req->id)->update([
             'receive' => 1,
         ]);
         return "yes";
     }

     public function venteJour()
     {
         $date = date('Y-m-d');
         $total = 0;
         $data = DB::table('commandes')->where([['date_cmd',$date],['statu',1],['payment',0]])->get();
         foreach ($data as  $value) {
            $tmp = DB::table('cart')->where('id_commande',$value->id_commande)->join('lots','cart.id_lot','=','lots.id_lot')->first();
            $total += ($tmp->qt_cart * $tmp->price);
         }
         return $total;
     }

     public function countAll()
     {
         return DB::table('commandes')->count();
     }

     public function ventemois()
     {
         $year = date('Y');
         $month = date('m');
         $total = 0;
         $data = DB::table('commandes')->where([['statu',1],['payment',0]])->orderBy('id_commande','DESC')->get();
         foreach ($data as $value) {
             $tmp =explode('-',$value->date_cmd);
             $compyear =  $tmp[0];
             $compmonth =  $tmp[1];

             if($compyear == $year)
             {
                 if($compmonth == $month)
                 {
                    $tmp = DB::table('cart')->where('id_commande',$value->id_commande)->join('lots','cart.id_lot','=','lots.id_lot')->first();
                    $total += ($tmp->qt_cart * $tmp->price);
                 }
             }

         }
         return $total;
     }

     public function cartByCommande(request $req)
     {
         $final = array();
         $data = DB::table('cart')->where('id_commande',$req->id)->get();

         foreach ($data as $value) {
             $id = $value->id_lot;
             $tmp = DB::table('pl')->join('lots', function ($join) use ($id) {
                $join->on('pl.id_lot', '=', 'lots.id_lot')
                     ->where('lots.id_lot', '=', $id);
            })->join('products','pl.id_product','=','products.id_product')->first();

            $final[] = array(
                'name_product' => $tmp->name_product,
                'code_lot' => $tmp->code_lot,
                'date_fab' => $tmp->date_fab,
                'date_exp' => $tmp->date_exp,
                'price' => $tmp->price,
                'qt_cart' => $value->qt_cart
            );
         }

         return $final;
     }

     public function Totalvente()
     {
         $total = 0;
         $data = DB::table('commandes')->where('statu',1)->get();
         foreach ($data as $value) {
             $tmp = DB::table('cart')->where('id_commande',$value->id_commande)->join('lots','cart.id_lot','=','lots.id_lot')->first();
             $total += $tmp->qt_cart * $tmp->price;
         }
         return $total;
     }

     public function TotalcommandesPaidandnotpaid()
     {
         $paid = DB::table('commandes')->where('payment',0)->count();
         $notpaid = DB::table('commandes')->where('payment',null)->count();
         return $paid . ',' . $notpaid;
     }

    public function searchcommandevente(request $req)
    {
        $final = array();
         $data = DB::table('commandes')->join('clients','commandes.id_client','=','clients.id_clients')
         ->orderBy('id_commande','DESC')->get();
         foreach ($data as  $value) {
            if($value->date_cmd >= $req->start && $value->date_cmd <= $req->end)
            {
                $final[] = $value;
            }
         }
         return $final;
    }

    public function addcommandeclient(request $req)
    {
        if($req->id_commande == null)
        {
            $data = DB::table('commandes')->orderBy('id_commande','DESC')->get();
            $date = date('Y-m-d');
            $year = date('Y');
            $format = $year . 'v' . strval(count($data) + 1);
            if($req->adress == null)
            {
                DB::table('commandes')->insert([
                    'ref_commande' => $format,
                    'date_cmd' => $date,
                    'date_valid' => null,
                    'id_client' => $req->client,
                    'adresse_liv' => null,
                    'statu' => 0,
                    'payment' => null,
                    'receive' => 0
                ]);
            }else{
                $client = DB::table('clients')->where('id_clients',$req->client)->first();
                DB::table('commandes')->insert([
                    'ref_commande' => $format,
                    'date_cmd' => $date,
                    'date_valid' => null,
                    'id_client' => $req->client,
                    'adresse_liv' => $client->adresse,
                    'statu' => 0,
                    'payment' => null,
                    'receive' => 0
                ]);
            }

            $final = DB::table('commandes')->orderBy('id_commande','DESC')->LIMIT(1)->first();
            DB::table('cart')->insert([
                'id_commande' => $final->id_commande,
                'id_lot' => $req->lot,
                'qt_cart' => $req->qts,
            ]);
            return $final->id_commande . ',' . $final->id_client;
        }else{
           $data = DB::table('commandes')->where('id_commande',$req->id_commande)->first();
           DB::table('cart')->insert([
            'id_commande' => $data->id_commande,
            'id_lot' => $req->lot,
            'qt_cart' => $req->qts,
           ]);
           return $data->id_commande . ',' . $data->id_client;
        }
    }

}


/*
$data = DB::table('cart')->where('id_client',$req->id)->get();
        $client = DB::table('clients')->where('id_clients',$req->id)->get();
        $date = date('Y-m-d');
        $global = array();
        array_push($global,$client);
        foreach ($data as $value) {
            $id = $value->id_lot;
            $t = DB::table('pl')->join('products','pl.id_product','=','products.id_product')
            ->join('lots', function ($join) use($id) {
                $join->on('pl.id_lot', '=', 'lots.id_lot')
                     ->where('pl.id_lot', '=', $id);
            })->get();   
            $t[] = array($value->qts);
            array_push($global,$t);
        }
        array_unshift($global,$date);
        array_unshift($global,$ref);

        $pdf = PDF::loadView('bc',compact('global'));
           $pdf->save($ref . '.pdf');
*/
