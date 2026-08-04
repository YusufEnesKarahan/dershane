<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use Illuminate\Http\Request;

$routesToTest = [
    'Teacher' => [
        '/teacher/homework',
        '/teacher/students',
    ],
    'Student' => [
        '/student/courses',
        '/student/exams',
    ],
    'Parent' => [
        '/parent/students',
        '/parent/attendance',
        '/parent/exams',
        '/parent/payments',
    ]
];

echo "=== SPRINT 10.8.3 LARAVEL HTTP KERNEL ROUTE TEST ===\n\n";

foreach ($routesToTest as $roleName => $routes) {
    echo "--- Testing Role: $roleName ---\n";
    $user = User::whereHas('roles', fn($q) => $q->where('name', $roleName))->first() 
         ?? User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first();

    if (!$user) {
        echo "No user found for role $roleName\n";
        continue;
    }

    foreach ($routes as $path) {
        auth()->login($user);
        $request = Request::create($path, 'GET');
        $request->setLaravelSession(app('session.store'));
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        echo sprintf("Role [%s] User [%s] GET %s => Status: %d\n", $roleName, $user->email, $path, $status);
        $kernel->terminate($request, $response);
    }
    echo "\n";
}

echo "--- Testing Super Admin Full Coverage ---\n";
$superAdmin = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first() ?? User::first();

$allTargetRoutes = [
    '/teacher/homework',
    '/teacher/students',
    '/student/courses',
    '/student/exams',
    '/parent/students',
    '/parent/attendance',
    '/parent/exams',
    '/parent/payments',
];

foreach ($allTargetRoutes as $path) {
    auth()->login($superAdmin);
    $request = Request::create($path, 'GET');
    $request->setLaravelSession(app('session.store'));
    
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo sprintf("Super Admin [%s] GET %s => Status: %d\n", $superAdmin->email, $path, $status);
    $kernel->terminate($request, $response);
}
