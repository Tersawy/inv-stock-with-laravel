<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'email',
        'company_name',
        'company_phone',
        'company_address',
        'company_logo',
        'footer',
        'developed_by',
        'currency_id',
        'warehouse_id',
        'customer_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
