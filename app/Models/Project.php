<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    protected $table = 'cfs_project';
    protected $primaryKey = 'project_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'project_id', 'invoice', 'month', 'drayage_user', 'drayage_typefile', 'status',
        'created_by', 'created_date', 'updated_by', 'transaction_date'
    ];

    protected $casts = [
        'month' => 'date',
        'created_date' => 'datetime',
        'transaction_date' => 'datetime',
    ];

    protected $appends = ['month_full'];

    public function getMonthAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('M/j') : null;
    }

    public function getMonthFullAttribute()
    {
        return $this->attributes['month'] 
            ? Carbon::parse($this->attributes['month'])->format('m/d/Y')
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

    public function masters()
    {
        return $this->hasMany(Master::class, 'fk_project_id', 'project_id')->where('status', '1');
    }
    public function drayageUserRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'drayage_user','gnct_id');
    }
    public function drayageFileRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'drayage_typefile','gnct_id');
    }
    public function invoiceRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'invoice','gnct_id');
    }
}
