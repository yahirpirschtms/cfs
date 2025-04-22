<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subproject extends Model
{
    protected $table = 'cfs_subprojects';
    protected $primaryKey = 'hbl';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'hbl',
        'fk_mbl',
        'subprojects_id',
        'pieces',
        'pallets',
        'works_palletized',
        'pallets_exchanged',
        'customer',
        'cfs_checkbox',
        'cfs_comment',
        'arrival_date',
        'whr',
        'lfd',
        'customs_release_checkbox',
        'customs_release_comment',
        'out_date_cr',
        'cr',
        'charges',
        'days_after_lfd',
        'cuft',
        'notes',
        'status',
        'created_by',
        'created_date',
        'updated_by',
        'transaction_date',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'lfd' => 'datetime',
        'out_date_cr' => 'datetime',
        'created_date' => 'datetime',
        'transaction_date' => 'datetime',
        'charges' => 'decimal:2',
        'cuft' => 'decimal:2',
    ];

    //Llamar a la funcion para actualizar las cantidades de pallets y pieces
    protected static function booted()
    {
        static::saved(function ($subproject) {
            if ($subproject->fk_mbl) {
                $master = $subproject->master;
                if ($master) {
                    $master->recalculateTotals();
                }
            }
        });
    }

    // Formateo de fechas tipo datetime
    public function getArrivalDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getLfdAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getOutDateCrAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getTransactionDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    // Relaciones
    public function master()
    {
        return $this->belongsTo(Master::class, 'fk_mbl', 'mbl');
    }

    public function costumer()
    {
        return $this->belongsTo(Costumer::class, 'customer', 'pk_customer');
    }

    // Relación con tabla pivote (cfs_h_pn)
    public function partnumberLinks()
    {
        return $this->hasMany(Partnumber::class, 'fk_hbl', 'hbl');
    }

    // Relación many-to-many con números de parte reales (cfs_pn)
    public function pns()
    {
        return $this->belongsToMany(Pn::class, 'cfs_h_pn', 'fk_hbl', 'fk_pn', 'hbl', 'pk_part_number');
    }
}
