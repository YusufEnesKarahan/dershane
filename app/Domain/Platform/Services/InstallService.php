<?php

namespace App\Domain\Platform\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\License;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallService
{
    protected string $lockFilePath;

    public function __construct()
    {
        $this->lockFilePath = storage_path('app/installed.lock');
    }

    /**
     * Check if the application is already installed.
     */
    public function isInstalled(): bool
    {
        // 1. Lock file existence check (Primary indicator)
        if (file_exists($this->lockFilePath)) {
            return true;
        }

        // 2. Database checks as fallback (to prevent installing over populated DBs)
        try {
            if (Schema::hasTable('users') && Schema::hasTable('licenses')) {
                $hasAdmin = User::whereHas('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                })->exists();
                $hasLicense = License::exists();

                if ($hasAdmin && $hasLicense) {
                    // Create lock file if it was missing but app is fully set up
                    $this->createLockFile();
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Database not reachable or tables don't exist yet
        }

        return false;
    }

    /**
     * Check environment requirements.
     */
    public function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version (>= 8.2)',
                'current' => PHP_VERSION,
                'satisfied' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'current' => extension_loaded('pdo') ? 'Loaded' : 'Not Loaded',
                'satisfied' => extension_loaded('pdo'),
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'current' => extension_loaded('mbstring') ? 'Loaded' : 'Not Loaded',
                'satisfied' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'current' => extension_loaded('openssl') ? 'Loaded' : 'Not Loaded',
                'satisfied' => extension_loaded('openssl'),
            ],
            'storage_writable' => [
                'name' => 'Storage Directory Writable',
                'current' => is_writable(storage_path()) ? 'Writable' : 'Not Writable',
                'satisfied' => is_writable(storage_path()),
            ],
            'cache_writable' => [
                'name' => 'Cache Directory Writable',
                'current' => is_writable(bootstrap_path('cache')) ? 'Writable' : 'Not Writable',
                'satisfied' => is_writable(bootstrap_path('cache')),
            ],
            'db_reachable' => [
                'name' => 'Database Connection',
                'current' => $this->testDbConnection() ? 'Connected' : 'Connection Failed',
                'satisfied' => $this->testDbConnection(),
            ]
        ];

        return $requirements;
    }

    /**
     * Test the database connection.
     */
    public function testDbConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Run all migrations and seed the Roles & Permissions.
     */
    public function runMigrations(): bool
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);
            return true;
        } catch (\Throwable $e) {
            logger()->error('Installation Migration/Seeding Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete the installation process.
     */
    public function completeInstallation(array $adminData, string $branchName): bool
    {
        try {
            return DB::transaction(function () use ($adminData, $branchName) {
                // 1. Create Default Branch
                $branch = Branch::firstOrCreate(
                    ['slug' => Str::slug($branchName)],
                    ['name' => $branchName]
                );

                // 2. Create Super Admin User
                $superAdminRole = Role::where('name', 'Super Admin')->first();
                if (!$superAdminRole) {
                    throw new \Exception('Super Admin role not found. Please run migrations & seeders first.');
                }

                $user = User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => bcrypt($adminData['password']),
                    'branch_id' => $branch->id,
                ]);

                $user->roles()->sync([$superAdminRole->id]);

                // 3. Create Default Active License
                License::create([
                    'license_key' => 'LIC-' . strtoupper(Str::random(16)),
                    'status' => 'active',
                    'plan' => 'enterprise',
                    'expires_at' => now()->addYear(),
                    'metadata' => [
                        'owner' => $adminData['email'],
                        'branch' => $branchName,
                    ]
                ]);

                // 4. Save installation lock file
                $this->createLockFile();

                return true;
            });
        } catch (\Throwable $e) {
            logger()->error('Complete installation transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create the installation lock file.
     */
    public function createLockFile(): void
    {
        $dir = dirname($this->lockFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->lockFilePath, json_encode([
            'installed_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT));
    }
}
