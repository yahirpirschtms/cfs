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

    //Funcion añadir nuevos masters
    public function saveNewMaster(Request $request){
        if(Auth::check()) {
            $validated = $request->validate([
                'inputnewmastercfsmbl' => 'required|unique:cfs_master,mbl',
                'inputnewmastercfsetaport' => 'required|date',
                'inputnewmastercfsarrivaldate' => 'required|date',
                'inputnewmastercfslfd' => 'required|date',
                'inputnewmastercfscontainernumber' => 'required',
                'inputnewmastercfsnotes' => 'nullable',
                'inputnewmastercfsproyectid' => 'required',
                
            ],[
                'inputnewmastercfsmbl.unique' => 'MBL already exists.',
                'inputnewmastercfsmbl.required' => 'MBL is required.',
                'inputnewmastercfsetaport.required' => 'ETA Port is required.',
                'inputnewmastercfsetaport.date' => 'ETA Port must be a date.',
                'inputnewmastercfsarrivaldate.required' => 'Arrival Date is required.',
                'inputnewmastercfsarrivaldate.date' => 'Arrival Date must be a date.',
                'inputnewmastercfslfd.required' => 'LFD is required.',
                'inputnewmastercfslfd.date' => 'LFD must be a date.',
                'inputnewmastercfscontainernumber.required' => 'Container Number is required.',
                'inputnewmastercfsnotes.required' => 'Notes is required.',
                'inputnewmastercfsproyectid.required' => 'Project ID is required.',
            ]);

            $etaport = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsetaport)->format('Y-m-d H:i:s');
            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfslfd)->format('Y-m-d H:i:s');


            // Obtener el proyecto por el ID
            $project = Project::find($request->inputnewmastercfsproyectid);

            if ($project) {
                Master::create([
                    'mbl' => $request->inputnewmastercfsmbl,
                    'fk_project_id' => $request->inputnewmastercfsproyectid,
                    'container_number' => $request->inputnewmastercfscontainernumber,
                    'notes' => $request->inputnewmastercfsnotes,
                    'eta_port' => $etaport,
                    'arrival_date' => $arrivaldate,
                    'lfd' => $lfd,
                    'created_by'=> Auth::check() ? Auth::user()->username : 'system',
                    'created_date' => now(),
                    'status' => '1',
                ]);

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->inputnewmastercfsproyectid)
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
                    'message' => 'Master successfully added.',
                    'masters' => $masters, // Devolver todos los proyectos con sus relaciones
                    'projects' => $projects,
                ], 200);
            } else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Project not found.']);
            }
        }
        return redirect('/login');
    }

    //Funcion para editar un project
    public function editNewMaster(Request $request){
        if (Auth::check()) {
            $originalId = $request->input('inputnewmastercfsmbloriginal');

            $validated = $request->validate([
                'inputnewmastercfsmbl' => [
                    'required',
                    Rule::unique('cfs_master', 'mbl')->ignore($originalId, 'mbl')
                ],
                'inputnewmastercfsetaport' => 'required|date',
                'inputnewmastercfsarrivaldate' => 'required|date',
                'inputnewmastercfslfd' => 'required|date',
                'inputnewmastercfscontainernumber' => 'required',
                'inputnewmastercfsnotes' => 'nullable',
                'inputnewmastercfsproyectid' => 'required',
                
            ],[
                'inputnewmastercfsmbl.required' => 'MBL is required.',
                'inputnewmastercfsmbl.unique' => 'MBL already exists.',
                'inputnewmastercfsetaport.required' => 'ETA Port is required.',
                'inputnewmastercfsetaport.date' => 'ETA Port must be a date.',
                'inputnewmastercfsarrivaldate.required' => 'Arrival Date is required.',
                'inputnewmastercfsarrivaldate.date' => 'Arrival Date must be a date.',
                'inputnewmastercfslfd.required' => 'LFD is required.',
                'inputnewmastercfslfd.date' => 'LFD must be a date.',
                'inputnewmastercfscontainernumber.required' => 'Container Number is required.',
                'inputnewmastercfsnotes.required' => 'Notes is required.',
                'inputnewmastercfsproyectid.required' => 'Project ID is required.',
            ]);

            $etaport = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsetaport)->format('Y-m-d H:i:s');
            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfslfd)->format('Y-m-d H:i:s');

            // Obtener el proyecto por el ID
            $masters = Master::find($originalId);

            if ($masters) {
                // Guardamos el MBL original antes del cambio
                $oldMasterMBL = $masters->mbl;
                $newMasterMBL = $request->inputnewmastercfsmbl;

                // Actualizar el project
                $masters->mbl = $newMasterMBL;
                $masters->notes = $request->inputnewmastercfsnotes;
                $masters->container_number = $request->inputnewmastercfscontainernumber;
                $masters->eta_port = $etaport;
                $masters->arrival_date = $arrivaldate;
                $masters->lfd = $lfd;
                $masters->updated_by = Auth::check() ? Auth::user()->username : 'system';
                $masters->transaction_date = now();
                $masters->save();

                // Si el MBL cambió, actualizamos los subprojects relacionados
                if ($oldMasterMBL !== $newMasterMBL) {
                    DB::table('cfs_subprojects')
                        ->where('fk_mbl', $oldMasterMBL)
                        ->where('status', 1) // condición adicional
                        ->update(['fk_mbl' => $newMasterMBL]);
                }

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->inputnewmastercfsproyectid)
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
                ])
                ->get();;

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

                // Responder con éxito y devolver todos los master con sus relaciones
                return response()->json([
                    'success' => true,
                    'message' => 'Master successfully updated.',
                    'masters' => $masters, // Devolver todos los proyectos con sus relaciones
                    'projects' => $projects,
                ], 200);
            } else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Master not found.']);
            }
        }
        return redirect('/login');
    }

    //Funcion para borrar el master
    public function deleteMaster(Request $request){
        if (Auth::check()) {
            // Validar que el mbl esté presente
            $request->validate([
                'mbl' => 'required|string',
                'project_id' => 'required|string',
            ]);

            // Obtener el Master por el ID
            //$master = Master::find($request->mbl);
            $master = Master::with(['subprojects.hblreferences','subprojects.pns'])->find($request->mbl);

            if ($master) {
                // Actualizar el estado del master a 0
                $master->status = 0;
                $master->updated_by = Auth::check() ? Auth::user()->username : 'system';
                $master->transaction_date = now();
                $master->save();

                // Procesar subprojects relacionados
                foreach ($master->subprojects as $subproject) {
                    // Cambiar status del subproject
                    $subproject->status = 0;
                    $subproject->updated_by = Auth::user()->username;
                    $subproject->transaction_date = now();
                    $subproject->save();

                    // Eliminar HBL references
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
                ])
                ->get();
                
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

                // Responder con éxito y los masters actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Master deleted successfully.',
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
