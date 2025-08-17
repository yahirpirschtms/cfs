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
use Illuminate\Support\Facades\DB;
use App\Repositories\CFSboardRepository;

class CFSboardController extends Controller
{
    protected $repo;

    public function __construct(CFSboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function cfsboard()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        try {
            $projects = $this->repo->getProjectsWithMastersAndSubprojects();

            return view('home.cfsboard', ['projects' => $projects]);

        } catch (\Exception $e) {
            \Log::error('Error cargando CFS Board: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cargar los proyectos.');
        }
    }

}
