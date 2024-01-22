<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    public function index(){
        
        return view('welcome');
    }

    public function getData(){


        // $data =  DB::table('fg_attribute')->get();

         $show = DB::table('fg_attribute')->first();

         return  response()->json($show);
        
        // dd($data);
    }
}
