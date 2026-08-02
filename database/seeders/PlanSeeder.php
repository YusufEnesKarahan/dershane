<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Küçük işletmeler ve yeni başlayan dershaneler için.',
                'price' => 299.00,
                'billing_period' => 'monthly',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'max_students' => 500,
                'max_users' => 5,
                'max_teachers' => 3,
                'is_active' => true,
                'features' => json_encode(['basic_dashboard', 'manual_billing']),
                'limits' => json_encode([
                    'students' => 500,
                    'users' => 5,
                    'branches' => 1,
                ]),
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Büyüyen eğitim kurumları için ideal özellikler.',
                'price' => 799.00,
                'billing_period' => 'monthly',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'max_students' => 2000,
                'max_users' => 20,
                'max_teachers' => 10,
                'is_active' => true,
                'features' => json_encode(['basic_dashboard', 'manual_billing', 'advanced_reports']),
                'limits' => json_encode([
                    'students' => 2000,
                    'users' => 20,
                    'branches' => 3,
                ]),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Birden fazla şubesi olan büyük zincir dershaneler için.',
                'price' => 1999.00,
                'billing_period' => 'monthly',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'max_students' => 10000,
                'max_users' => 100,
                'max_teachers' => 50,
                'is_active' => true,
                'features' => json_encode(['basic_dashboard', 'manual_billing', 'advanced_reports', 'multi_branch']),
                'limits' => json_encode([
                    'students' => 10000,
                    'users' => 100,
                    'branches' => 10,
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
