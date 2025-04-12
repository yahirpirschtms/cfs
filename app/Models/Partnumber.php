<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Partnumber extends Model
{
    protected $table = 'cfs_h_pn';
    protected $primaryKey = 'pk_part_number';
    public $timestamps = false;

    protected $fillable = [
        'fk_hbl', 'description', 'created_by', 'status',
        'created_date', 'updated_by', 'transaction_date'
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
}
