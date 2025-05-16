<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
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
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    //Funcion para guardar un nuevo project
    public function saveNewProject(Request $request){
        if (Auth::check()) {
            $validated = $request->validate([
                'inputnewcfsprojectprojectid' => 'required|unique:cfs_project,project_id',
                'inputnewcfsprojectmonth' => 'required|date',
                'inputnewcfsprojectinvoice' => 'required',
                'inputnewcfspeojectdrayageperson' => 'required',
                'inputnewcfsprojectdrayagefiletype' => 'required',
            ],[
                'inputnewcfsprojectprojectid.required' => 'Project ID is required.',
                'inputnewcfsprojectprojectid.unique' => 'Project ID already exists.',
                'inputnewcfsprojectmonth.required' => 'Month is required.',
                'inputnewcfsprojectmonth.date' => 'Month must be a date.',
                'inputnewcfsprojectinvoice.required' => 'Invoice is required.',
                'inputnewcfspeojectdrayageperson.required' => 'Drayage Person is required.',
                'inputnewcfsprojectdrayagefiletype.required' => 'Drayage File Type is required.',
            ]);

            $month = Carbon::createFromFormat('m/d/Y', $request->inputnewcfsprojectmonth)->format('Y-m-d'); // Solo fecha

            Project::create([
                'project_id' => $request->inputnewcfsprojectprojectid,
                'month' => $month,
                'invoice' => $request->inputnewcfsprojectinvoice,
                'drayage_user' => $request->inputnewcfspeojectdrayageperson,
                'drayage_typefile' => $request->inputnewcfsprojectdrayagefiletype,
                'created_by'=> Auth::check() ? Auth::user()->username : 'system',
                'created_date' => now(),
                'status' => '1',
            ]);

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

            // Responder con éxito y devolver todos los proyectos con sus relaciones
            return response()->json([
                'message' => 'Project successfully added.',
                'projects' => $projects, // Devolver todos los proyectos con sus relaciones
            ], 200);
        }
        return redirect('/login');
    }

    //Funcion para borrar el project
    public function deleteProject(Request $request){
        if (Auth::check()) {
            // Validar que el project_id esté presente
            $request->validate([
                'project_id' => 'required|string',
            ]);

            // Obtener el proyecto por el ID
            //$project = Project::find($request->project_id);
            $project = Project::with(['masters.subprojects.hblreferences', 'masters.subprojects.pns'])->find($request->project_id);

            if ($project) {
                // Actualizar el estado del proyecto a 0
                $project->status = 0;
                $project->updated_by = Auth::check() ? Auth::user()->username : 'system';
                $project->transaction_date = now();
                $project->save();

                // Desactivar masters relacionados
                foreach ($project->masters as $master) {
                    $master->status = 0;
                    $master->updated_by = Auth::user()->username;
                    $master->transaction_date = now();
                    $master->save();

                    // Desactivar subprojects relacionados
                    foreach ($master->subprojects as $subproject) {
                        $subproject->status = 0;
                        $subproject->updated_by = Auth::user()->username;
                        $subproject->transaction_date = now();
                        $subproject->save();

                        // Eliminar hbl_references
                        foreach ($subproject->hblreferences as $hbl) {
                            $hbl->delete();
                        }

                        // Desactivar part numbers (pns)
                        DB::table('cfs_h_pn')
                        ->where('fk_hbl', $subproject->hbl) // o la clave foránea que tengas que relacione con el subproject
                        ->update([
                            'status' => 0,
                            'updated_by' => Auth::user()->username,
                            'transaction_date' => now(),
                        ]);
                    }
                }

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
                
                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Project deleted successfully.',
                    'projects' => $projects,
                ]);
            } else {
                // Si no se encuentra el proyecto, responder con error
                return response()->json(['success' => false, 'message' => 'Project not found.']);
            }
        }
        return redirect('/login');
    }

    //Funcion para editar un project
    public function editNewProject(Request $request){
        if (Auth::check()) {
            $originalId = $request->input('inputnewcfsprojectprojectidoriginal');

            $validated = $request->validate([
                //'inputnewcfsprojectprojectid' => 'required|unique:cfs_project,project_id',
                'inputnewcfsprojectprojectid' => [
                    'required',
                    Rule::unique('cfs_project', 'project_id')->ignore($originalId, 'project_id')
                ],
                'inputnewcfsprojectmonth' => 'required|date',
                'inputnewcfsprojectinvoice' => 'required',
                'inputnewcfspeojectdrayageperson' => 'required',
                'inputnewcfsprojectdrayagefiletype' => 'required',
            ],[
                'inputnewcfsprojectprojectid.required' => 'Project ID is required.',
                'inputnewcfsprojectprojectid.unique' => 'Project ID already exists.',
                'inputnewcfsprojectmonth.required' => 'Month is required.',
                'inputnewcfsprojectmonth.date' => 'Month must be a date.',
                'inputnewcfsprojectinvoice.required' => 'Invoice is required.',
                'inputnewcfspeojectdrayageperson.required' => 'Drayage Person is required.',
                'inputnewcfsprojectdrayagefiletype.required' => 'Drayage File Type is required.',
            ]);

            $month = Carbon::createFromFormat('m/d/Y', $request->inputnewcfsprojectmonth)->format('Y-m-d'); // Solo fecha
            
            // Obtener el proyecto por el ID
            //$project = Project::find($request->inputnewcfsprojectprojectid);
            $project = Project::find($originalId);

            if ($project) {
                // Guardamos el project_id original antes del cambio
                $oldProjectId = $project->project_id;
                $newProjectId = $request->inputnewcfsprojectprojectid;

                // Actualizar el project
                $project->project_id = $newProjectId;
                $project->drayage_user = $request->inputnewcfspeojectdrayageperson;
                $project->drayage_typefile = $request->inputnewcfsprojectdrayagefiletype;
                $project->invoice = $request->inputnewcfsprojectinvoice;
                $project->month = $month;
                $project->updated_by = Auth::check() ? Auth::user()->username : 'system';
                $project->transaction_date = now();
                $project->save();

                // Si el project_id cambió, actualizamos los masters relacionados
                if ($oldProjectId !== $newProjectId) {
                    DB::table('cfs_master')
                        ->where('fk_project_id', $oldProjectId)
                        ->where('status', 1) // condición adicional
                        ->update(['fk_project_id' => $newProjectId]);
                }

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

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Project updated successfully.',
                    'projects' => $projects,
                ]);
            } else {
                // Si no se encuentra el proyecto, responder con error
                return response()->json(['success' => false, 'message' => 'Project not found.']);
            }
        }
        return redirect('/login');
    }

}
