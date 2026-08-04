<?php

namespace App\Domain\Finance\Services;

use App\Models\PaymentPlan;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Discount;
use App\Models\Refund;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use Illuminate\Support\Facades\DB;
use Exception;

class FinanceManagementService
{
    protected $notificationService;
    protected $limitService;

    public function __construct(NotificationService $notificationService, SubscriptionLimitService $limitService)
    {
        $this->notificationService = $notificationService;
        $this->limitService = $limitService;
    }

    public function createPaymentPlan(array $data): PaymentPlan
    {
        $this->limitService->checkPaymentPlanLimit($data['branch_id']);

        return DB::transaction(function () use ($data) {
            $data['net_amount'] = $data['total_amount'] - ($data['discount_amount'] ?? 0);
            $plan = PaymentPlan::create($data);
            return $plan;
        });
    }

    public function updatePaymentPlan(PaymentPlan $plan, array $data): PaymentPlan
    {
        return DB::transaction(function () use ($plan, $data) {
            $data['net_amount'] = ($data['total_amount'] ?? $plan->total_amount) - ($data['discount_amount'] ?? $plan->discount_amount);
            $plan->update($data);
            
            $this->recalculateBalances($plan);
            return $plan;
        });
    }

    public function generateInstallments(PaymentPlan $plan, int $count, array $dueDates = [], array $amounts = []): void
    {
        DB::transaction(function () use ($plan, $count, $dueDates, $amounts) {
            // Delete pending installments if regenerating
            $plan->installments()->where('status', 'pending')->delete();
            
            $defaultAmount = $plan->net_amount / $count;
            $startDate = now();

            for ($i = 1; $i <= $count; $i++) {
                $amount = $amounts[$i-1] ?? $defaultAmount;
                $dueDate = $dueDates[$i-1] ?? $startDate->copy()->addMonths($i)->format('Y-m-d');
                
                Installment::create([
                    'branch_id' => $plan->branch_id,
                    'payment_plan_id' => $plan->id,
                    'installment_no' => $i,
                    'due_date' => $dueDate,
                    'amount' => $amount,
                    'remaining_amount' => $amount,
                    'status' => 'pending'
                ]);
            }
        });
    }

    public function recordPayment(array $data, ?Installment $installment = null): Payment
    {
        return DB::transaction(function () use ($data, $installment) {
            $payment = Payment::create($data);

            PaymentTransaction::create([
                'branch_id' => $payment->branch_id,
                'payment_id' => $payment->id,
                'transaction_type' => 'collection',
                'amount' => $payment->amount,
                'description' => 'Payment received via ' . $payment->payment_method
            ]);

            if ($installment) {
                $installment->paid_amount += $payment->amount;
                $installment->remaining_amount = max(0, $installment->amount - $installment->paid_amount);
                
                if ($installment->remaining_amount == 0) {
                    $installment->status = 'paid';
                } elseif ($installment->paid_amount > 0) {
                    $installment->status = 'partial';
                }
                $installment->save();
                
                $this->recalculateBalances($installment->paymentPlan);
            }

            if ($payment->student && $payment->student->user) {
                $this->notificationService->send(
                    $payment->student->user,
                    'Ödeme Alındı',
                    "{$payment->amount} tutarında ödemeniz alınmıştır. Teşekkür ederiz.",
                    \App\Domain\Notification\Enums\NotificationType::PAYMENT
                );
            }

            return $payment;
        });
    }

    public function cancelPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            if ($payment->installment) {
                $inst = $payment->installment;
                $inst->paid_amount = max(0, $inst->paid_amount - $payment->amount);
                $inst->remaining_amount = $inst->amount - $inst->paid_amount;
                
                if ($inst->paid_amount == 0) {
                    $inst->status = $inst->due_date->isPast() ? 'overdue' : 'pending';
                } else {
                    $inst->status = 'partial';
                }
                $inst->save();
                
                $this->recalculateBalances($inst->paymentPlan);
            }
            
            PaymentTransaction::where('payment_id', $payment->id)->delete();
            $payment->delete();
        });
    }

    public function applyDiscount(array $data, PaymentPlan $plan): Discount
    {
        return DB::transaction(function () use ($data, $plan) {
            $discount = Discount::create($data);
            
            $plan->discount_amount += $discount->value;
            $plan->net_amount = $plan->total_amount - $plan->discount_amount;
            $plan->save();
            
            $this->recalculateBalances($plan);
            
            return $discount;
        });
    }

    public function refundPayment(Payment $payment, float $amount, string $reason, int $approvedBy): Refund
    {
        if ($amount > $payment->amount) {
            throw new Exception("İade tutarı, tahsilat tutarından büyük olamaz.");
        }
        
        return DB::transaction(function () use ($payment, $amount, $reason, $approvedBy) {
            $refund = Refund::create([
                'branch_id' => $payment->branch_id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'reason' => $reason,
                'approved_by' => $approvedBy,
                'status' => 'completed'
            ]);
            
            PaymentTransaction::create([
                'branch_id' => $payment->branch_id,
                'payment_id' => $payment->id,
                'transaction_type' => 'refund',
                'amount' => -$amount,
                'description' => "Refund: {$reason}"
            ]);
            
            if ($payment->installment) {
                $inst = $payment->installment;
                $inst->paid_amount -= $amount;
                $inst->remaining_amount += $amount;
                
                if ($inst->paid_amount <= 0) {
                    $inst->status = $inst->due_date->isPast() ? 'overdue' : 'pending';
                } else {
                    $inst->status = 'partial';
                }
                $inst->save();
                
                $this->recalculateBalances($inst->paymentPlan);
            }
            
            return $refund;
        });
    }

    public function recalculateBalances(PaymentPlan $plan): void
    {
        // Simple recalculation: check if all installments paid
        $totalRemaining = $plan->installments()->sum('remaining_amount');
        if ($totalRemaining == 0 && $plan->installments()->count() > 0) {
            $plan->status = 'completed';
        } else {
            $plan->status = 'active';
        }
        $plan->save();
    }

    public function closePaymentPlan(PaymentPlan $plan): void
    {
        $plan->update(['status' => 'completed']);
    }

    public function duplicatePaymentPlan(PaymentPlan $plan, int $newStudentId): PaymentPlan
    {
        return DB::transaction(function () use ($plan, $newStudentId) {
            $newPlan = $plan->replicate();
            $newPlan->student_id = $newStudentId;
            $newPlan->status = 'active';
            $newPlan->save();
            
            foreach ($plan->installments as $inst) {
                $newInst = $inst->replicate();
                $newInst->payment_plan_id = $newPlan->id;
                $newInst->paid_amount = 0;
                $newInst->remaining_amount = $newInst->amount;
                $newInst->status = 'pending';
                $newInst->save();
            }
            
            return $newPlan;
        });
    }
}
