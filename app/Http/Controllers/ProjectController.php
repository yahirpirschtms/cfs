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

            // Responder con éxito y devolver todos los proyectos con sus relaciones
            return response()->json([
                'message' => 'Project successfully added.',
                'projects' => $projects, // Devolver todos los proyectos con sus relaciones
            ], 200);
        }
        return redirect('/login');
    }

    //Funcion para borrar el project
    /*public function deleteProject(Request $request){
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
                        $q->where('status', '1')
                        ->select('mbl', 'fk_project_id', 'container_number', 'total_pieces', 'total_pallets', 'eta_port', 'arrival_date', 'lfd')
                        ->with([
                            'subprojects' => function ($q) {
                                $q->where('status', '1')
                                ->select('hbl', 'fk_mbl', 'subprojects_id', 'cfs_comment','customs_release_comment', 'arrival_date', 'lfd', 'out_date_cr')
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
    }*/

    //Funcion para borrar el project mas rapida
    public function deleteProject(Request $request){
        if (Auth::check()) {
            // Validar que el project_id esté presente
            $request->validate([
                'project_id' => 'required|string',
            ]);

            // Obtener el proyecto por el ID
            //$project = Project::with(['masters.subprojects.hblreferences', 'masters.subprojects.pns'])->find($request->project_id);
            $project = Project::select('project_id', 'status', 'updated_by', 'transaction_date') // Solo los campos que usas
            ->with([
                'masters' => function ($q) {
                    $q->select('mbl', 'fk_project_id', 'status', 'updated_by', 'transaction_date')
                    ->with([
                        'subprojects' => function ($q) {
                            $q->select('hbl', 'fk_mbl', 'status', 'updated_by', 'transaction_date')
                                ->with([
                                    'hblreferences:pk_hbl_reference,fk_hbl', // solo ID y la clave foránea
                                    'pns:pk_part_number'            // igual aquí
                                ]);
                        }
                    ]);
                }
            ])
            ->find($request->project_id);

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
    /*public function editNewProject(Request $request){
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
            $project = Project::select('project_id', 'status', 'updated_by', 'transaction_date', 'drayage_user', 'drayage_typefile', 'invoice', 'month')
            ->find($originalId);

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

                //Si el project_id cambió, actualizamos los masters relacionados
                if ($oldProjectId !== $newProjectId) {
                    DB::table('cfs_master')
                        ->where('fk_project_id', $oldProjectId)
                        ->where('status', 1) // condición adicional
                        ->update(['fk_project_id' => $newProjectId]);
                //DB::statement("UPDATE cfs_master SET fk_project_id = ? WHERE fk_project_id = ? AND status = 1", [
                //    $newProjectId, $oldProjectId
                //]);
                }

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
    }*/

    //Funcion para editar un project mas rapida
    public function editNewProject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $originalId = $request->input('inputnewcfsprojectprojectidoriginal');

        $validated = $request->validate([
            'inputnewcfsprojectprojectid' => [
                'required',
                Rule::unique('cfs_project', 'project_id')->ignore($originalId, 'project_id')
            ],
            'inputnewcfsprojectmonth' => 'required|date',
            'inputnewcfsprojectinvoice' => 'required',
            'inputnewcfspeojectdrayageperson' => 'required',
            'inputnewcfsprojectdrayagefiletype' => 'required',
        ], [
            'inputnewcfsprojectprojectid.required' => 'Project ID is required.',
            'inputnewcfsprojectprojectid.unique' => 'Project ID already exists.',
            'inputnewcfsprojectmonth.required' => 'Month is required.',
            'inputnewcfsprojectmonth.date' => 'Month must be a date.',
            'inputnewcfsprojectinvoice.required' => 'Invoice is required.',
            'inputnewcfspeojectdrayageperson.required' => 'Drayage Person is required.',
            'inputnewcfsprojectdrayagefiletype.required' => 'Drayage File Type is required.',
        ]);

        $month = Carbon::createFromFormat('m/d/Y', $request->inputnewcfsprojectmonth)->format('Y-m-d');
        $newProjectId = $request->inputnewcfsprojectprojectid;
        $username = Auth::user()->username ?? 'system';

        // Verificar existencia rápido
        $projectExists = Project::where('project_id', $originalId)->exists();

        if (!$projectExists) {
            return response()->json(['success' => false, 'message' => 'Project not found.']);
        }

        DB::transaction(function () use ($originalId, $newProjectId, $request, $month, $username) {
            // Actualizar proyecto directo
            Project::where('project_id', $originalId)->update([
                'project_id' => $newProjectId,
                'drayage_user' => $request->inputnewcfspeojectdrayageperson,
                'drayage_typefile' => $request->inputnewcfsprojectdrayagefiletype,
                'invoice' => $request->inputnewcfsprojectinvoice,
                'month' => $month,
                'updated_by' => $username,
                'transaction_date' => now(),
            ]);

            // Actualizar fk_project_id en cfs_master solo si cambió project_id
            if ($originalId !== $newProjectId) {
                DB::table('cfs_master')
                    ->where('fk_project_id', $originalId)
                    ->where('status', 1)
                    ->update(['fk_project_id' => $newProjectId]);
            }
        });

        // Cargar todos los proyectos con sus relaciones necesarias
        $projects = Project::select('project_id', 'month', 'invoice', 'drayage_user', 'drayage_typefile')
            ->with([
                'masters' => function ($q) {
                    $q->where('status', '1')
                        ->select('mbl', 'fk_project_id', 'container_number', 'total_pieces', 'total_pallets', 'eta_port', 'arrival_date', 'lfd')
                        ->with([
                            'subprojects' => function ($q) {
                                $q->where('status', '1')
                                    ->select('hbl', 'fk_mbl', 'cfs_comment', 'customs_release_comment', 'arrival_date', 'lfd', 'out_date_cr')
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

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'projects' => $projects,
        ]);
    }

}
