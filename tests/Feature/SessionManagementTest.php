<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQUserSession;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Event;
use App\Events\SessionRevoked;

class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_active_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        HQUserSession::create([
            'user_id' => $user->id,
            'tenant_id' => null,
            'token_hash' => 'hash1',
            'expires_at' => now()->addDay(),
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);
        
        $response = $this->getJson('/api/identity/sessions');
        
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'sessions');
    }

    public function test_user_can_revoke_session()
    {
        Event::fake([SessionRevoked::class]);

        $user = User::factory()->create();
        $this->actingAs($user);
        
        $session = HQUserSession::create([
            'user_id' => $user->id,
            'tenant_id' => null,
            'token_hash' => 'hash1',
            'expires_at' => now()->addDay(),
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);
        
        $response = $this->deleteJson('/api/identity/session/' . $session->id);
        
        $response->assertStatus(200);
        $this->assertNull(HQUserSession::find($session->id));
        
        Event::assertDispatched(SessionRevoked::class);
    }
}
