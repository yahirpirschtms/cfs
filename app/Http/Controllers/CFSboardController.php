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
            $projects = Project::select('project_id', 'month', 'invoice', 'drayage_user', 'drayage_typefile')
            ->with([
                'masters' => function ($q) {
                    $q->where('status', '1')
                    ->select('mbl', 'fk_project_id', 'container_number', 'total_pieces', 'total_pallets', 'eta_port', 'arrival_date', 'lfd')
                    ->with([
                        'subprojects' => function ($q) {
                            $q->where('status', '1')
                            ->select('hbl', 'fk_mbl', 'cfs_comment','customs_release_comment', 'arrival_date', 'lfd', 'out_date_cr')
                            ->with([
                                'cfscommentRelation:gnct_id,gntc_value,gntc_description',
                                'customreleaseRelation:gnct_id,gntc_value,gntc_description',
                            ]);
                        }
                    ]);
                },
                'drayageUserRelation' => function ($q) {
                    $q->select('gnct_id', 'gntc_value', 'gntc_description')
                    ->where('gntc_status', '1');
                },
                'drayageFileRelation' => function ($q) {
                    $q->select('gnct_id', 'gntc_value', 'gntc_description')
                    ->where('gntc_status', '1');
                },
                'invoiceRelation' => function ($q) {
                    $q->select('gnct_id', 'gntc_value', 'gntc_description')
                    ->where('gntc_status', '1');
                },
            ])
            ->where('status', '1')
            ->get();      

            return view('home.cfsboard', compact('projects'));  // Pasamos los proyectos a la vista
        }

        return redirect('/login');
    }
}
