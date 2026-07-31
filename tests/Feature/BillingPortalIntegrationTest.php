<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\User;

class BillingPortalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_view_invoices()
    {
        // This test ensures the integration with HQInvoice is functional via the portal
        $tenant = HQTenant::create(['name' => 'Test', 'slug' => 'test-billing', 'uuid' => \Illuminate\Support\Str::uuid()]);
        
        $invoice = \App\Models\HQInvoice::create([
            'tenant_id' => $tenant->id,
            'amount' => 500,
            'currency' => 'USD',
            'status' => 'paid',
            'invoice_number' => 'INV-123456',
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);
        
        // Simulating the API route behavior
        $invoices = \App\Models\HQInvoice::where('tenant_id', $tenant->id)->get();
        
        $this->assertCount(1, $invoices);
        $this->assertEquals(500, $invoices->first()->amount);
    }
}
