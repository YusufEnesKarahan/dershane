<?php

namespace App\Domain\Onboarding\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;
use App\Models\Branch;

class OnboardingChecklist extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'onboarding_checklists';

    protected $fillable = [
        'branch_id',
        'key',
        'completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
