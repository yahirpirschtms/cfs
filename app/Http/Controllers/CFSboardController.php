<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Master;
use App\Models\Subproject;
use App\Models\Costumer;
use App\Models\Partnumber;
use App\Models\Service;
use App\Models\HouseService;
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
                                },
                                'services' => function ($q) {
                                    $q->where('cfs_services.status', '1'); // Filtrar partnumbers con status 1
                                },
                                'hblreferences' => function ($q) { // <- añade esta parte
                                    $q->where('cfs_hbl_references.status', '1');
                                },
                                'cfscommentRelation',
                                'customreleaseRelation',
                            ]);
                        }
                    ]);
                },
                'drayageUserRelation',
                'drayageFileRelation',
                'invoiceRelation',
            ])
            ->where('status', '1')
            ->get();        

            return view('home.cfsboard', compact('projects'));  // Pasamos los proyectos a la vista
        }

        return redirect('/login');
    }
}
