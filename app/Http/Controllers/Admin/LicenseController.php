<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\LicenseService;
use Illuminate\Http\Request;

use App\Domain\Platform\Services\SubscriptionService;
use App\Domain\Billing\Services\BillingService;
use App\Models\PlatformAuditLog;
use App\Models\Plan;
use App\Models\SubscriptionPayment;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService,
        protected SubscriptionService $subscriptionService,
        protected BillingService $billingService
    ) {}

    public function index()
    {
        $licenseStatus = $this->licenseService->checkLicense();
        $license = $this->licenseService->getCurrentLicense();
        $plans = Plan::where('is_active', true)->get();

        if ($license && $license->subscription) {
            $license->subscription->load(['payments', 'payments.invoice']);
        }

        return view('admin.platform.licenses.index', compact('licenseStatus', 'license', 'plans'));
    }

    public function activate(Request $request)
    {
        $license = $this->licenseService->getCurrentLicense();
        if (!$license || !$license->subscription) {
            return back()->with('error', 'Lisans veya abonelik bulunamadı.');
        }

        // Create pending payment for current plan
        $this->billingService->createSubscriptionPayment($license->subscription, [
            'amount' => $license->subscription->plan->price,
            'currency' => 'TRY'
        ]);
        
        return back()->with('success', 'Ödeme kaydı oluşturuldu. Lütfen işlemi tamamlayın.');
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $license = $this->licenseService->getCurrentLicense();
        $plan = Plan::find($request->plan_id);

        if (!$license || !$plan || !$license->subscription) {
            return back()->with('error', 'Geçersiz talep.');
        }

        // Apply plan change via SubscriptionService
        $subscription = $this->subscriptionService->changePlan($license, $plan);
        PlatformAuditLog::record(auth()->user(), 'license.changed', $license, [
            'description' => 'Lisans planı değiştirildi.',
            'old_plan_id' => $license->subscription?->plan_id,
            'new_plan_id' => $plan->id,
        ]);

        // Create pending payment for new plan
        $this->billingService->createSubscriptionPayment($subscription, [
            'amount' => $plan->price,
            'currency' => 'TRY'
        ]);

        return back()->with('success', "Plan değiştirildi ve ödeme kaydı oluşturuldu. Lütfen işlemi tamamlayın.");
    }

    public function pay(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:subscription_payments,id'
        ]);

        $payment = SubscriptionPayment::findOrFail($request->payment_id);
        
        if ($payment->status === 'paid') {
            return back()->with('error', 'Bu ödeme zaten tamamlanmış.');
        }

        $this->billingService->completePayment($payment);
        PlatformAuditLog::record(auth()->user(), 'subscription.payment.completed', $payment, [
            'description' => 'Abonelik ödemesi tamamlandı.',
            'subscription_id' => $payment->subscription_id,
            'payment_id' => $payment->id,
        ]);

        return back()->with('success', 'Ödeme başarıyla tamamlandı ve abonelik aktifleştirildi.');
    }
}
