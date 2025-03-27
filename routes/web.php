<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\CFSboardController;
use App\Http\Controllers\CFSclientController;

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
});
