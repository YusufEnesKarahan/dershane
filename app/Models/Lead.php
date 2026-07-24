<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'crm_leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'whatsapp',
        'email',
        'school',
        'grade',
        'city',
        'district',
        'address',
        'guardian_info',
        'program',
        'reference',
        'lead_source_id',
        'lead_status_id',
        'branch_id',
        'advisor_id',
    ];

    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function notes()
    {
        return $this->hasMany(LeadNote::class)->orderBy('created_at', 'desc');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class)->orderBy('created_at', 'desc');
    }

    public function followups()
    {
        return $this->hasMany(LeadFollowup::class)->orderBy('followup_date', 'asc');
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class)->orderBy('created_at', 'desc');
    }

    public function tags()
    {
        return $this->belongsToMany(LeadTag::class, 'lead_tag_items', 'lead_id', 'lead_tag_id');
    }
}
