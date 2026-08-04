<?php

namespace App\Domain\Onboarding\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;
use App\Models\Branch;

class InstitutionSetting extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'institution_settings';

    protected $fillable = [
        'branch_id',
        'institution_name',
        'logo',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'website',
        'academic_year',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
