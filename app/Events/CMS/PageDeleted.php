<?php
namespace App\Events\CMS;

use App\Models\Page;
use Illuminate\Foundation\Events\Dispatchable;

class PageDeleted
{
    use Dispatchable;
    public function __construct(public Page $page) {}
}
