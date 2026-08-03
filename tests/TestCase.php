<?php

namespace Tests;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $branch;
    protected $superAdmin;
    protected $tenantUser;

    protected function setupSaaSTenant()
    {
        // Seed roles if necessary
        if (\App\Models\Role::count() === 0) {
            $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
        }

        $this->branch = \App\Models\Branch::firstOrCreate(
            ['slug' => 'main-branch'],
            ['name' => 'Main Branch']
        );

        $this->tenantUser = \App\Models\User::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->superAdmin = \App\Models\User::factory()->create([
            'branch_id' => $this->branch->id,
        ]);
        $role = \App\Models\Role::where('name', 'Super Admin')->first();
        if ($role) {
            $this->superAdmin->roles()->attach($role);
        }

        // Setup active branch in session and context
        session(['active_branch_id' => $this->branch->id]);
        if (class_exists(\App\Core\Context\TenantContext::class)) {
            \App\Core\Context\TenantContext::setActiveBranchId($this->branch->id);
        }

        $plan = Plan::firstOrCreate(
            ['slug' => 'test-tenant-plan'],
            [
                'name' => 'Test Tenant Plan',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'max_students' => 9999,
                'max_users' => 9999,
                'max_teachers' => 9999,
                'is_active' => true,
                'features' => ['sms', 'advanced_reports', 'api_access', 'online_payment', 'attendance'],
            ]
        );

        Subscription::updateOrCreate(
            ['branch_id' => $this->branch->id],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => now()->subDay(),
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addMonth(),
            ]
        );

        // Add dummy SystemIdentity and Term for bypass onboarding
        \App\Models\SystemIdentity::firstOrCreate(
            ['company_name' => 'Test Company'],
            ['uuid' => \Illuminate\Support\Str::uuid(), 'brand_name' => 'Test Brand']
        );
        \App\Models\AcademicTerm::firstOrCreate(
            ['is_active' => true],
            ['name' => 'Active Term', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonths(8)]
        );

        return $this;
    }
}
