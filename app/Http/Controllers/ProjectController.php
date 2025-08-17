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
use App\Repositories\CFSboardRepository;

class ProjectController extends Controller
{
    protected $repo;

    public function __construct(CFSboardRepository $repo)
    {
        $this->repo = $repo;
    }

    //Funcion para guardar un nuevo project mas rapido
    public function saveNewProject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Validación de datos
        $validated = $request->validate([
            'inputnewcfsprojectprojectid' => 'required',
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

        // Iniciar transacción
        DB::beginTransaction();
        try {
            // Insertar nuevo proyecto
            DB::table('cfs_project')->insert([
                'project_id' => $request->inputnewcfsprojectprojectid,
                'month' => $month,
                'invoice' => $request->inputnewcfsprojectinvoice,
                'drayage_user' => $request->inputnewcfspeojectdrayageperson,
                'drayage_typefile' => $request->inputnewcfsprojectdrayagefiletype,
                'created_by' => Auth::user()->username ?? 'system',
                'created_date' => now(),
                'status' => '1',
            ]);

            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            DB::commit();

            // Responder con éxito
            return response()->json([
                'message' => 'Project successfully added.',
                'projects' => $projects,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Detectar error de clave duplicada MySQL (SQLSTATE 23000 y error code 1062)
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'Project ID already exists.'
                ], 422);
            }

            Log::error('Error saving project: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error adding project: ' . $e->getMessage()
            ], 500);
        }
    }

    //Delete con prodimientos almacenados
    /*public function deleteProject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'project_id' => 'required|string',
        ]);

        try {
            $username = Auth::user()->username ?? 'system';

            // 👇 Llamar directamente al procedimiento
            DB::statement('CALL delete_project(?, ?)', [
                $request->project_id,
                $username
            ]);

            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.',
                'projects' => $projects,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }*/

    public function deleteProject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'project_id' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $username = Auth::user()->username ?? 'system';
            $now = now();

            // Desactivar proyecto
            $affected = DB::table('cfs_project')
                ->where('project_id', $request->project_id)
                ->where('status', '1')
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            if ($affected === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found or already deleted.'
                ], 404);
            }

            // Desactivar masters relacionados
            DB::table('cfs_master')
                ->where('fk_project_id', $request->project_id)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            // Desactivar subprojects relacionados
            $subprojects = DB::table('cfs_subprojects')
                ->whereIn('fk_mbl', function ($q) use ($request) {
                    $q->select('mbl')
                    ->from('cfs_master')
                    ->where('fk_project_id', $request->project_id);
                })
                ->pluck('hbl');

            DB::table('cfs_subprojects')
                ->whereIn('hbl', $subprojects)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            // Eliminar referencias
            DB::table('cfs_hbl_references')
                ->whereIn('fk_hbl', $subprojects)
                ->delete();

            // Desactivar part numbers
            DB::table('cfs_h_pn')
                ->whereIn('fk_hbl', $subprojects)
                ->update([
                    'status' => 0,
                    'updated_by' => $username,
                    'transaction_date' => $now,
                ]);

            DB::commit();

            // 🚀 Ahora ya no armo todo aquí → llamo al repo
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.',
                'projects' => $projects,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error deleting project: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting project: '.$e->getMessage(),
            ], 500);
        }
    }

    //Funcion para editar un project mas rapida
    public function editNewProject(Request $request){
        if (!Auth::check()) {
            return redirect('/login');
        }
        $originalId = $request->input('inputnewcfsprojectprojectidoriginal');

        // Validación
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

        DB::beginTransaction();
        try {
            // Actualizar proyecto y capturar filas afectadas
            $affected = DB::table('cfs_project')
                ->where('project_id', $originalId)
                ->update([
                    'project_id' => $newProjectId,
                    'drayage_user' => $request->inputnewcfspeojectdrayageperson,
                    'drayage_typefile' => $request->inputnewcfsprojectdrayagefiletype,
                    'invoice' => $request->inputnewcfsprojectinvoice,
                    'month' => $month,
                    'updated_by' => $username,
                    'transaction_date' => now(),
                ]);

            // Si no se actualizó ninguna fila, significa que el proyecto no existe
            if ($affected === 0) {
                throw new \Exception('Project not found.');
            }

            // Actualizar cfs_master si cambia el ID
            if ($originalId !== $newProjectId) {
                DB::table('cfs_master')
                    ->where('fk_project_id', $originalId)
                    ->where('status', 1)
                    ->update(['fk_project_id' => $newProjectId]);
            }

            // Cargar todos los proyectos con sus relaciones
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'projects' => $projects
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Duplicado de Project ID
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json(['message' => 'Project ID already exists.'], 422);
            }

            Log::error('Error updating project: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error updating project: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

}
