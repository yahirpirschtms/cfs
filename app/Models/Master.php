<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Master extends Model
{
    protected $table = 'cfs_master';
    protected $primaryKey = 'mbl';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mbl', 'fk_project_id', 'container_number', 'total_pieces',
        'total_pallets', 'eta_port', 'arrival_date', 'lfd', 'notes', 'status',
        'created_by', 'created_date', 'updated_by', 'transaction_date'
    ];

    protected $casts = [
        'eta_port' => 'datetime',
        'arrival_date' => 'datetime',
        'lfd' => 'datetime',
        'created_date' => 'datetime',
        'transaction_date' => 'datetime',
    ];

    protected $appends = ['eta_port_full', 'arrival_date_full', 'lfd_full'];

    //Funcion calcular pallets y pieces automaticamente
    public function recalculateTotals()
    {
        $totals = $this->subprojects()->where('status', 1)->selectRaw('SUM(pieces) as total_pieces, SUM(pallets) as total_pallets')->first();

        $this->total_pieces = $totals->total_pieces ?? 0;
        $this->total_pallets = $totals->total_pallets ?? 0;
        $this->save();
    }

    public function getEtaPortAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('m/d/Y') : null;
    }

    public function getEtaPortFullAttribute()
    {
        return $this->attributes['eta_port'] 
            ? Carbon::parse($this->attributes['eta_port'])->format('m/d/Y H:i:s')
            : null;
    }

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

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i:s');
    }

    public function getTransactionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('m/d/Y H:i:s') : null;
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'fk_project_id', 'project_id');
    }

    public function subprojects()
    {
        return $this->hasMany(Subproject::class, 'fk_mbl', 'mbl')->where('status', '1');
    }
}
