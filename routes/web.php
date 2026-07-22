<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes Registry
|--------------------------------------------------------------------------
|
| This file is the primary entry point for all web-facing routes. It includes
| sub-route files for Frontend website, Admin dashboard, and Auth forms.
|
*/

require __DIR__.'/frontend.php';
require __DIR__.'/admin.php';
require __DIR__.'/parent.php';
require __DIR__.'/auth.php';
