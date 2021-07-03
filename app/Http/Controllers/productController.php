<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\categorie;
use App\Models\product;

class productController extends Controller
{
    public function getproductBycategorie(request $req)
    {
        $categorie = categorie::where('name_categorie',$req->name)->get();
        if($categorie[0]->id_categorie == 1)
        {
            return DB::table('products')->where('id_categorie',$categorie[0]->id_categorie)->orderBy('id_product', 'desc')->paginate(10);
        }else{
            return DB::table('products')->where('id_categorie',$categorie[0]->id_categorie)->orderBy('id_product', 'desc')
            ->join('forms','products.id_form','=','forms.id_form')
            ->join('dcis','products.id_dci','=','dcis.id_dci')->paginate(10);
        }
    }

    public function getproductByName(request $req)
    {
        $check = product::where('name_product',$req->name)->get();
        if($check[0]->id_dci != null && $check[0]->id_form != null)
        {
            return DB::table('products')->where('name_product',$req->name)->join('dcis','products.id_dci','=','dcis.id_dci')
       ->join('forms','products.id_form','=','forms.id_form')->get();
        }else{
               return $check;
        }
    }

    public function alert()
    {
        return DB::table('alerts')->where('condition1','count')->get();
    }

    public function getLots(request $req)
    {
        $data = product::where('id_product',$req->id)->get();
        $final = array();

       /* return DB::table('pl')->where('id_product',$data[0]->id_product)
        ->join('lots','pl.id_lot','=','lots.id_lot')
        ->join('pls', function ($join) {
            $join->on('pl.id_pl', '=', 'pls.id_pl')
                 ->where('pls.statu', '==', 0);
        })
        ->get();*/

        $check = DB::table('pl')->where('id_product',$data[0]->id_product)
        ->join('lots','pl.id_lot','=','lots.id_lot')
        ->get();
        foreach ($check as $value) {
            $tmp = DB::table('pls')->where([['id_pl','=',$value->id_pl],['statu','=',0]])->first();
            if($tmp)
            {
                $final[] = array('id_lot'=> $value->id_lot,'code_lot' => $value->code_lot,'price' => $value->price,
                'date_exp' => $value->date_exp,'date_fab' => $value->date_fab,'qt' => $value->qt);
            }
        }
        return $final;
    }


    public function getLots2(request $req)
    {
       return $check = DB::table('pl')->where('id_product',$req->id)
        ->join('lots','pl.id_lot','=','lots.id_lot')
        ->get();
    }

    public function cartDetails(request $req)
    {
        return DB::table('cart_tmp')->where('id_client',$req->id)->join('products','cart_tmp.id_product','=','products.id_product')
        ->join('lots','cart_tmp.id_lot','=','lots.id_lot')->get();
    }

    public function getAllProductscount(request $req)
    {
        $categorie = categorie::where('name_categorie',$req->name)->get();

         return product::where('id_categorie',$categorie[0]->id_categorie)->count();
    }

    public function searchByCategorie(request $req)
    {
        $categorie = categorie::where('name_categorie',$req->namecat)->get();

        if($categorie[0]->id_categorie == 1)
        {
            return DB::table('products')->where([['name_product','like','%'.$req->key.'%'],['id_categorie',$categorie[0]->id_categorie]])->get();
        }else{
            return DB::table('products')->where([['name_product','like','%'.$req->key.'%'],['id_categorie',$categorie[0]->id_categorie]])
            ->join('forms','products.id_form','=','forms.id_form')
            ->join('dcis','products.id_dci','=','dcis.id_dci')->get();
        }
    }

    public function putoTmp(request $req)
    {
        for ($i=0; $i <count($req->ids) ; $i++) { 
            DB::table('cart_tmp')->insert([
                'id_client' => $req->id,
                'id_product' => $req->idp,
                'id_lot' => $req->ids[$i],
                'qts' => $req->qts[$i]
            ]);
        }
        return "yes";
    }

    public function updateCartDetail(request $req)
    {
        $up = DB::table('cart_tmp')->where('id',$req->id)->update([
            'qts' => $req->value
        ]);

        if(@$up)
        {
            return "yes";
        }
    }

    public function deleteProductCart(request $req)
    {
         $delete = DB::table('cart_tmp')->where('id',$req->id)->delete();
         if(@$delete)
         {
             return "yes";
         }else{
             return "no";
         }
    }

