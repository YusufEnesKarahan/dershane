<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaUsage extends Model
{
    protected $fillable = ['media_id', 'model_type', 'model_id', 'field'];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
