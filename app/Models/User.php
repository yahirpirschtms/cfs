<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = "users_yms";
    public $timestamps = false; // Deshabilitar timestamps
 
    // Definir la clave primaria
    protected $primaryKey = 'pk_users';
 
     // Si la clave primaria no es un entero, indica que es de tipo string
     //public $incrementing = false;
     protected $keyType = 'string';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'pk_users',
        'username',
        'password',
        'roles',
        'permissions',  
    ];

    // Convertir roles y permisos a arrays automáticamente
    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
    ];

    public function getRolesAttribute($value)
    {
        // Suponiendo que los roles están almacenados como una cadena separada por comas
        return explode(',', $value);
    }

    public function getPermissionsAttribute($value)
    {
        // Suponiendo que los roles están almacenados como una cadena separada por comas
        return explode(',', $value);
    }
}
