<?php

namespace App\Domain\Onboarding\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;
use App\Models\Branch;

class OnboardingStep extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'onboarding_steps';

    protected $fillable = [
        'branch_id',
        'step',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'step' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
