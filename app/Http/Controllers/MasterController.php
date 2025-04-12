<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Subproject;
use App\Models\Costumer;
use App\Models\Partnumber;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MasterController extends Controller
{
    //Funcion para obtener los masters del project
    public function getProjectMasters(Request $request){
        if (Auth::check()) {
            // Validar que el project_id esté presente
            $request->validate([
                'project_id' => 'required|string',
            ]);

            // Obtener el proyecto por el ID
            $project = Project::find($request->project_id);

            if ($project) {

                // Obtener directamente los masters relacionados al proyecto
                $masters = Master::where('fk_project_id', $request->project_id)
                ->where('status', '1')
                ->with([
                    'subprojects' => function ($q) {
                        $q->where('status', '1')->with([
                            'costumer' => function ($q) {
                                $q->where('status', '1')->with([
                                    'partnumbers' => function ($q) {
                                        $q->where('status', '1');
                                    }
                                ]);
                            }
                        ]);
                    }
                ])
                ->get();

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Masters successfully founded.',
                    'masters' => $masters,
                ]);
            } else {
                // Si no se encuentra el proyecto, responder con error
                return response()->json(['success' => false, 'message' => 'Project not found.']);
            }
        }
        return redirect('/login');
    }
}
