<?php

namespace App\Models;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = ['reference', 'amount', 'expense_category_id', 'warehouse_id', 'date', 'details'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
