<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use App\Models\Subproject;
use App\Models\Service;
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
use App\Repositories\CFSboardRepository;

class MasterController extends Controller
{
    protected $repo;

    public function __construct(CFSboardRepository $repo)
    {
        $this->repo = $repo;
    }

    //Funcion añadir nuevos masters mas rapida
    public function saveNewMaster(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Validación de datos
        $validated = $request->validate([
            'inputnewmastercfsmbl' => 'required',
            'inputnewmastercfsetaport' => 'required|date',
            'inputnewmastercfsarrivaldate' => 'required|date',
            'inputnewmastercfslfd' => 'required|date',
            'inputnewmastercfscontainernumber' => 'required',
            'inputnewmastercfsnotes' => 'nullable',
            'inputnewmastercfsproyectid' => 'required|exists:cfs_project,project_id',
        ], [
            'inputnewmastercfsmbl.unique' => 'MBL already exists.',
            'inputnewmastercfsmbl.required' => 'MBL is required.',
            'inputnewmastercfsetaport.required' => 'ETA Port is required.',
            'inputnewmastercfsetaport.date' => 'ETA Port must be a date.',
            'inputnewmastercfsarrivaldate.required' => 'Arrival Date is required.',
            'inputnewmastercfsarrivaldate.date' => 'Arrival Date must be a date.',
            'inputnewmastercfslfd.required' => 'LFD is required.',
            'inputnewmastercfslfd.date' => 'LFD must be a date.',
            'inputnewmastercfscontainernumber.required' => 'Container Number is required.',
            'inputnewmastercfsproyectid.required' => 'Project ID is required.',
            'inputnewmastercfsproyectid.exists' => 'Project not found.',
        ]);

        // Convertir fechas
        $etaport     = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsetaport)->format('Y-m-d H:i:s');
        $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsarrivaldate)->format('Y-m-d H:i:s');
        $lfd         = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfslfd)->format('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            // Insertar nuevo master
            DB::table('cfs_master')->insert([
                'mbl'             => $request->inputnewmastercfsmbl,
                'fk_project_id'   => $request->inputnewmastercfsproyectid,
                'container_number'=> $request->inputnewmastercfscontainernumber,
                'notes'           => $request->inputnewmastercfsnotes,
                'eta_port'        => $etaport,
                'arrival_date'    => $arrivaldate,
                'lfd'             => $lfd,
                'created_by'      => Auth::user()->username ?? 'system',
                'created_date'    => now(),
                'status'          => '1',
            ]);

            // Re-obtener todos los proyectos con masters y subprojects
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            DB::commit();

            return response()->json([
                'message'  => 'Master successfully added.',
                'projects' => $projects,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'MBL already exists.'
                ], 422);
            }

            \Log::error('Error saving master: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error adding master: ' . $e->getMessage()
            ], 500);
        }
    }

    //Funcion editar masters mas rapida
    public function editNewMaster(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $originalId = $request->input('inputnewmastercfsmbloriginal');

        // Validación
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
            'inputnewmastercfsproyectid' => 'required|exists:cfs_project,project_id',
        ], [
            'inputnewmastercfsmbl.required' => 'MBL is required.',
            'inputnewmastercfsmbl.unique' => 'MBL already exists.',
            'inputnewmastercfsetaport.required' => 'ETA Port is required.',
            'inputnewmastercfsetaport.date' => 'ETA Port must be a date.',
            'inputnewmastercfsarrivaldate.required' => 'Arrival Date is required.',
            'inputnewmastercfsarrivaldate.date' => 'Arrival Date must be a date.',
            'inputnewmastercfslfd.required' => 'LFD is required.',
            'inputnewmastercfslfd.date' => 'LFD must be a date.',
            'inputnewmastercfscontainernumber.required' => 'Container Number is required.',
            'inputnewmastercfsproyectid.required' => 'Project ID is required.',
            'inputnewmastercfsproyectid.exists' => 'Project ID not found.',
        ]);

        // Conversión de fechas
        $etaport     = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsetaport)->format('Y-m-d H:i:s');
        $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfsarrivaldate)->format('Y-m-d H:i:s');
        $lfd         = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewmastercfslfd)->format('Y-m-d H:i:s');
        $newMasterMBL = $request->inputnewmastercfsmbl;
        $username    = Auth::user()->username ?? 'system';

        DB::beginTransaction();
        try {
            // Obtener datos actuales del master
            $master = DB::table('cfs_master')
                ->select('mbl', 'arrival_date', 'lfd')
                ->where('mbl', $originalId)
                ->first();

            if (!$master) {
                throw new \Exception('Master not found.');
            }

            $oldMasterMBL     = $master->mbl;
            $oldMasterArrival = $master->arrival_date;
            $oldMasterLFD     = $master->lfd;

            // Actualizar master
            $affected = DB::table('cfs_master')
                ->where('mbl', $originalId)
                ->update([
                    'mbl'             => $newMasterMBL,
                    'fk_project_id'   => $request->inputnewmastercfsproyectid,
                    'notes'           => $request->inputnewmastercfsnotes,
                    'container_number'=> $request->inputnewmastercfscontainernumber,
                    'eta_port'        => $etaport,
                    'arrival_date'    => $arrivaldate,
                    'lfd'             => $lfd,
                    'updated_by'      => $username,
                    'transaction_date'=> now(),
                ]);

            if ($affected === 0) {
                throw new \Exception('Master not found or no changes applied.');
            }

            // Si cambia el MBL, actualizar subprojects relacionados
            if ($oldMasterMBL !== $newMasterMBL) {
                DB::table('cfs_subprojects')
                    ->where('fk_mbl', $oldMasterMBL)
                    ->where('status', 1)
                    ->update(['fk_mbl' => $newMasterMBL]);
            }

            // Si cambia ArrivalDate o LFD, actualizar todos los subprojects relacionados
            if (
                !Carbon::parse($oldMasterArrival)->equalTo(Carbon::parse($arrivaldate)) ||
                !Carbon::parse($oldMasterLFD)->equalTo(Carbon::parse($lfd))
            ) {
                $subprojects = Subproject::where('fk_mbl', $newMasterMBL)
                ->where('status', 1)
                ->get();

                foreach ($subprojects as $sub) {
                    $sub->arrival_date = $arrivaldate;
                    $sub->lfd = $lfd;
                    $sub->save();

                    // Ahora sí puedes llamar al método del modelo
                    $sub->recalculateStorageAndCharges();
                }
            }

            // Recargar proyectos con masters y subprojects
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Master successfully updated.',
                'projects' => $projects,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json(['message' => 'MBL already exists.'], 422);
            }

            Log::error('Error updating master: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error updating master: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    //Funcion para borrar el master mas rapida
    public function deleteMaster(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'mbl' => 'required|string',
            'project_id' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $username = Auth::user()->username ?? 'system';
            $now = now();

            // Desactivar master
            $affected = DB::table('cfs_master')
                ->where('mbl', $request->mbl)
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            if ($affected === 0) {
                throw new \Exception('Master not found or already deleted.');
            }

            // Obtener todos los HBL de los subprojects relacionados
            $subprojectsHBL = DB::table('cfs_subprojects')
                ->where('fk_mbl', $request->mbl)
                ->pluck('hbl');

            if ($subprojectsHBL->isNotEmpty()) {
                // Desactivar subprojects
                DB::table('cfs_subprojects')
                    ->whereIn('hbl', $subprojectsHBL)
                    ->update([
                        'status' => 0,
                        'updated_by' => $username,
                        'transaction_date' => $now,
                    ]);

                // Eliminar HBL references
                DB::table('cfs_hbl_references')
                    ->whereIn('fk_hbl', $subprojectsHBL)
                    ->delete();

                // Desactivar part numbers
                DB::table('cfs_h_pn')
                    ->whereIn('fk_hbl', $subprojectsHBL)
                    ->update([
                        'status' => 0,
                        'updated_by' => $username,
                        'transaction_date' => $now,
                    ]);
            }

            DB::commit();

            // Cargar proyectos actualizados
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            return response()->json([
                'success' => true,
                'message' => 'Master deleted successfully.',
                'projects' => $projects,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error deleting master: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting master: '.$e->getMessage(),
            ], 500);
        }
    }

}
