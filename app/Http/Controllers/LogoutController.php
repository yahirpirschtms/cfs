<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout(){
        Session::flush();  // Limpiar sesión
        Auth::logout();    // Cerrar sesión

        // Borrar las cookies de autenticación
        cookie()->queue(cookie()->forget('XSRF-TOKEN')); // Para Laravel 7 y versiones superiores
        cookie()->queue(cookie()->forget('laravel_session'));

        return redirect()->to('/login');
    }
}
