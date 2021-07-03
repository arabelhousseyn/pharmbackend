<?php

namespace App\Http\Controllers;
use App\Mail\sendVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\clients;
class clientController extends Controller
{

    
    public function registerClient(request $req)
    {
            $token = Str::random(60);
            $code = rand(1000,10000);
            $url = "http://localhost:8080/verification/?sk=" .$token;
            $details = [
                'title' => 'Verification email',
                'body' => 'votre code d\'activation ' . $code . ' appuyez sur ce lien pour active votre compte <a href="'.$url.'">click</a>' 
            ];

            $client = new clients;
            $client->fname = $req->fname;
            $client->lname = $req->lname;
            $client->username =$req->username;
            $client->phone = $req->phone;
            $client->email = $req->mail;
            $client->password = Hash::make($req->repass);
            $client->token = $token;
            $client->code = $code;
            $client->active = 1;
            $client->save();
            Mail::to($client->email)->send(new sendVerificationMail($details));
            return $token;
       
    }

    public function verifyToken(request $req)
    {
        $client = clients::where('token',$req->token)->get();
        if(count($client) > 0)
        {
            if($client[0]->code == $req->code)
            {
                clients::where('id_clients',$client[0]->id_clients)->update([
                    'code' => null,
                    'token' => null,
                    'active' => 0
                ]);

                return "yes";
            }else{
                return "nocode";
            }
        }else{
            return "no";
        }

    }

    public function login(request $req)
    {
        $bool = false;

            $client = clients::where('username',$req->iden)->get();
            if(count($client) > 0)
            {
                if(count($client) > 0)
                {
                    if($client[0]->active == 0)
                    {
                        
                        if(Hash::check($req->pass,$client[0]->password))
                        {
                            return $client[0]->id_clients;
                        }else{
                            return "nopass";
                        }
                    }elseif($client[0]->active == 1){
            $token = Str::random(60);
            $code = rand(1000,10000);
            $url = "http://localhost:8080/verification/?sk=" .$token;
            $details = [
                'title' => 'Verification email',
                'body' => 'votre code d\'activation ' . $code . ' appuyez sur ce lien pour active votre compte <a href="'.$url.'">click</a>' 
            ];
            clients::where('id_clients',$client[0]->id_clients)->update([
                'code' => $code,
                'token' => $token
            ]);
            Mail::to($client[0]->email)->send(new sendVerificationMail($details));
                        return $token;
                    }else{
                        return "noactive";
                    }

                }else{
                    return "no";
                }
            }else{
                $bool = true;
            }

            if($bool)
            {
                $client = clients::where('phone',$req->iden)->get(); 

                if(count($client) > 0)
                {
                    if($client[0]->active == 0)
                    {
                        if(Hash::check($req->pass,$client[0]->password))
                        {
                            return $client[0]->id_clients;
                        }else{
                            return "nopass";
                        }
                    }elseif($client[0]->active == 1){
                        $token = Str::random(60);
            $code = rand(1000,10000);
            $url = "http://localhost:8080/verification/?sk=" .$token;
            $details = [
                'title' => 'Verification email',
                'body' => 'votre code d\'activation ' . $code . ' appuyez sur ce lien pour active votre compte <a href="'.$url.'">click</a>' 
            ];
            clients::where('id_clients',$client[0]->id_clients)->update([
                'code' => $code,
                'token' => $token
            ]);
            Mail::to($client[0]->email)->send(new sendVerificationMail($details));
                        return $token;
                    }else{
                        return "noactive";
                    }

                }else{
                    return "no";
                }
            }
    }

    public function getClient(request $req)
    {
        return clients::where('id_clients',$req->id)->get();
    }

    public function recoveryMail(request $req)
    {
        $client = clients::where('email',$req->mail)->get();
        if(count($client) > 0)
        {
            $token = Str::random(60);
            $url = "http://localhost:8080/forget/?sk=" .$token . '&mail=' . $req->mail; // to be changed
            $details = [
                'title' => 'Verification email',
                'body' => ' appuyez sur ce lien pour active votre compte <a href="'.$url.'">click</a>' 
            ];
            clients::where('id_clients',$client[0]->id_clients)->update([
                'token' => $token
            ]);
            Mail::to($client[0]->email)->send(new sendVerificationMail($details));
            return $token;
        }else{
            return "no";
        }
    }

    public function recovery(request $req)
    {
      $client = clients::where('email',$req->mail)->get();

      clients::where('email',$req->mail)->update([
          'password' => Hash::make($req->repass),
          'token' => null,
          'code' => null,
      ]);

      return "yes";

    }

    public function getclientByName(request $req)
    {
        $client = clients::where('fname',$req->fname)->get();
        if(@count($client) > 0)
        {
            return $client;
        }else{
            return "no";
        }
    }

    public function updateClientById(request $req)
    {

        if($req->fname != null)
        {
            clients::where('id_clients',$req->id)->update([
                'fname' => $req->fname
            ]);
        }

        if($req->lname != null)
        {
            clients::where('id_clients',$req->id)->update([
                'lname' => $req->lname
            ]);
        }

        if($req->phone != null)
        {
            clients::where('id_clients',$req->id)->update([
                'phone' => $req->phone
            ]);
        }

        if($req->email != null)
        {
            clients::where('id_clients',$req->id)->update([
                'email' => $req->email
            ]);
        }

        if($req->phone != null)
        {
            clients::where('id_clients',$req->id)->update([
                'phone' => $req->phone
            ]);
        }

        if($req->adrs != null)
        {
            clients::where('id_clients',$req->id)->update([
                'adresse' => $req->adrs
            ]);
        }

        return "yes";
    }

