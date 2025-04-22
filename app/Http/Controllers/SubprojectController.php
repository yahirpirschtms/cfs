<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Subproject;
use App\Models\Costumer;
use App\Models\Partnumber;
use App\Models\Pn;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SubprojectController extends Controller
{
    //Funcion para obtener los subprojects del master
    public function getMastersSubprojects(Request $request){
        if (Auth::check()) {
            // Validar que el mbl esté presente
            $request->validate([
                'mbl' => 'required|string',
                'project_id' => 'required|string',
            ]);

            // Obtener el Master por el ID
            $master = Master::find($request->mbl);

            if ($master) {

                // Obtener los subprojects relacionados al mbl
                $subprojects = Subproject::where('fk_mbl', $request->mbl)
                ->where('status', '1')
                ->with([
                    'costumer' => function ($q) {
                        $q->where('status', '1');
                    },
                    'pns' => function ($q) {
                        $q->where('cfs_pn.status', '1'); // Filtrar partnumbers con status 1
                    }
                ])
                ->get();

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->project_id)
                ->where('status', '1')
                ->with([
                    'subprojects' => function ($q) {
                        $q->where('status', '1')
                        ->with([
                            'costumer' => function ($q) {
                                $q->where('cfs_customer.status', '1');
                            },
                            'pns' => function ($q) {
                                $q->where('cfs_pn.status', '1'); // evitar ambigüedad
                            }
                        ]);
                    }
                ])
                ->get();

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

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Subprojects successfully founded.',
                    'subprojects' => $subprojects,
                    'masters' => $masters,
                    'projects' => $projects,
                ]);
            } else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Master not found.']);
            }
        }
        return redirect('/login');
    }
}
