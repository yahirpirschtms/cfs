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

    public function getMonthAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y');
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
        return $this->hasMany(Master::class, 'fk_project_id', 'project_id');
    }
    public function drayageUserRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'drayage_user','gnct_id');
    }
    public function drayageFileRelation()
    {
        return $this->belongsTo(GenericCatalogs::class, 'drayage_typefile','gnct_id');
    }
}
