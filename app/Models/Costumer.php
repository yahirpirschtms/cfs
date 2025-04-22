<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Costumer extends Model
{
    protected $table = 'cfs_customer';
    protected $primaryKey = 'pk_customer';
    public $timestamps = false;

    protected $fillable = [
        'name', 'address', 'city', 'state', 'country', 'zipcode', 'status',
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
        return $this->hasMany(Subproject::class, 'customer', 'pk_customer');
    }
}
