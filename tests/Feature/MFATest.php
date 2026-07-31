<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class MFATest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enable_mfa()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/identity/mfa/enable');
        
        $response->assertStatus(200);
        
        $security = \App\Models\HQUserSecurity::where('user_id', $user->id)->first();
        $this->assertTrue($security->mfa_enabled);
    }

    public function test_user_can_verify_mfa()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/identity/mfa/verify', [
            'code' => '123456'
        ]);
        
        $response->assertStatus(200);
    }
}
