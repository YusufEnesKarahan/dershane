<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQSubscriptionPlan;
use App\Models\HQSubscription;
use App\Models\HQInvoice;
use App\Models\HQPayment;
use Illuminate\Support\Facades\Gate;

class HQBillingController extends Controller
{
    /**
     * View all subscription plans.
     */
    public function plans()
    {
        Gate::authorize('hq.viewBilling');
        
        $plans = HQSubscriptionPlan::withCount('subscriptions')->get();
        return view('admin.hq.billing.plans.index', compact('plans'));
    }

    /**
     * View all subscriptions.
     */
    public function subscriptions()
    {
        Gate::authorize('hq.viewBilling');
        
        $subscriptions = HQSubscription::with(['tenant', 'plan'])->latest()->paginate(20);
        return view('admin.hq.billing.subscriptions.index', compact('subscriptions'));
    }

    /**
     * View all invoices.
     */
    public function invoices()
    {
        Gate::authorize('hq.viewBilling');
        
        $invoices = HQInvoice::with(['tenant', 'subscription.plan'])->latest('issued_at')->paginate(20);
        return view('admin.hq.billing.invoices.index', compact('invoices'));
    }

    /**
     * View all payments.
     */
    public function payments()
    {
        Gate::authorize('hq.viewBilling');
        
        $payments = HQPayment::with(['invoice.tenant'])->latest('paid_at')->paginate(20);
        return view('admin.hq.billing.payments.index', compact('payments'));
    }
}
