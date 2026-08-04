<?php

namespace App\Domain\Package\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class BranchPackage extends Model
{
    use HasFactory;

    protected $table = 'branch_packages';

    protected $fillable = [
        'branch_id',
        'package_id',
        'license_type',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->end_date || !$this->end_date->isPast());
    }
}
