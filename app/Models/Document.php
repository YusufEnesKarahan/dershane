<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'category_id',
        'title',
        'file_name',
        'file_path',
        'type',
        'file_type',
        'file_size',
        'uploaded_by',
        'status',
        'description',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->orderBy('version_number', 'desc');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(DocumentPermission::class, 'document_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DocumentLog::class, 'document_id')->orderBy('created_at', 'desc');
    }
}
