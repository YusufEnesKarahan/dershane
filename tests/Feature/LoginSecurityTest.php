<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use App\Events\LoginSuccessful;
use App\Events\LoginFailed;
use App\Events\AccountLocked;

class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_resets_attempts_and_fires_event()
    {
        Event::fake([LoginSuccessful::class]);

        $user = User::factory()->create(['password' => Hash::make('password123')]);
        
        $response = $this->postJson('/api/identity/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        
        Event::assertDispatched(LoginSuccessful::class);
    }

    public function test_failed_login_increments_attempts()
    {
        Event::fake([LoginFailed::class]);

        $user = User::factory()->create(['password' => Hash::make('password123')]);
        
        $response = $this->postJson('/api/identity/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
        
        $security = \App\Models\HQUserSecurity::where('user_id', $user->id)->first();
        $this->assertEquals(1, $security->failed_attempts);
        
        Event::assertDispatched(LoginFailed::class);
    }

    public function test_account_locks_after_max_failed_attempts()
    {
        Event::fake([AccountLocked::class]);

        $tenant = HQTenant::create(['name' => 'T1', 'slug' => 't1', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $user = User::factory()->create(['password' => Hash::make('password123')]);
        
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/identity/login', [
                'email' => $user->email,
                'password' => 'wrong',
                'tenant_id' => $tenant->id,
            ]);
        }

        $security = \App\Models\HQUserSecurity::where('user_id', $user->id)->first();
        $this->assertNotNull($security->locked_until);
        
        Event::assertDispatched(AccountLocked::class);
        
        // Next valid login should fail because of lock
        $response = $this->postJson('/api/identity/login', [
            'email' => $user->email,
            'password' => 'password123',
            'tenant_id' => $tenant->id,
        ]);
        
        $response->assertStatus(401);
    }
}
