<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    public function index()
    {
        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $purchases = Adjustment::with($warehouse)->get();

        $purchases = $purchases->map(function ($purchase) {
            return [
                'id'            => $purchase->id,
                'reference'     => $purchase->reference,
                'items_count'   => $purchase->items_count,
                'warehouse'     => $purchase->warehouse,
                'date'          => $purchase->date
            ];
        });

        return $this->success($purchases);
    }
}
