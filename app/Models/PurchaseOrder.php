<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['supplier_id', 'order_number', 'order_date', 'total_amount', 'status', 'notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
