<?php
namespace App\Events\CMS;

use App\Models\Page;
use Illuminate\Foundation\Events\Dispatchable;

class PagePublished
{
    use Dispatchable;
    public function __construct(public Page $page) {}
}
