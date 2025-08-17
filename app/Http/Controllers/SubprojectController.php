<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
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
use Illuminate\Validation\Rule;
use App\Repositories\CFSboardRepository;

class SubprojectController extends Controller
{
    protected $repo;

    public function __construct(CFSboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getMastersSubprojects(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'mbl' => 'required|string',
            'project_id' => 'required|string',
        ]);

        $projectId = $request->input('project_id');
        $masterId = $request->input('mbl');

        $masterExists = Master::where('mbl', $masterId)->exists();
        if (!$masterExists) {
            return response()->json(['success' => false, 'message' => 'Master not found.']);
        }

        $result = $this->repo->getProjectsWithSubprojects($projectId, $masterId);

        return response()->json([
            'success' => true,
            'message' => 'Subprojects successfully found.',
            'subprojects' => $result['selectedSubprojects'],
            'projects' => $result['projects'],
        ]);
    }

    public function saveNewSubproject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

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
                    'checkCollected' => 'nullable',
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

        DB::beginTransaction();

        try {
            $checkAgent = $request->has('checkAgent') ? 'Yes' : 'No';
            $checkCollected = $request->has('checkCollected') ? 'Yes' : 'No';

            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfslfd)->format('Y-m-d H:i:s');
            $outdate = $request->inputnewsubprojectcfsoutdatecr
                ? Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsoutdatecr)->format('Y-m-d H:i:s')
                : null;

            $projectId = $request->input('inputnewsubprojectproyectid');
            $masterId = $request->input('inputnewsubprojectcfsmbl');

            $master = Master::where('mbl', $masterId)
                ->where('fk_project_id', $projectId)
                ->first();

            if (!$master) {
                return response()->json(['success' => false, 'message' => 'Master or project not found.'], 404);
            }

            // Insertar subproject
            DB::table('cfs_subprojects')->insert([
                'hbl' => $request->inputnewsubprojectcfshbl,
                'fk_mbl' => $masterId,
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
                'collected' => $checkCollected,
                'charges' => $request->inputnewsubprojectcfscharges,
                'notes' => $request->inputnewsubprojectcfsnotes,
                'created_by'=> Auth::user()->username ?? 'system',
                'created_date' => now(),
                'status' => '1',
            ]);

            // Recalcular totales del Master
            $master->recalculateTotals();

            // Guardar HBL References en batch
            if ($request->filled('hbl_references')) {
                $hblRefs = array_map(fn($ref) => [
                    'fk_hbl' => $request->inputnewsubprojectcfshbl,
                    'description' => $ref,
                    'status' => '1',
                    'created_by' => Auth::user()->username ?? 'system',
                    'created_date' => now(),
                ], array_filter($request->hbl_references));

                if (!empty($hblRefs)) {
                    DB::table('cfs_hbl_references')->insert($hblRefs);
                }
            }

            // Guardar Part Numbers en batch
            if ($request->filled('part_numbers')) {
                $pns = array_map(fn($pn) => [
                    'fk_hbl' => $request->inputnewsubprojectcfshbl,
                    'fk_pn' => $pn,
                    'status' => '1',
                    'created_by' => Auth::user()->username ?? 'system',
                    'created_date' => now(),
                ], array_filter($request->part_numbers));

                if (!empty($pns)) {
                    DB::table('cfs_h_pn')->insert($pns);
                }
            }

            $result = $this->repo->getProjectsWithSubprojects($projectId, $masterId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subproject successfully added.',
                'subprojects' => $result['selectedSubprojects'],
                'projects' => $result['projects'],
                'pkproject' => $projectId,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'HBL already exists.'
                ], 422);
            }

            \Log::error('Error saving subproject: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error adding subproject: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSubproject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'mbl' => 'required|string',
            'hbl' => 'required|string',
            'project' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $username = Auth::user()->username ?? 'system';
            $now = now();

            // Desactivar subproject
            $affected = DB::table('cfs_subprojects')
                ->where('hbl', $request->hbl)
                ->where('fk_mbl', $request->mbl)
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            if ($affected === 0) {
                throw new \Exception('Subproject not found or already deleted.');
            }

            // Desactivar Part Numbers asociados
            DB::table('cfs_h_pn')
                ->where('fk_hbl', $request->hbl)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            // Eliminar HBL References asociados
            DB::table('cfs_hbl_references')
                ->where('fk_hbl', $request->hbl)
                ->delete();

            // Recalcular totales del Master si existe
            $master = Master::where('mbl', $request->mbl)->first();
            if ($master) {
                $master->recalculateTotals();
            }

            // Obtener proyectos y subprojects actualizados desde el repo
            $result = $this->repo->getProjectsWithSubprojects($request->project, $request->mbl);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subproject successfully deleted.',
                'projects' => $result['projects'],
                'subprojects' => $result['selectedSubprojects'],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error deleting subproject: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting subproject: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editNewSubproject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $originalHBL = $request->input('inputnewsubprojectcfshbloriginal');

        $validated = $request->validate([
                'inputnewsubprojectproyectid' => 'required',
                'inputnewsubprojectcfsmbl' => 'required',
                'inputnewsubprojectcfssubprojectid' => 'required',
                //'inputnewsubprojectcfshbl' => 'required',
                'inputnewsubprojectcfshbl' => [
                    'required',
                    Rule::unique('cfs_subprojects', 'hbl')->ignore($originalHBL, 'hbl')
                ],
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
                'checkCollected' => 'nullable',
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

        DB::beginTransaction();

        try {
            $checkAgent = $request->has('checkAgent') ? 'Yes' : 'No';
            $checkCollected = $request->has('checkCollected') ? 'Yes' : 'No';
            $username = Auth::user()->username ?? 'system';

            $arrivaldate = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsarrivaldate)->format('Y-m-d H:i:s');
            $lfd = Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfslfd)->format('Y-m-d H:i:s');
            $outdate = $request->inputnewsubprojectcfsoutdatecr
                ? Carbon::createFromFormat('m/d/Y H:i:s', $request->inputnewsubprojectcfsoutdatecr)->format('Y-m-d H:i:s')
                : null;

            $projectId = $request->input('inputnewsubprojectproyectid');
            $masterId = $request->input('inputnewsubprojectcfsmbl');
            $newHBL = $request->inputnewsubprojectcfshbl;

            $master = Master::where('mbl', $masterId)->where('fk_project_id', $projectId)->first();
            if (!$master) {
                throw new \Exception('Master not found.');
            }

            // Actualizar subproject usando DB::update
            DB::table('cfs_subprojects')
                ->where('hbl', $originalHBL)
                ->update([
                    'hbl' => $newHBL,
                    'subprojects_id' => $request->inputnewsubprojectcfssubprojectid,
                    'pieces' => $request->inputnewsubprojectcfspieces,
                    'works_palletized' => $request->inputnewsubprojectcfsworkspalletized,
                    'pallets_exchanged' => $request->inputnewsubprojectcfspalletsexchanged,
                    'pallets' => $request->inputnewsubprojectcfspallets,
                    'services_charge' => $request->inputnewsubprojectcfspalletizedcharges,
                    'customer' => $request->inputnewsubprojectcfscustomer,
                    'agent' => $checkAgent,
                    'collected' => $checkCollected,
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
                    'updated_by' => $username,
                    'transaction_date' => now(),
                ]);

            // Actualizar HBL References en batch
            DB::table('cfs_hbl_references')->where('fk_hbl', $originalHBL)->delete();
            if ($request->filled('hbl_references')) {
                $hblRefs = array_map(fn($ref) => [
                    'fk_hbl' => $newHBL,
                    'description' => $ref,
                    'status' => '1',
                    'created_by' => $username,
                    'created_date' => now(),
                ], array_filter($request->hbl_references));
                if (!empty($hblRefs)) {
                    DB::table('cfs_hbl_references')->insert($hblRefs);
                }
            }

            // Actualizar Part Numbers en batch
            DB::table('cfs_h_pn')->where('fk_hbl', $originalHBL)->delete();
            if ($request->filled('part_numbers')) {
                $pns = array_map(fn($pn) => [
                    'fk_hbl' => $newHBL,
                    'fk_pn' => $pn,
                    'status' => '1',
                    'created_by' => $username,
                    'created_date' => now(),
                ], array_filter($request->part_numbers));
                if (!empty($pns)) {
                    DB::table('cfs_h_pn')->insert($pns);
                }
            }

            // Recalcular totales del Master
            $master->recalculateTotals();

            // Obtener proyectos actualizados
            $result = $this->repo->getProjectsWithSubprojects($projectId, $masterId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subproject successfully updated.',
                'subprojects' => $result['selectedSubprojects'],
                'projects' => $result['projects'],
                'pkproject' => $projectId,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating subproject: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
