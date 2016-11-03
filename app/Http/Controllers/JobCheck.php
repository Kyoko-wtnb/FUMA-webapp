<?php

namespace IPGAP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IPGAP\Http\Requests;
use IPGAP\Http\Controllers\Controller;

class JobCheck extends Controller
{
    public function index(Request $request){
      $email = $request -> input('Email');
      $jobtitle = $request -> input('jobtitle');

      if(!filter_var($email, FILTER_VALIDATE_EMAIL)===false){
        $results = DB::select('SELECT * FROM jobs WHERE email=?', [$email]);
        $exists = false;
        foreach($results as $row){
          if($row->title==$jobtitle){
            $exists = true;
            break;
          }
        }
        if($exists){return "2";}
        else{return "1";}
      }else{
        return "3";
      }
    }
}
