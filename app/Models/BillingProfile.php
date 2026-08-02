<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;

class BillingProfile extends Model
{
    use TenantScoped;

    protected $fillable = [
        'branch_id',
        'company_name',
        'tax_number',
        'tax_office',
        'billing_email',
        'address',
    ];
}
