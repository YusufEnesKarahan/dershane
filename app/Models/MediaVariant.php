<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaVariant extends Model
{
    protected $fillable = [
        'media_id',
        'variant_name',
        'disk',
        'filename',
        'mime_type',
        'size',
        'width',
        'height',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
