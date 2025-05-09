<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Subproject;
use App\Models\Costumer;
use App\Models\Partnumber;
use App\Models\Service;
use App\Models\HouseService;
use App\Models\HblReferences;
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
                    },
                    'services' => function ($q) {
                        $q->where('cfs_services.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'hblreferences' => function ($q) { // <- añade esta parte
                        $q->where('cfs_hbl_references.status', '1');
                    },
                    'cfscommentRelation',
                    'customreleaseRelation',
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

    //Funcion añadir nuevo subproject
    public function saveNewSubproject(Request $request){
        if(Auth::check()) {
            $validated = $request->validate([
                'inputnewsubprojectproyectid' => 'required',
                'inputnewsubprojectcfsmbl' => 'required',
                'inputnewsubprojectcfssubprojectid' => 'required',
                'inputnewsubprojectcfshbl' => 'required|unique:cfs_subprojects,hbl',
                // Validación de los nuevos arreglos (hbl_references y part_numbers)
                'hbl_references' => 'nullable|array',
                'hbl_references.*' => 'nullable|string|required_without:hbl_references', // Si hay algún valor, no debe ser vacío

                'part_numbers' => 'nullable|array',
                'part_numbers.*' => 'nullable|string|required_without:part_numbers', // Si hay algún valor, no debe ser vacío

                'inputnewsubprojectcfspieces' => 'required|numeric|min:0',
                'inputnewsubprojectcfsworkspalletized' => 'required',
                'inputnewsubprojectcfspalletsexchanged' => 'required',
                'inputnewsubprojectcfspallets' => 'required|numeric|min:0',
                'inputnewsubprojectcfspalletizedcharges' => 'required',
                'inputnewsubprojectcfscustomer' => 'required',
                'checkAgent' => 'nullable',

                'inputnewsubprojectcfscfscomment' => 'required',
                'inputnewsubprojectcfsarrivaldate' => 'required|date',
                'inputnewsubprojectcfsmagayawhr' => 'nullable',
                'inputnewsubprojectcfslfd' => 'required|date',
                'inputnewsubprojectcfscustomsreleasecomment' => 'required',
                'inputnewsubprojectcfsoutdatecr' => 'nullable',
                'inputnewsubprojectcfsmagayacr' => 'nullable',
                'inputnewsubprojectcfsdalfd' => 'required|numeric|min:0',
                'inputnewsubprojectcfscuft' => 'nullable',
                'inputnewsubprojectcfswhstoragecharges' => 'required',
                'inputnewsubprojectcfsdeliverycharges' => 'nullable',
                'inputnewsubprojectcfscharges' => 'required',
                'inputnewsubprojectcfsnotes' => 'nullable',
            ],[
                'inputnewsubprojectproyectid.required' => 'Project ID is required.',
                'inputnewsubprojectcfsmbl.required' => 'MBL is required.',
                'inputnewsubprojectcfssubprojectid.required' => 'Subproject ID is required.',
                'inputnewsubprojectcfshbl.unique' => 'HBL already exists.',
                'inputnewsubprojectcfshbl.required' => 'HBL is required.',
                'inputnewsubprojectcfspieces.required' => 'Pieces is required.',
                'inputnewsubprojectcfspieces.numeric' => 'Pieces must be a number.',
                'inputnewsubprojectcfspieces.min' => 'Pieces must be at least 0.',
                'inputnewsubprojectcfsworkspalletized.required' => 'Works/Palletized is required.',
                'inputnewsubprojectcfspalletsexchanged.required' => 'Pallets Exchanged is required.',
                'inputnewsubprojectcfspallets.required' => 'Pallets is required.',
                'inputnewsubprojectcfspallets.numeric' => 'Pallets must be a number.',
                'inputnewsubprojectcfspallets.min' => 'Pallets must be at least 0.',
                'inputnewsubprojectcfspalletizedcharges.required' => 'Palletized Charge is required.',
                'inputnewsubprojectcfscustomer.required' => 'Customer is required.',
                'inputnewsubprojectcfscfscomment.required' => 'CFS is required.',
                'inputnewsubprojectcfsarrivaldate.required' => 'Arrival Date is required.',
                'inputnewsubprojectcfsarrivaldate.date' => 'Arrival must be a date.',
                'inputnewsubprojectcfslfd.required' => 'LFD is required.',
                'inputnewsubprojectcfslfd.date' => 'LFD must be a date.',
                'inputnewsubprojectcfscustomsreleasecomment.required' => 'Custom release is required.',
                'inputnewsubprojectcfsdalfd.required' => 'Days after LFD is required.',
                'inputnewsubprojectcfsdalfd.numeric' => 'Days after LFD must be a number.',
                'inputnewsubprojectcfsdalfd.min' => 'Days after LFD must be at least 0.',
                'inputnewsubprojectcfswhstoragecharges.required' => 'WH Storahe Charge is required.',
                'inputnewsubprojectcfscharges.required' => 'Charges is required.',

                'hbl_references.array' => 'HBL references must be an array.',
                'hbl_references.*.string' => 'Each HBL reference must be a string.',
                'hbl_references.*.required_without' => 'Each HBL reference must not be empty if provided.',

                'part_numbers.array' => 'Part numbers must be an array.',
                'part_numbers.*.string' => 'Each Part Number must be a string.',
                'part_numbers.*.required_without' => 'Each Part Number must not be empty if provided.',
            ]);

            $checkAgent = $request->has('checkAgent') ? 'Yes' : 'No';

            $outdate = $request->inputnewsubprojectcfsoutdatecr ? Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsoutdatecr)->format('Y-m-d H:i:s') : null;
            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfslfd)->format('Y-m-d H:i:s');


            $projectId = $request->input('inputnewsubprojectproyectid');
            $masterId = $request->input('inputnewsubprojectcfsmbl');

            $master = Master::where('mbl', $masterId)
                ->where('fk_project_id', $projectId)
                ->first();

            if ($master) {
                Subproject::create([
                    'hbl' => $request->inputnewsubprojectcfshbl,
                    'fk_mbl' => $request->inputnewsubprojectcfsmbl,
                    'subprojects_id' => $request->inputnewsubprojectcfssubprojectid,
                    'pieces' => $request->inputnewsubprojectcfspieces,
                    'works_palletized' => $request->inputnewsubprojectcfsworkspalletized,
                    'pallets_exchanged' => $request->inputnewsubprojectcfspalletsexchanged,
                    'pallets' => $request->inputnewsubprojectcfspallets,
                    'services_charge' => $request->inputnewsubprojectcfspalletizedcharges,
                    'customer' => $request->inputnewsubprojectcfscustomer,
                    'agent' => $checkAgent,
                    'cfs_comment' => $request->inputnewsubprojectcfscfscomment,
                    'arrival_date' => $arrivaldate,
                    'whr' => $request->inputnewsubprojectcfsmagayawhr,
                    'lfd' => $lfd,
                    'customs_release_comment' => $request->inputnewsubprojectcfscustomsreleasecomment,
                    'out_date_cr' => $outdate,
                    'cr' => $request->inputnewsubprojectcfsmagayacr,
                    'days_after_lfd' => $request->inputnewsubprojectcfsdalfd,
                    'cuft' => $request->inputnewsubprojectcfscuft,
                    'wh_storage_charge' => $request->inputnewsubprojectcfswhstoragecharges,
                    'delivery_charges' => $request->inputnewsubprojectcfsdeliverycharges,
                    'charges' => $request->inputnewsubprojectcfscharges,
                    'notes' => $request->inputnewsubprojectcfsnotes,
                    'created_by'=> Auth::check() ? Auth::user()->username : 'system',
                    'created_date' => now(),
                    'status' => '1',
                ]);

                // Obtener el subproyecto recién creado
                $subproject = Subproject::where('hbl', $request->inputnewsubprojectcfshbl)->first();

                // Guardar HBL References si vienen en el request
                if ($request->filled('hbl_references')) {
                    foreach ($request->hbl_references as $ref) {
                        if (!empty($ref)) {
                            HblReferences::create([
                                'fk_hbl' => $subproject->hbl,
                                'description' => $ref,
                                'status' => '1',
                                'created_by' => Auth::user()->username ?? 'system',
                                'created_date' => now(),
                            ]);
                        }
                    }
                }

                // Guardar Part Numbers si vienen en el request
                if ($request->filled('part_numbers')) {
                    foreach ($request->part_numbers as $pn) {
                        if (!empty($pn)) {
                            Partnumber::create([
                                'fk_hbl' => $subproject->hbl,
                                'fk_pn' => $pn,
                                'status' => '1',
                                'created_by' => Auth::user()->username ?? 'system',
                                'created_date' => now(),
                            ]);
                        }
                    }
                }

                // Obtener los subprojects relacionados al mbl
                $subprojects = Subproject::where('fk_mbl', $request->inputnewsubprojectcfsmbl)
                ->where('status', '1')
                ->with([
                    'costumer' => function ($q) {
                        $q->where('status', '1');
                    },
                    'pns' => function ($q) {
                        $q->where('cfs_pn.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'services' => function ($q) {
                        $q->where('cfs_services.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'hblreferences' => function ($q) { // <- añade esta parte
                        $q->where('cfs_hbl_references.status', '1');
                    },
                    'cfscommentRelation',
                    'customreleaseRelation',
                ])
                ->get();

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->inputnewsubprojectproyectid)
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

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Subprojects successfully added.',
                    'subprojects' => $subprojects,
                    'masters' => $masters,
                    'projects' => $projects,
                ]);
            }
            else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Mastar or project not found.']);
            }

        }
        return redirect('/login');
    }

    //Funcion para borrar el subproject
    public function deleteSubproject(Request $request){
        if (Auth::check()) {
            // Validar que el mbl y el hbl estén presentes
            $request->validate([
                'mbl' => 'required|string',
                'hbl' => 'required|string',
                'project' => 'required|string',
            ]);

            $subproject = Subproject::where('hbl', $request->hbl)
                ->where('fk_mbl', $request->mbl)
                ->first();

            if ($subproject) {
                // Actualizar el estado del master a 0
                $subproject->status = 0;
                $subproject->updated_by = Auth::check() ? Auth::user()->username : 'system';
                $subproject->transaction_date = now();
                $subproject->save();

                // Obtener los subprojects relacionados al mbl
                $subprojects = Subproject::where('fk_mbl', $request->mbl)
                ->where('status', '1')
                ->with([
                    'costumer' => function ($q) {
                        $q->where('status', '1');
                    },
                    'pns' => function ($q) {
                        $q->where('cfs_pn.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'services' => function ($q) {
                        $q->where('cfs_services.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'hblreferences' => function ($q) { // <- añade esta parte
                        $q->where('cfs_hbl_references.status', '1');
                    },
                    'cfscommentRelation',
                    'customreleaseRelation',
                ])
                ->get();

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->project)
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

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Subproject successfully deleted.',
                    'subprojects' => $subprojects,
                    'masters' => $masters,
                    'projects' => $projects,
                ]);
            } else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Subproject not found.']);
            }
        }
        return redirect('/login');
    }

    //Funcion añadir nuevo subproject
    public function editNewSubproject(Request $request){
        if(Auth::check()) {
            $validated = $request->validate([
                'inputnewsubprojectproyectid' => 'required',
                'inputnewsubprojectcfsmbl' => 'required',
                'inputnewsubprojectcfssubprojectid' => 'required',
                'inputnewsubprojectcfshbl' => 'required',
                // Validación de los nuevos arreglos (hbl_references y part_numbers)
                'hbl_references' => 'nullable|array',
                'hbl_references.*' => 'nullable|string|required_without:hbl_references', // Si hay algún valor, no debe ser vacío

                'part_numbers' => 'nullable|array',
                'part_numbers.*' => 'nullable|string|required_without:part_numbers', // Si hay algún valor, no debe ser vacío

                'inputnewsubprojectcfspieces' => 'required|numeric|min:0',
                'inputnewsubprojectcfsworkspalletized' => 'required',
                'inputnewsubprojectcfspalletsexchanged' => 'required',
                'inputnewsubprojectcfspallets' => 'required|numeric|min:0',
                'inputnewsubprojectcfspalletizedcharges' => 'required',
                'inputnewsubprojectcfscustomer' => 'required',
                'checkAgent' => 'nullable',

                'inputnewsubprojectcfscfscomment' => 'required',
                'inputnewsubprojectcfsarrivaldate' => 'required|date',
                'inputnewsubprojectcfsmagayawhr' => 'nullable',
                'inputnewsubprojectcfslfd' => 'required|date',
                'inputnewsubprojectcfscustomsreleasecomment' => 'required',
                'inputnewsubprojectcfsoutdatecr' => 'nullable',
                'inputnewsubprojectcfsmagayacr' => 'nullable',
                'inputnewsubprojectcfsdalfd' => 'required|numeric|min:0',
                'inputnewsubprojectcfscuft' => 'nullable',
                'inputnewsubprojectcfswhstoragecharges' => 'required',
                'inputnewsubprojectcfsdeliverycharges' => 'nullable',
                'inputnewsubprojectcfscharges' => 'required',
                'inputnewsubprojectcfsnotes' => 'nullable',
            ],[
                'inputnewsubprojectproyectid.required' => 'Project ID is required.',
                'inputnewsubprojectcfsmbl.required' => 'MBL is required.',
                'inputnewsubprojectcfssubprojectid.required' => 'Subproject ID is required.',
                'inputnewsubprojectcfshbl.unique' => 'HBL already exists.',
                'inputnewsubprojectcfshbl.required' => 'HBL is required.',
                'inputnewsubprojectcfspieces.required' => 'Pieces is required.',
                'inputnewsubprojectcfspieces.numeric' => 'Pieces must be a number.',
                'inputnewsubprojectcfspieces.min' => 'Pieces must be at least 0.',
                'inputnewsubprojectcfsworkspalletized.required' => 'Works/Palletized is required.',
                'inputnewsubprojectcfspalletsexchanged.required' => 'Pallets Exchanged is required.',
                'inputnewsubprojectcfspallets.required' => 'Pallets is required.',
                'inputnewsubprojectcfspallets.numeric' => 'Pallets must be a number.',
                'inputnewsubprojectcfspallets.min' => 'Pallets must be at least 0.',
                'inputnewsubprojectcfspalletizedcharges.required' => 'Palletized Charge is required.',
                'inputnewsubprojectcfscustomer.required' => 'Customer is required.',
                'inputnewsubprojectcfscfscomment.required' => 'CFS is required.',
                'inputnewsubprojectcfsarrivaldate.required' => 'Arrival Date is required.',
                'inputnewsubprojectcfsarrivaldate.date' => 'Arrival must be a date.',
                'inputnewsubprojectcfslfd.required' => 'LFD is required.',
                'inputnewsubprojectcfslfd.date' => 'LFD must be a date.',
                'inputnewsubprojectcfscustomsreleasecomment.required' => 'Custom release is required.',
                'inputnewsubprojectcfsdalfd.required' => 'Days after LFD is required.',
                'inputnewsubprojectcfsdalfd.numeric' => 'Days after LFD must be a number.',
                'inputnewsubprojectcfsdalfd.min' => 'Days after LFD must be at least 0.',
                'inputnewsubprojectcfswhstoragecharges.required' => 'WH Storahe Charge is required.',
                'inputnewsubprojectcfscharges.required' => 'Charges is required.',

                'hbl_references.array' => 'HBL references must be an array.',
                'hbl_references.*.string' => 'Each HBL reference must be a string.',
                'hbl_references.*.required_without' => 'Each HBL reference must not be empty if provided.',

                'part_numbers.array' => 'Part numbers must be an array.',
                'part_numbers.*.string' => 'Each Part Number must be a string.',
                'part_numbers.*.required_without' => 'Each Part Number must not be empty if provided.',
            ]);

            $checkAgent = $request->has('checkAgent') ? 'Yes' : 'No';

            $outdate = $request->inputnewsubprojectcfsoutdatecr ? Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsoutdatecr)->format('Y-m-d H:i:s') : null;
            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfslfd)->format('Y-m-d H:i:s');


            $projectId = $request->input('inputnewsubprojectproyectid');
            $masterId = $request->input('inputnewsubprojectcfsmbl');

            $master = Master::where('mbl', $masterId)
                ->where('fk_project_id', $projectId)
                ->first();

            if ($master) {
                // Actualizar el subproject

                // Obtener el proyecto por el ID
                $subproject = Subproject::find($request->inputnewsubprojectcfshbl);

                if($subproject){
                    //$subproject->hbl = $request->inputnewsubprojectcfshbl;
                    //$subproject->fk_mbl = $request->inputnewsubprojectcfsmbl;
                    $subproject->subprojects_id = $request->inputnewsubprojectcfssubprojectid;
                    $subproject->pieces = $request->inputnewsubprojectcfspieces;
                    $subproject->works_palletized = $request->inputnewsubprojectcfsworkspalletized;
                    $subproject->pallets_exchanged = $request->inputnewsubprojectcfspalletsexchanged;
                    $subproject->pallets = $request->inputnewsubprojectcfspallets;
                    $subproject->services_charge = $request->inputnewsubprojectcfspalletizedcharges;
                    $subproject->customer = $request->inputnewsubprojectcfscustomer;
                    $subproject->agent = $checkAgent;
                    $subproject->cfs_comment = $request->inputnewsubprojectcfscfscomment;
                    $subproject->arrival_date = $arrivaldate;
                    $subproject->whr = $request->inputnewsubprojectcfsmagayawhr;
                    $subproject->lfd = $lfd;
                    $subproject->customs_release_comment = $request->inputnewsubprojectcfscustomsreleasecomment;
                    $subproject->out_date_cr = $outdate;
                    $subproject->cr = $request->inputnewsubprojectcfsmagayacr;
                    $subproject->days_after_lfd = $request->inputnewsubprojectcfsdalfd;
                    $subproject->cuft = $request->inputnewsubprojectcfscuft;
                    $subproject->wh_storage_charge = $request->inputnewsubprojectcfswhstoragecharges;
                    $subproject->delivery_charges = $request->inputnewsubprojectcfsdeliverycharges;
                    $subproject->charges = $request->inputnewsubprojectcfscharges;
                    $subproject->notes = $request->inputnewsubprojectcfsnotes;
                    $subproject->updated_by = Auth::check() ? Auth::user()->username : 'system';
                    $subproject->transaction_date = now();
                    $subproject->save();
                }
                // Obtener el subproyecto recién creado
                $subprojectId = $subproject->hbl;

                if ($request->has('hbl_references') && is_array($request->hbl_references)) {
                    $references = array_filter($request->hbl_references, fn($ref) => !empty($ref));
                    $existingDescriptions = [];
                
                    foreach ($references as $ref) {
                        $exists = HblReferences::where('fk_hbl', $subprojectId)
                            ->where('description', $ref)
                            ->exists();
                
                        if (!$exists) {
                            HblReferences::create([
                                'fk_hbl' => $subprojectId,
                                'description' => $ref,
                                'status' => '1',
                                'created_by' => Auth::user()->username ?? 'system',
                                'created_date' => now(),
                            ]);
                        }
                
                        $existingDescriptions[] = $ref;
                    }
                
                    // Eliminar referencias que ya no están en el array enviado
                    HblReferences::where('fk_hbl', $subprojectId)
                        ->whereNotIn('description', $existingDescriptions)
                        ->delete();
                } else {
                    HblReferences::where('fk_hbl', $subprojectId)->delete();
                }

                if ($request->has('hbl_references') && is_array($request->hbl_references)) {
                    $references = array_filter($request->hbl_references, fn($ref) => !empty($ref));
                    $existingDescriptions = [];
                
                    foreach ($references as $ref) {
                        $exists = HblReferences::where('fk_hbl', $subprojectId)
                            ->where('description', $ref)
                            ->exists();
                
                        if (!$exists) {
                            HblReferences::create([
                                'fk_hbl' => $subprojectId,
                                'description' => $ref,
                                'status' => '1',
                                'created_by' => Auth::user()->username ?? 'system',
                                'created_date' => now(),
                            ]);
                        }
                
                        $existingDescriptions[] = $ref;
                    }
                
                    // Eliminar referencias que ya no están en el array enviado
                    HblReferences::where('fk_hbl', $subprojectId)
                        ->whereNotIn('description', $existingDescriptions)
                        ->delete();
                } else {
                    HblReferences::where('fk_hbl', $subprojectId)->delete();
                }

                if ($request->has('part_numbers') && is_array($request->part_numbers)) {
                    $partNumbers = array_filter($request->part_numbers, fn($pn) => !empty($pn));
                    $existingPartNumbers = [];
                
                    foreach ($partNumbers as $pn) {
                        $exists = Partnumber::where('fk_hbl', $subprojectId)
                            ->where('fk_pn', $pn)
                            ->exists();
                
                        if (!$exists) {
                            Partnumber::create([
                                'fk_hbl' => $subprojectId,
                                'fk_pn' => $pn,
                                'status' => '1',
                                'created_by' => Auth::user()->username ?? 'system',
                                'created_date' => now(),
                            ]);
                        }
                
                        $existingPartNumbers[] = $pn;
                    }
                
                    // Eliminar PNs que ya no están en el array enviado
                    Partnumber::where('fk_hbl', $subprojectId)
                        ->whereNotIn('fk_pn', $existingPartNumbers)
                        ->delete();
                } else {
                    Partnumber::where('fk_hbl', $subprojectId)->delete();
                }

                // Obtener los subprojects relacionados al mbl
                $subprojects = Subproject::where('fk_mbl', $request->inputnewsubprojectcfsmbl)
                ->where('status', '1')
                ->with([
                    'costumer' => function ($q) {
                        $q->where('status', '1');
                    },
                    'pns' => function ($q) {
                        $q->where('cfs_pn.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'services' => function ($q) {
                        $q->where('cfs_services.status', '1'); // Filtrar partnumbers con status 1
                    },
                    'hblreferences' => function ($q) { // <- añade esta parte
                        $q->where('cfs_hbl_references.status', '1');
                    },
                    'cfscommentRelation',
                    'customreleaseRelation',
                ])
                ->get();

                // Obtener los masters con relaciones anidadas actualizadas
                $masters = Master::where('fk_project_id', $request->inputnewsubprojectproyectid)
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

                // Responder con éxito y los proyectos actualizados
                return response()->json([
                    'success' => true,
                    'message' => 'Subprojects successfully added.',
                    'subprojects' => $subprojects,
                    'masters' => $masters,
                    'projects' => $projects,
                ]);
            }
            else {
                // Si no se encuentra el master, responder con error
                return response()->json(['success' => false, 'message' => 'Master not found.']);
            }

        }
        return redirect('/login');
    }
}
