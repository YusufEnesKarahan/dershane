<?php

namespace App\Domain\Institution\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;
use App\Models\Branch;

class InstitutionSetting extends Model
{
    use HasFactory, TenantScoped, SoftDeletes;

    protected $table = 'institution_settings';

    protected $fillable = [
        'branch_id',
        'institution_name',
        'logo',
        'favicon',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'website',
        'tax_number',
        'description',
        'primary_color',
        'secondary_color',
        'timezone',
        'language',
        'notification_preferences',
        'invoice_information',
        'academic_year',
    ];

    protected function casts(): array
    {
        return [
            'notification_preferences' => 'array',
            'invoice_information' => 'array',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
