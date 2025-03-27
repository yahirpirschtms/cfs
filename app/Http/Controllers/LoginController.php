<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function show(){
        if(Auth::check()){
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }
    
    public function login(LoginRequest $request){
        $credentials = $request->getCredentials();

        // Obtener solo las columnas necesarias para optimizar el rendimiento
        $user = User::select('pk_users', 'password', 'roles')
            ->where('username', $credentials['username'])
            ->first();

        if (!$user) {
            return redirect()->to('/login')->withErrors(['auth.failed' => 'Incorrect username']);
        }

        // Verificar si la contraseña está encriptada correctamente con bcrypt
        if (Hash::needsRehash($user->password)) {
            return redirect()->to('/login')->withErrors(['auth.failed' => 'Your password is not secure. Please contact support.']);
        }

        // Validar la contraseña con bcrypt
        if (!Hash::check($credentials['password'], $user->password)) {
            return redirect()->to('/login')->withErrors(['auth.failed' => 'Incorrect password']);
        }

        // Iniciar sesión si la contraseña es válida
        Auth::login($user);

        //return $this->authenticated($request, $user);

        // Redirigir según el rol del usuario
        return $this->redirectBasedOnRole($user);
    }

    public function authenticated(Request $request, $user){
        return redirect('cfsboard');
    }

    // Método para redirigir al usuario según su rol
    private function redirectBasedOnRole($user){
        // Verificar si el usuario tiene roles definidos
        $roles = is_array($user->roles) ? $user->roles : [];
        
        // Si no tiene roles asignados, redirigir al login
        if (empty($roles)) {
            Auth::logout();  // Si no tiene roles, cerramos la sesión
            return redirect('/login');
        }

        // Redirigir al dashboard de admin si tiene el rol de admin
        if (in_array('admin', $roles)) {
            return redirect('cfsboard');
        }

        // Redirigir a la página de cliente si tiene el rol de client
        if (in_array('client', $roles)) {
            return redirect('cfsclient');
        }
        // Si no tiene un rol válido, redirigir al login
        else{
        Auth::logout();  // Asegurarse de cerrar sesión si no tiene un rol válido
        return redirect('/login');
        }
    }
}