    public function getAll()
    {
        return DB::table('products')->orderBy('id_product','DESC')->get();
    }

    public function getAlldcis()
    {
        return DB::table('dcis')->get();
    }

    public function getAllforms()
    {
        return DB::table('forms')->get();
    }

    public function addProduct(request $req)
    {
        if($req->pic == "null")
        {
            if($req->cat == 1)
            {
                DB::table('products')->insert([
                    'name_product' => $req->name,
                    'pic' => null,
                    'id_dci' => null,
                    'id_form' => null,
                    'id_categorie' => $req->cat,
                    'sill' => $req->sill,
                ]);
            }else{
                DB::table('products')->insert([
                    'name_product' => $req->name,
                    'pic' => null,
                    'id_dci' => $req->dci,
                    'id_form' => $req->form,
                    'id_categorie' => $req->cat,
                    'sill' => $req->sill,
                ]);
            }
            return DB::table('products')->orderBy('id_product','DESC')->LIMIT(1)->get();
        }else{
            $path = uniqid() . '.jpg';
             $req->pic->storeAs('products',$path);
             if($req->cat == 1)
             {
                DB::table('products')->insert([
                    'name_product' => $req->name,
                    'pic' => $path,
                    'id_dci' => null,
                    'id_form' => null,
                    'id_categorie' => $req->cat,
                    'sill' => $req->sill,
                ]);
             }else{
                DB::table('products')->insert([
                    'name_product' => $req->name,
                    'pic' => $path,
                    'id_dci' => $req->dci,
                    'id_form' => $req->form,
                    'id_categorie' => $req->cat,
                    'sill' => $req->sill,
                ]);
             }
            return DB::table('products')->orderBy('id_product','DESC')->LIMIT(1)->get();
        }
    }

    public function addLot(request $req)
    {
        DB::table('lots')->insert([
            'code_lot' => $req->code,
            'date_fab' => $req->fab,
            'date_exp' => $req->exp,
            'price' => $req->vente,
            'qt' => null,
        ]);
        $data = DB::table('lots')->orderBy('id_lot','DESC')->LIMIT(1)->get();
        DB::table('pl')->insert([
            'id_product' => $req->id,
            'id_lot' => $data[0]->id_lot,
        ]);
        return $data;
    }

    public function getLotProductByStock(request $req)
    {
        $final = array();
        $data =  DB::table('pls')->where('id_stock',$req->id)->join('pl','pls.id_pl','=','pl.id_pl')->get();
        foreach ($data as  $value) {
            $product = DB::table('products')->where('id_product',$value->id_product)->first();
            $lots = DB::table('lots')->where('id_lot',$value->id_lot)->first();
            $final[] = array('id_pls' => $value->id_pls,'name_product' => $product->name_product,'code_lot' => $lots->code_lot,'date_fab' => $lots->date_fab,'date_exp' => $lots->date_exp
        ,'price' => $lots->price,'statu' => $value->statu,'qt_pls' => $value->qt_pls);
        }
        return $final;
    }

    public function lotByProduct(request $req)
    {
        $data =  DB::table('pl')->where('id_product',$req->idprd)->join('lots','lots.id_lot','=','pl.id_lot')
        ->orderBy('id_pl','DESC')->get();
        $infos = DB::table('products')->where('id_product',$req->idprd)->first();
        $data[] = $infos;
        return $data;
    }

    public function lotByProduct2(request $req)
    {
        return DB::table('pl')->where('id_product',$req->idprd)->join('lots','lots.id_lot','=','pl.id_lot')
        ->orderBy('id_pl','DESC')->get();
    }

    public function addproductlotForStock(request $req)
    {
        $chk = DB::table('pl')->where([['id_product','=',$req->idprd],['id_lot','=',$req->idlot]])->first();
        
        $lot = DB::table('lots')->where('id_lot',$chk->id_lot)->first();
        $ch = DB::table('pls')->where('id_pl',$chk->id_pl)->first();

        if($ch)
        {
            $compare = intval($lot->qt) - intval($ch->qt_pls);

        if($req->qt > $compare)
        {
            return $compare;
        }else{
            DB::table('pls')->insert([
                'id_stock' => $req->stock,
                'id_pl' => $chk->id_pl,
                'statu' => 0,
                'qt_pls' => $req->qt,
            ]);
            return "yes";
        }
        }else{
            if(intval($req->qt) > intval($lot->qt))
            {
                return $lot->qt;
            }else{
                DB::table('pls')->insert([
                    'id_stock' => $req->stock,
                    'id_pl' => $chk->id_pl,
                    'statu' => 0,
                    'qt_pls' => $req->qt,
                ]);
                return "yes";
            }
        }

    }

