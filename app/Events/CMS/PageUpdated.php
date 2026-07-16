<?php
namespace App\Events\CMS;

use App\Models\Page;
use Illuminate\Foundation\Events\Dispatchable;

class PageUpdated
{
    use Dispatchable;
    public function __construct(public Page $page) {}
}
