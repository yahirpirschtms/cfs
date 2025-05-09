<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'cfs_services';
    protected $primaryKey = 'pk_service';
    public $timestamps = false;

    protected $fillable = [
        'pk_service', 'description', 'cost', 'status', 'created_by', 'created_date', 'updated_by', 'transaction_date'
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'transaction_date' => 'datetime',
    ];

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getTransactionDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    // Relación con cfs_h_service (relaciones a subprojects)
    public function houseserviceLinks()
    {
        return $this->hasMany(HouseService::class, 'fk_service', 'pk_service');
    }

    // Relación a subproyectos a través de la tabla pivote
    public function subprojects()
    {
        return $this->belongsToMany(Subproject::class, 'cfs_h_service', 'fk_service', 'fk_hbl', 'pk_service', 'hbl');
    }
}