    public function deleteproductByStock(request $req)
    {
        DB::table('pls')->where('id_pls',$req->id_pls)->delete();
        return "yes";
    }

    public function getproductsjoincategorie()
    {
        return DB::table('products')
        ->join('categories','products.id_categorie','=','categories.id_categorie')->orderBy('id_product', 'DESC')->get();
    }

    public function addphoto(request $req)
    {
        $path = uniqid() . '.jpg';
             $req->image->storeAs('products',$path);
             DB::table('products')->where('id_product',$req->id)->update([
                'pic' => $path,
            ]);
            return "yes";
    }

    public function delphoto(request $req)
    {
        DB::table('products')->where('id_product',$req->id)->update([
            'pic' => null,
        ]);
        return "yes";
    }

    public function updateLot(request $req)
    {
        DB::table('lots')->where('id_lot',$req->id_lot)->update([
            'code_lot' => $req->code_lot,
            'date_fab' => $req->date_fab,
            'date_exp' => $req->date_exp,
            'price' => $req->price
        ]);
        return "yes";
    }

    public function getformdci(request $req)
    {
        return DB::table('products')->where('id_product',$req->id_product)
        ->join('forms','products.id_form','=','forms.id_form')
        ->join('dcis','products.id_dci','=','dcis.id_dci')->get();
    }

    public function addlotforProduct(request $req)
    {
        DB::table('lots')->insert([
            'code_lot' => $req->code,
            'date_fab' => $req->fab,
            'date_exp' => $req->exp,
            'price' => $req->price,
            'qt' => $req->qts,
        ]);
        $data = DB::table('lots')->orderBy('id_lot','DESC')->LIMIT(1)->get();

        DB::table('pl')->insert([
            'id_product' => $req->id,
            'id_lot' => $data[0]->id_lot,
        ]);

        return "yes";
    }

    public function deletelotforproduct(request $req)
    {
       foreach ($req->ids as  $value) {
           DB::table('lots')->where('id_lot',$value)->delete();
       }
       return "yes";
    }

    public function updateproduct(request $req)
    {
        DB::table('products')->where('id_product',$req->id_product)->update([
            'name_product' => $req->name_product,
            'id_dci' => $req->id_dci,
            'id_form' => $req->id_form,
            'id_categorie' => $req->id_categorie,
            'sill' => $req->sill,
        ]);
        return "yes";
    }

    public function deleteproducts(request $req)
    {
        foreach ($req->dds as $value) {
            DB::table('products')->where('id_product',$value)->delete();
        }
        return "yes";
    }


    public function getSingleproduct()
    {
        $ids = array();
        $final = array();
        $data = DB::table('pls')->where('statu',0)->join('pl','pls.id_pl','=','pl.id_pl')->get();
        foreach ($data as $value) {
            array_push($ids,$value->id_product);
        }

        $ids = array_count_values($ids);

        foreach ($ids as $key => $value) {
            $tmp = DB::table('products')->where('id_product',$key)->first();
            $final[] = $tmp;
        }
        return $final;

    }

    public function allProductsjoin()
    {
        $data = DB::table('products')->orderBy('id_product','DESC')->join('categories','products.id_categorie','=','categories.id_categorie')->get();
        return $data;
    }

    public function detailsByProduct($id = null)
    {
        $final = array();
        if($id == null)
        {
            return "no";
        }else{
            $pl = DB::table('pl')->where('id_product','=',$id)->join('lots','pl.id_lot','=','lots.id_lot')->get();
            foreach ($pl as  $value) {
                $data = DB::table('pls')->where('id_pl','=',$value->id_pl)->join('stock','stock.id_stock','=','pls.id_stock')->get();
                $final[] = array('pls' => $data,'pl' => $value);
            }
            return $final;
        }
    }

    public function updatePls(request $req)
    {
        foreach ($req->push as  $value) {
            DB::table('pls')->where('id_pls','=',$value['id_pls'])->update([
                'statu' => $value['statu'],
            ]);
            return "yes";
        }
    }
    
    

    
}

