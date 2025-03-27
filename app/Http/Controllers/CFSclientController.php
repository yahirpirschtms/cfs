<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CFSclientController extends Controller
{
    public function cfsclient(){
        if (Auth::check()) {
            return view('home.cfsclient');
            // Verificamos si los datos provienen del botón o no
            /*$from_button = session('from_button', 0);
            $origin = session('shipment_origin');
            $trailerId = session('trailer_id');
            $status =  session('date_of_status');

            return view('home.trafficworkflowstart', compact('origin', 'trailerId', 'status', 'from_button'));*/
        }

        return redirect('/login');
    }
}
