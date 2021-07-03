<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class achatcrmController extends Controller
{
    public function fournisseurs()
    {
        return DB::table('fournisseurs')->count();
    }
}
