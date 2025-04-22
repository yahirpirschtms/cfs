<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pn extends Model
{
    protected $table = 'cfs_pn';
    protected $primaryKey = 'pk_part_number';
    public $timestamps = false;

    protected $fillable = [
        'pk_part_number', 'description', 'status', 'created_by', 'created_date', 'updated_by', 'transaction_date'
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

    // Relación con cfs_h_pn (relaciones a subprojects)
    public function partnumberLinks()
    {
        return $this->hasMany(Partnumber::class, 'fk_pn', 'pk_part_number');
    }

    // Relación a subproyectos a través de la tabla pivote
    public function subprojects()
    {
        return $this->belongsToMany(Subproject::class, 'cfs_h_pn', 'fk_pn', 'fk_hbl', 'pk_part_number', 'hbl');
    }
}
