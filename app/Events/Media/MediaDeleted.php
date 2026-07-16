<?php
namespace App\Events\Media;

use App\Models\Media;
use Illuminate\Foundation\Events\Dispatchable;

class MediaDeleted
{
    use Dispatchable;
    public function __construct(public Media $media) {}
}
