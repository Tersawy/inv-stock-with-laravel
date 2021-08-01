<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjustment extends Model
{
    use SoftDeletes;

    protected $fillable = ['items_count', 'note', 'date', 'warehouse_id', 'status', 'reference'];

    protected $append = ['reference'];

    public function details()
    {
        return $this->hasMany(AdjustmentDetail::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