    public function changePassword(request $req)
    {
        $client = clients::where('id_clients',$req->id)->get();

        if(Hash::check($req->old,$client[0]->password))
        {
            clients::where('id_clients',$req->id)->update([
                'password' => Hash::make($req->rnew)
            ]);
            return "yes";
        }else{
            return "no";
        }
    }

    public function restrict(request $req)
    {
       clients::where('id_clients',$req->id)->update(['active' => 2]);
       return "yes";
    }


    public function searched(request $req)
    {
        return DB::table('searched')->where('id_client',$req->id)->orderBy('id','desc')->limit(5)->get();
    }

    public function insertKEY(request $req)
    {
        return DB::table('searched')->insert([
            'id_client' => $req->id,
            'key_word' => $req->key,
            'id_categorie' => $req->pushcats
        ]);
        return "yes";
    }

    public function getsearch(request $req)
    {
        $params = explode(';',$req->name);

        if($params[1] == "none")
        {
            return DB::table('products')->where('name_product','like','%'.$params[0].'%')->get();
        }else{
           $searched = DB::table('searched')->where('id_client',$params[1])->orderBy('id','desc')->limit(1)->get();
           if($searched[0]->id_categorie == null)
           {
            return DB::table('products')->where('name_product','like','%' .$searched[0]->key_word . '%')->get(); 
           }else{
            return DB::table('products')->where([['name_product','like','%' .$searched[0]->key_word . '%'],['id_categorie',$searched[0]->id_categorie]])->get(); 
           } 
        }
    }

    public function getAllclient()
    {
        return DB::table('clients')->orderBy('id_clients','DESC')->get();
    }

    public function addClient(request $req)
    {
        $date = date('Y-m-d H:i:s');
        $checkUser = DB::table('clients')->where('username',$req->username)->get();
        $checkph = DB::table('clients')->where('phone',$req->phone)->get();

        if(@count($checkUser) > 0)
        {
           return "nousername";
        }else{
            if(@count($checkph) > 0)
        {
           return "nophone";
        }else{
            DB::table('clients')->insert([
                'fname' => $req->fname,
                'lname' => $req->lname,
                'username' => $req->username,
                'email' => $req->email,
                'phone' => $req->phone,
                'adresse' => $req->adresse,
                'password' => Hash::make($req->repass),
                'active' => 0,
                'token' => null,
                'code' => null,
                'date_creation' => $date
            ]);
            return "yes";
        }
        }
    }

    public function updateClient(request $req)
    {
        $checkUser = DB::table('clients')->where([['username',$req->username],['id_clients','<>',$req->id_clients]])->get();
        $checkph = DB::table('clients')->where([['phone',$req->phone],['id_clients','<>',$req->id_clients]])->get();

        if(@count($checkUser) > 0)
        {
           return "nousername";
        }else{
            if(@count($checkph) > 0)
        {
           return "nophone";
        }else{
            DB::table('clients')->where('id_clients',$req->id_clients)->update([
                'fname' => $req->fname,
                'lname' => $req->lname,
                'username' => $req->username,
                'email' => $req->email,
                'phone' => $req->phone,
                'adresse' => $req->adresse,
            ]);
            return "yes";
        }
        } 
    }

    public function susingleClinet(request $req)
    {
        DB::table('clients')->where('id_clients',$req->id_clients)->update([
            'active' => 1,
        ]);

        return "yes";
    }

    public function reactive(request $req)
    {
        DB::table('clients')->where('id_clients',$req->id_clients)->update([
            'active' => 0,
        ]);

        return "yes";
    }

    public function getAllreclamation()
    {
        return DB::table('reclamation')->join('commandes','reclamation.id_commande','=','commandes.id_commande')
        ->join('clients','reclamation.id_client','=','clients.id_clients')->orderBy('id_rec','DESC')->get();
    }

    public function setReclamation(request $req)
    {
        $date = date('Y-m-d H:i:s');
        DB::table('reclamation')->insert([
            'id_commande' => $req->id_commande,
            'id_client' => $req->id_client,
            'note' => $req->msg,
            'feedback' => '',
            'statu_rec' =>  0,
            'date_note' => $date
        ]);
        return "yes";
    }

    public function getreclamantionsByClient(request $req)
    {
        $id = $req->id;
        return DB::table('reclamation')->join('commandes','reclamation.id_commande','=','commandes.id_commande')
        ->join('clients', function ($join) use($id) {
            $join->on('reclamation.id_client', '=', 'clients.id_clients')
                 ->where('clients.id_clients', '=', $id);
        })->get();
    }

    public function countAll()
    {
        return DB::table('clients')->count();
    }

    public function countAllrec()
    {
        return DB::table('reclamation')->where('statu_rec',0)->count();
    }

    public function feedback(request $req)
    {
        DB::table('reclamation')->where('id_rec',$req->id)->update([
            'feedback' => $req->rep,
            'statu_rec' => $req->statu,
        ]);
        return "yes";
    }

    public function feddbackbycommande(request $req)
    {
        return DB::table('reclamation')->where([['id_commande','=',$req->id_commande],['id_client','=',$req->id_client]])->get();

    }

    

}
