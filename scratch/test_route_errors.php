<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'superadmin@test.com')->first();
Auth::login($user);

$urls = [
    '/admin/classrooms/create',
    '/admin/teachers',
    '/admin/courses',
    '/admin/attendance',
    '/admin/exams',
    '/admin/reporting/reports',
];

foreach ($urls as $url) {
    try {
        $req = \Illuminate\Http\Request::create("http://127.0.0.1:8000" . $url, 'GET');
        $req->setLaravelSession($app->make('session')->driver());
        $res = $kernel->handle($req);
        echo "[$url] Status: " . $res->getStatusCode() . "\n";
        if ($res->getStatusCode() >= 400) {
            $content = $res->getContent();
            if (preg_match('/<title>(.*?)<\/title>/s', $content, $m)) {
                echo "  Title: " . trim($m[1]) . "\n";
            }
            if (preg_match('/class="exception-message">(.*?)<\/div>/s', $content, $m)) {
                echo "  Msg: " . trim(strip_tags($m[1])) . "\n";
            }
        }
    } catch (\Throwable $e) {
        echo "[$url] Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
