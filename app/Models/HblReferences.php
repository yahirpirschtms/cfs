<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HblReferences extends Model
{
    protected $table = 'cfs_hbl_references';
    protected $primaryKey = 'pk_hbl_reference';
    public $timestamps = false;

    protected $fillable = [
        'description', 'fk_hbl', 'status',
        'created_by', 'created_date', 'updated_by', 'transaction_date'
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

    public function subprojects()
    {
        return $this->belongsTo(Subproject::class, 'fk_hbl', 'hbl');
    }
}
