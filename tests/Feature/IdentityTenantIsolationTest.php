<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQUserSession;
use Laravel\Sanctum\Sanctum;

class IdentityTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_revoke_other_tenant_session()
    {
        $tenant1 = HQTenant::create(['name' => 'T1', 'slug' => 't1', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $tenant2 = HQTenant::create(['name' => 'T2', 'slug' => 't2', 'uuid' => \Illuminate\Support\Str::uuid()]);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $session2 = HQUserSession::create([
            'user_id' => $user2->id,
            'tenant_id' => $tenant2->id,
            'token_hash' => 'hash2',
            'expires_at' => now()->addDay(),
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);
        
        $this->actingAs($user1);
        
        $response = $this->deleteJson('/api/identity/session/' . $session2->id, ['tenant_id' => $tenant1->id]);
        
        $response->assertStatus(403);
    }
}
