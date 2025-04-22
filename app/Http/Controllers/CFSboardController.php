<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Master;
use App\Models\Subproject;
use App\Models\Costumer;
use App\Models\Partnumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CFSboardController extends Controller
{
    //Entrar al CFS Board project list
    public function cfsboard(){
        if (Auth::check()) {
            // Obtener todos los proyectos con sus relaciones necesarias
            $projects = Project::with([
                'masters' => function ($q) {
                    $q->where('status', '1')->with([
                        'subprojects' => function ($q) {
                            $q->where('status', '1')->with([
                                'costumer' => function ($q) {
                                    $q->where('cfs_customer.status', '1');
                                },
                                'pns' => function ($q) { // <- Aquí es la clave
                                    $q->where('cfs_pn.status', '1');
                                }
                            ]);
                        }
                    ]);
                },
                'drayageUserRelation',
                'drayageFileRelation',
            ])
            ->where('status', '1')
            ->get();        

            return view('home.cfsboard', compact('projects'));  // Pasamos los proyectos a la vista
        }

        return redirect('/login');
    }
}
