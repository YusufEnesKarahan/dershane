<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\Role;
use Illuminate\Support\Facades\Event;

class InvitationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_invitation()
    {
        Event::fake([\App\Events\InvitationSent::class]);

        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        
        $response = $this->postJson('/api/onboarding/invite', [
            'tenant_id' => $tenant->id,
            'email' => 'newuser@test.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('hq_tenant_invitations', [
            'email' => 'newuser@test.com',
            'tenant_id' => $tenant->id,
        ]);
    }
}
