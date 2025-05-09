<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class HouseService extends Model
{
    protected $table = 'cfs_h_service';
    protected $primaryKey = 'pk_h_service';
    public $timestamps = false;

    protected $fillable = [
        'fk_hbl', 'fk_service', 'status', 'created_by', 'created_date', 'updated_by', 'transaction_date'
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

    public function subproject()
    {
        return $this->belongsTo(Subproject::class, 'fk_hbl', 'hbl');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'fk_service', 'pk_service');
    }
}
