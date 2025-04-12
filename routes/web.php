<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\CFSboardController;
use App\Http\Controllers\CFSclientController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MasterController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'show']);

Route::post('/login', [LoginController::class, 'login'])->name('login');

//Middleware para usuarios no autentificados
Route::middleware('auth')->group(function () {

    //Ruta para cerrar tu sesión y salir del sistema
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    //Ruta para acceder a modulos de internos
    Route::get('/cfsboard', [CFSboardController::class, 'cfsboard'])->name('cfsboard');

    //Ruta para acceder a modulo de clientes
    Route::get('/cfsclient', [CFSclientController::class, 'cfsclient'])->name('cfsclient');

    //Ruta nutrir los selects2 del CFS Board
    Route::get('/getLoadSelects', [SelectController::class, 'getLoadSelects'])->name('getLoadSelects');

    //Ruta para añadir un nuevo Drayage User
    Route::post('/saveNewDrayageUser', [SelectController::class, 'saveNewDrayageUser'])->name('saveNewDrayageUser');

    //Ruta para añadir un nuevo Drayage File Type
    Route::post('/saveNewDrayageFileType', [SelectController::class, 'saveNewDrayageFileType'])->name('saveNewDrayageFileType');

    //Ruta para añadir un nuevo Project
    Route::post('/saveNewProject', [ProjectController::class, 'saveNewProject'])->name('saveNewProject');

    //Ruta para borrar el project
    Route::post('/deleteProject', [ProjectController::class, 'deleteProject'])->name('deleteProject');

    //Ruta para editar un edit Project
    Route::post('/editNewProject', [ProjectController::class, 'editNewProject'])->name('editNewProject');

    //Ruta para obtener los masters de un Project
    Route::post('/getProjectMasters', [MasterController::class, 'getProjectMasters'])->name('getProjectMasters');
});
