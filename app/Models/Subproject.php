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
        'agent',
        'cfs_checkbox',
        'cfs_comment',
        'arrival_date',
        'whr',
        'lfd',
        'customs_release_checkbox',
        'customs_release_comment',
        'out_date_cr',
        'cr',
        'services_charge',
        'wh_storage_charge',
        'delivery_charges',
        'charges',
        'collected',
        'payed',
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
        'services_charge' => 'decimal:2',
        'wh_storage_charge' => 'decimal:2',
        'delivery_charges' => 'decimal:2',
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

    //funcion recalcular cantidades si se editan las fechas del master
    public function recalculateStorageAndCharges()
    {
        if (!$this->out_date_cr || !$this->lfd) {
            return;
        }

        // Calcula los días después del LFD; si es negativo, pon 0
        $days = Carbon::parse($this->lfd)->diffInDays(Carbon::parse($this->out_date_cr), false);
        $this->days_after_lfd = $days > 0 ? $days : 0;

        // Buscar el servicio "STORAGE"
        $storageService = Service::where('description', 'STORAGE')
            ->where('status', '1')
            ->first();

        $storageCost = $storageService ? $storageService->cost : 0;

        // Calcular el cargo de almacenamiento
        $this->wh_storage_charge = round($this->days_after_lfd * $this->cuft * $storageCost, 2);

        // Calcular cargos totales
        $this->charges = round(
            $this->wh_storage_charge +
            $this->services_charge +
            $this->delivery_charges,
            2
        );

        $this->save();
    }

    protected $appends = ['arrival_date_full', 'lfd_full', 'out_date_cr_full'];

    // Formateo de fechas tipo datetime
    public function getArrivalDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('m/d/Y') : null;
    }

    public function getArrivalDateFullAttribute()
    {
        return $this->attributes['arrival_date'] 
            ? Carbon::parse($this->attributes['arrival_date'])->format('m/d/Y H:i:s') 
            : null;
    }

    public function getLfdAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('m/d/Y') : null;
    }

    public function getLfdFullAttribute()
    {
        return $this->attributes['lfd'] 
            ? Carbon::parse($this->attributes['lfd'])->format('m/d/Y H:i:s') 
            : null;
    }

    public function getOutDateCrAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('m/d/Y') : null;
    }
    
    public function getOutDateCrFullAttribute()
    {
        return $this->attributes['out_date_cr'] 
            ? Carbon::parse($this->attributes['out_date_cr'])->format('m/d/Y H:i:s')
            : null;
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

    //
    public function hblreferences()
    {
        return $this->hasMany(HblReferences::class, 'fk_hbl', 'hbl');
    }

    // Relación con tabla pivote (cfs_h_pn)
    public function servicesLinks()
    {
        return $this->hasMany(HouseService::class, 'fk_hbl', 'hbl');
    }

    // Relación many-to-many con servicios reales (cfs_services)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'cfs_h_service', 'fk_hbl', 'fk_service', 'hbl', 'pk_service');
    }

    //Relacion para el CFS Comment
    public function cfscommentRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'cfs_comment','gnct_id');
    }

    //Relacion para el Custom Release Comment
    public function customreleaseRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'customs_release_comment','gnct_id');
    }
}
