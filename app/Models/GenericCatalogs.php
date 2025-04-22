<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericCatalogs extends Model
{
    use HasFactory;

    // El nombre de la tabla en la base de datos
    protected $table = 'generic_catalogs';

    // La clave primaria de la tabla
    protected $primaryKey = 'gnct_id';

    // Indicar si la clave primaria es autoincremental
    public $incrementing = true;

    // Indicar el tipo de la clave primaria (por defecto es 'int', lo dejamos igual)
    protected $keyType = 'int';

    // Si no se están utilizando los campos created_at y updated_at de Laravel
    public $timestamps = false;

    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'gntc_value', 
        'gntc_gntc_id', 
        'gntc_description', 
        'gntc_group', 
        'gntc_status', 
        'gntc_creation_date', 
        'gntc_user', 
        'gntc_update_date', 
        'gntc_update_user', 
        'gntc_label'
    ];

    // Relación recursiva (self-referencing) para el campo gntc_gntc_id
    public function parentCatalog()
    {
        return $this->belongsTo(GenericCatalogs::class, 'gntc_gntc_id', 'gnct_id');
    }

    // Relación para obtener los hijos de un catalogo (si es necesario)
    public function childCatalogs()
    {
        return $this->hasMany(GenericCatalogs::class, 'gntc_gntc_id', 'gnct_id');
    }
    public function drayageUser()
    {
        return $this->hasMany(GenericCatalogs::class, 'drayage_user','gnct_id');
    }
    public function drayageFile()
    {
        return $this->hasMany(GenericCatalogs::class, 'drayage_typefile','gnct_id');
    }
}