<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSubscriptionItem extends Model
{
    use HasFactory;

    protected $table = 'hq_subscription_items';

    protected $fillable = [
        'subscription_id',
        'extension_id',
        'quantity',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'quantity' => 'integer',
    ];

    public function subscription()
    {
        return $this->belongsTo(HQSubscription::class, 'subscription_id');
    }

    public function extension()
    {
        return $this->belongsTo(HQExtension::class, 'extension_id');
    }
}
