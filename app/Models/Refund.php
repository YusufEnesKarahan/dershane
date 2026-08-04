<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class Refund extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'payment_id',
        'amount',
        'reason',
        'approved_by',
        'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
