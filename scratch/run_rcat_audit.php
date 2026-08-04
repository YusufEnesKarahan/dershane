<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Running RCAT Full System Route & RBAC Audit ===\n\n";

$rolesToTest = [
    'Super Admin' => 'superadmin@test.com',
    'Branch Admin' => 'admin1@test.com',
    'Teacher' => 'teacher1@test.com',
    'Student' => 'student1@test.com',
    'Parent' => 'parent1@test.com',
];

$users = [];
foreach ($rolesToTest as $roleName => $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        $users[$roleName] = $user;
    } else {
        echo "WARNING: User with email {$email} not found for role {$roleName}!\n";
    }
}

// Get all registered GET routes
$routeCollection = Route::getRoutes();
$routesToAudit = [];

foreach ($routeCollection as $route) {
    $methods = $route->methods();
    if (in_array('GET', $methods) || in_array('HEAD', $methods)) {
        $uri = $route->uri();
        $name = $route->getName();
        
        // Skip debugbar, horizon, or telescope routes if any
        if (str_starts_with($uri, '_') || str_starts_with($uri, 'sanctum')) {
            continue;
        }

        // Replace route parameters with sample values
        $testUri = preg_replace('/\{[a-zA-Z0-9_]+\}/', '1', $uri);

        $routesToAudit[] = [
            'uri' => $uri,
            'testUri' => '/' . ltrim($testUri, '/'),
            'name' => $name ?? 'unnamed',
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
        ];
    }
}

echo "Total GET/HEAD routes discovered: " . count($routesToAudit) . "\n\n";

$auditResults = [];

foreach ($routesToAudit as $routeInfo) {
    $uri = $routeInfo['testUri'];
    $name = $routeInfo['name'];
    $routeEntry = [
        'uri' => $uri,
        'name' => $name,
        'middleware' => implode(', ', $routeInfo['middleware']),
        'roles' => [],
    ];

    foreach ($users as $roleName => $user) {
        Auth::login($user);
        session(['active_branch_id' => $user->branch_id ?? 1]);

        $request = \Illuminate\Http\Request::create($uri, 'GET');
        $request->setUserResolver(fn() => $user);

        try {
            $response = $kernel->handle($request);
            $statusCode = $response->getStatusCode();
            
            // Check if response is redirect
            if ($statusCode >= 300 && $statusCode < 400) {
                $location = $response->headers->get('Location');
                $statusStr = "{$statusCode} -> {$location}";
            } else {
                $statusStr = (string)$statusCode;
            }

            // Determine if RBAC result is Pass or Fail or Bug
            $isSuccess = ($statusCode === 200);
            $isForbidden = ($statusCode === 403);
            $isError = ($statusCode >= 500);

            $routeEntry['roles'][$roleName] = [
                'status' => $statusCode,
                'statusStr' => $statusStr,
                'isError' => $isError,
            ];
        } catch (\Throwable $e) {
            $routeEntry['roles'][$roleName] = [
                'status' => 500,
                'statusStr' => '500 Exception: ' . substr($e->getMessage(), 0, 80),
                'isError' => true,
            ];
        }
    }

    $auditResults[] = $routeEntry;
}

// Print summary matrix
echo "=== AUDIT RESULTS SUMMARY ===\n";
printf("%-45s | %-12s | %-12s | %-12s | %-12s | %-12s\n", "URI", "Super Admin", "Branch Admin", "Teacher", "Student", "Parent");
echo str_repeat("-", 115) . "\n";

foreach ($auditResults as $res) {
    printf("%-45s | %-12s | %-12s | %-12s | %-12s | %-12s\n",
        substr($res['uri'], 0, 44),
        $res['roles']['Super Admin']['statusStr'] ?? 'N/A',
        $res['roles']['Branch Admin']['statusStr'] ?? 'N/A',
        $res['roles']['Teacher']['statusStr'] ?? 'N/A',
        $res['roles']['Student']['statusStr'] ?? 'N/A',
        $res['roles']['Parent']['statusStr'] ?? 'N/A'
    );
}

// Save json output for report generator
file_put_contents(__DIR__ . '/rcat_audit_data.json', json_encode($auditResults, JSON_PRETTY_PRINT));
echo "\nSaved full audit data to scratch/rcat_audit_data.json\n";
