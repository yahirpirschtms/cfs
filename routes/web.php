<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\CFSboardController;
use App\Http\Controllers\CFSclientController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\SubprojectController;

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

    //Ruta para añadir un nuevo Master
    Route::post('/saveNewMaster', [MasterController::class, 'saveNewMaster'])->name('saveNewMaster');

    //Ruta para añadir un nuevo Master
    Route::post('/editNewMaster', [MasterController::class, 'editNewMaster'])->name('editNewMaster');

    //Ruta para borrar el master
    Route::post('/deleteMaster', [MasterController::class, 'deleteMaster'])->name('deleteMaster');

    //Ruta para obtener los subprojetcs de un Master
    Route::post('/getMastersSubprojects', [SubprojectController::class, 'getMastersSubprojects'])->name('getMastersSubprojects');

    //Ruta para añadir un nuevo PartNumber
    Route::post('/saveNewPartNumber', [SelectController::class, 'saveNewPartNumber'])->name('saveNewPartNumber');

    //Ruta para añadir un nuevo Customer
    Route::post('/saveNewCustomer', [SelectController::class, 'saveNewCustomer'])->name('saveNewCustomer');

    //Ruta para añadir un nuevo CFS
    Route::post('/saveNewCFS', [SelectController::class, 'saveNewCFS'])->name('saveNewCFS');

    //Ruta para añadir un nuevo Customs Release
    Route::post('/saveNewCustomRelease', [SelectController::class, 'saveNewCustomRelease'])->name('saveNewCustomRelease');

    //Ruta para añadir un nuevo Invoice
    Route::post('/saveNewInvoice', [SelectController::class, 'saveNewInvoice'])->name('saveNewInvoice');

    //Ruta para añadir un nuevo Subproject
    Route::post('/saveNewSubproject', [SubprojectController::class, 'saveNewSubproject'])->name('saveNewSubproject');

    //Ruta para eliminar un Subproject
    Route::post('/deleteSubproject', [SubprojectController::class, 'deleteSubproject'])->name('deleteSubproject');

    //Ruta para editar un Subproject
    Route::post('/editNewSubproject', [SubprojectController::class, 'editNewSubproject'])->name('editNewSubproject');
});
