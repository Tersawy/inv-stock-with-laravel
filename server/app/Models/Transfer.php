<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
  use SoftDeletes, InvoiceAttributes;

  protected $fillable = [
    'reference',
    'from_warehouse_id',
    'to_warehouse_id',
    'tax',
    'discount',
    'discount_method',
    'status',
    'shipping',
    'note',
    'date',
    'total_price',
    'items_count'
  ];

  protected $fieldActionName = 'cost';

  public function details()
  {
    return $this->hasMany(TransferDetail::class);
  }

  public function from_warehouse()
  {
    return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
  }

  public function to_warehouse()
  {
    return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
  }
}
