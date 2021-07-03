<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class notificationController extends Controller
{
    public function getNotifications()
    {
        $final = array();
        $data =  DB::table('notifications')->where('notif_statu',0)->join('commandes','notifications.id_commande','=','commandes.id_commande')->get();
        foreach ($data as  $value) {
            $tmp = DB::table('clients')->where('id_clients',$value->id_client)->select('fname','lname')->first();
            $final[] = array(
               'notif' => $value,
               'client' => $tmp,
            );

            DB::table('notifications')->where('id_notification',$value->id_notification)->update([
                'pushed' => 1
            ]);
        }
        return $final;
    }

    public function mark()
    {
        return DB::table('notifications')->where('notif_statu',0)->update([
            'notif_statu' => 1
        ]);
    }
}
