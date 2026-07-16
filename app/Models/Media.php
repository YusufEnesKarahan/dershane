<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'disk',
        'directory',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'width',
        'height',
        'checksum',
        'alt',
        'caption',
        'title',
        'description',
        'visibility',
        'collection',
        'folder_id',
        'status',
        'uploaded_by',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'visibility' => \App\Enums\MediaVisibility::class,
        'last_used_at' => 'datetime',
    ];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function usages()
    {
        return $this->hasMany(MediaUsage::class);
    }

    public function variants()
    {
        return $this->hasMany(MediaVariant::class);
    }

    public function getUrl(string $conversion = 'original'): string
    {
        $dir = $this->directory ? $this->directory . '/' : '';
        
        if ($conversion !== 'original') {
            $variant = $this->variants()->where('variant_name', $conversion)->first();
            if ($variant) {
                return Storage::disk($variant->disk)->url($dir . $variant->filename);
            }
        }

        return Storage::disk($this->disk)->url($dir . $this->filename);
    }
}
