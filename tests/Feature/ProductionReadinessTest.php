<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Core\Contracts\ActivityLoggerInterface;
use Illuminate\Support\Facades\Log;

class ProductionReadinessTest extends TestCase
{
    public function test_health_endpoint_returns_ok_status()
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'database' => 'ok',
            'cache' => 'ok',
            'queue' => 'ok',
            'storage' => 'ok',
        ]);
    }

    public function test_security_headers_are_present_on_web_responses()
    {
        $response = $this->get('/health');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_activity_logger_writes_structured_audit_logs()
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'AUDIT_TRAIL: User Created')
                    && $context['action'] === 'User Created'
                    && isset($context['timestamp']);
            });

        $logger = app(ActivityLoggerInterface::class);
        $logger->log('User Created', ['user_id' => 10], 1);
    }

    public function test_api_404_returns_standardized_json()
    {
        $response = $this->getJson('/api/non-existent-endpoint');

        $response->assertStatus(404);
        $response->assertJson(['status' => 'error', 'message' => 'Resource not found.']);
    }
}
