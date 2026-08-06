<?php

namespace App\Domain\Finance\Services;

use App\Models\AcademicTerm;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Installment;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\PlatformAuditLog;
use App\Models\Student;
use App\Models\PreRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FinanceManagementService
{
    /**
     * Legacy support: Create Payment Plan
     */
    public function createPaymentPlan($student, $totalAmount = 0, $installmentsCount = 1, $startDate = null)
    {
        $activeTermId = AcademicTerm::where('is_active', true)->first()?->id ?? 1;

        if (is_array($student)) {
            $data = $student;
            $stu = Student::find($data['student_id'] ?? 1);
            $total = (float) ($data['total_amount'] ?? $data['amount'] ?? $totalAmount ?: 0);
            $discount = (float) ($data['discount_amount'] ?? 0);
            $net = max(0, $total - $discount);
            $qty = (int) ($data['total_installments'] ?? $data['installment_count'] ?? $data['installments'] ?? $installmentsCount ?: 1);
            $instAmount = (float) ($data['installment_amount'] ?? ($qty > 0 ? $net / $qty : $net));

            return PaymentPlan::create([
                'branch_id' => $data['branch_id'] ?? $stu?->branch_id ?? session('active_branch_id') ?? 1,
                'academic_term_id' => $data['academic_term_id'] ?? $activeTermId,
                'student_id' => $data['student_id'] ?? 1,
                'title' => $data['title'] ?? 'Eğitim Ödeme Planı',
                'total_amount' => $total,
                'discount_amount' => $discount,
                'net_amount' => $net,
                'total_installments' => $qty,
                'installment_amount' => $instAmount,
                'start_date' => $data['start_date'] ?? now()->format('Y-m-d'),
            ]);
        }

        $studentObj = is_object($student) ? $student : Student::find($student);
        $studentId = $studentObj?->id ?? (is_numeric($student) ? $student : 1);
        $branchId = $studentObj?->branch_id ?? session('active_branch_id') ?? 1;
        $total = (float) ($totalAmount ?: 0);
        $qty = (int) ($installmentsCount ?: 1);

        return PaymentPlan::create([
            'branch_id' => $branchId,
            'academic_term_id' => $activeTermId,
            'student_id' => $studentId,
            'title' => 'Eğitim Ödeme Planı',
            'total_amount' => $total,
            'net_amount' => $total,
            'total_installments' => $qty,
            'installment_amount' => $qty > 0 ? ($total / $qty) : $total,
            'start_date' => $startDate ?? now()->format('Y-m-d'),
        ]);
    }

    /**
     * Legacy support: Generate Installments for PaymentPlan
     */
    public function generateInstallments(PaymentPlan $plan, $installmentCount = null)
    {
        $count = (int) ($installmentCount ?: ($plan->total_installments ?: 1));
        $amount = $plan->installment_amount ?: (($plan->net_amount ?? $plan->total_amount) / $count);
        $startDate = Carbon::parse($plan->start_date ?: now());

        $installments = collect();
        for ($i = 0; $i < $count; $i++) {
            $inst = Installment::create([
                'branch_id' => $plan->branch_id,
                'payment_plan_id' => $plan->id,
                'student_id' => $plan->student_id,
                'installment_no' => $i + 1,
                'installment_number' => $i + 1,
                'amount' => $amount,
                'due_date' => $startDate->copy()->addMonths($i)->format('Y-m-d'),
                'status' => 'Pending',
            ]);
            $installments->push($inst);
        }
        return $installments;
    }

    /**
     * Create Invoice with multiple line items
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $student = Student::with(['branch', 'classroom', 'user'])->findOrFail($data['student_id']);
            $branchId = $data['branch_id'] ?? $student->branch_id ?? auth()->user()?->branch_id ?? 1;

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4));

            $invoice = Invoice::create([
                'branch_id' => $branchId,
                'invoice_number' => $invoiceNumber,
                'student_id' => $student->id,
                'guardian_id' => $data['guardian_id'] ?? $student->guardian_id ?? null,
                'issue_date' => $data['issue_date'] ?? now()->format('Y-m-d'),
                'due_date' => $data['due_date'] ?? now()->format('Y-m-d'),
                'total_amount' => 0.00,
                'paid_amount' => 0.00,
                'status' => 'Pending', // Bekliyor
                'description' => $data['description'] ?? null,
            ]);

            $totalAmount = 0.00;
            $items = $data['items'] ?? [];

            if (empty($items) && isset($data['amount'])) {
                $items = [[
                    'item_type' => $data['item_type'] ?? 'Kayıt Ücreti',
                    'description' => $data['description'] ?? 'Öğrenim Ücreti',
                    'quantity' => 1,
                    'unit_price' => $data['amount'],
                ]];
            }

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = $qty * $unitPrice;
                $totalAmount += $lineTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'] ?? 'Kayıt Ücreti',
                    'description' => $item['description'] ?? 'Eğitim Hizmeti',
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);
            }

            $invoice->update(['total_amount' => $totalAmount]);

            $this->logAudit('invoice.created', 'Invoice', $invoice->id, "Fatura oluşturuldu: {$invoice->invoice_number} - Tutar: {$totalAmount} TL");

            return $invoice;
        });
    }

    /**
     * Record Payment for an Invoice
     */
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $paymentNumber = 'PAY-' . date('Ymd') . '-' . Str::upper(Str::random(4));
            $amount = (float) ($data['amount'] ?? 0);

            $payment = Payment::create([
                'branch_id' => $invoice->branch_id,
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'Nakit',
                'payment_date' => $data['payment_date'] ?? now(),
                'reference_no' => $data['reference_no'] ?? null,
                'received_by' => auth()->id() ?? $data['received_by'] ?? 1,
                'notes' => $data['notes'] ?? null,
                'status' => 'Completed',
            ]);

            // Recalculate invoice paid amount & status
            $newPaid = (float) $invoice->payments()->where('status', 'Completed')->sum('amount');
            $status = 'Pending';
            if ($newPaid >= (float) $invoice->total_amount && $invoice->total_amount > 0) {
                $status = 'Paid'; // Ödendi
            } elseif ($newPaid > 0) {
                $status = 'Partial'; // Kısmi Ödendi
            }

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $status,
            ]);

            $this->logAudit('payment.created', 'Payment', $payment->id, "Ödeme alındı: {$amount} TL ({$payment->payment_method}) - Fatura: {$invoice->invoice_number}");

            // Send notification if student user exists
            if ($invoice->student?->user_id) {
                Notification::create([
                    'branch_id' => $invoice->branch_id,
                    'user_id' => $invoice->student->user_id,
                    'title' => 'Ödeme Alındı',
                    'message' => "{$amount} TL tutarındaki {$payment->payment_method} ödemeniz sisteme işlendi. Teşekkür ederiz.",
                    'type' => 'payment',
                    'status' => 'Unread',
                ]);
            }

            return $payment;
        });
    }

    /**
     * Delete Payment
     */
    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $paymentId = $payment->id;
            $amount = $payment->amount;

            $payment->delete();

            if ($invoice) {
                $newPaid = (float) $invoice->payments()->where('status', 'Completed')->sum('amount');
                $status = 'Pending';
                if ($newPaid >= (float) $invoice->total_amount && $invoice->total_amount > 0) {
                    $status = 'Paid';
                } elseif ($newPaid > 0) {
                    $status = 'Partial';
                }

                $invoice->update([
                    'paid_amount' => $newPaid,
                    'status' => $status,
                ]);
            }

            $this->logAudit('payment.deleted', 'Payment', $paymentId, "Ödeme silindi: {$amount} TL");
        });
    }

    /**
     * Finance Dashboard Stats & Chart Data
     */
    public function getDashboardMetrics(?int $branchId = null): array
    {
        $branchId = $branchId ?? session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $invoicesQuery = Invoice::where('branch_id', $branchId);
        $paymentsQuery = Payment::where('branch_id', $branchId)->where('status', 'Completed');
        $preRegQuery = PreRegistration::where('branch_id', $branchId);

        $totalCollected = (clone $paymentsQuery)->sum('amount');
        $thisMonthCollected = (clone $paymentsQuery)->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount');
        $todayCollected = (clone $paymentsQuery)->whereDate('payment_date', now()->toDateString())->sum('amount');

        $totalInvoiced = (clone $invoicesQuery)->sum('total_amount');
        $totalPaidOnInvoices = (clone $invoicesQuery)->sum('paid_amount');
        $pendingAmount = max(0, $totalInvoiced - $totalPaidOnInvoices);

        $openInvoicesCount = (clone $invoicesQuery)->whereIn('status', ['Pending', 'Partial', 'Bekliyor', 'Kısmi Ödendi'])->count();
        $overdueInvoicesCount = (clone $invoicesQuery)->whereIn('status', ['Pending', 'Partial', 'Bekliyor', 'Kısmi Ödendi'])->where('due_date', '<', now()->toDateString())->count();

        // 12 Months collection trend
        $chartMonths = [];
        $chartCollections = [];
        $chartPreRegs = [];
        $chartConverted = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');
            $chartMonths[] = $monthName;

            $chartCollections[] = (clone $paymentsQuery)
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $chartPreRegs[] = (clone $preRegQuery)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $chartConverted[] = (clone $preRegQuery)
                ->where('status', 'Kayıt Oldu')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
        }

        return [
            'total_collected' => $totalCollected,
            'pending_amount' => $pendingAmount,
            'this_month_collected' => $thisMonthCollected,
            'today_collected' => $todayCollected,
            'open_invoices_count' => $openInvoicesCount,
            'overdue_invoices_count' => $overdueInvoicesCount,
            'chart_months' => $chartMonths,
            'chart_collections' => $chartCollections,
            'chart_pre_regs' => $chartPreRegs,
            'chart_converted' => $chartConverted,
        ];
    }

    private function logAudit(string $event, string $auditableType, int $auditableId, string $description): void
    {
        PlatformAuditLog::record(auth()->user(), $event, $auditableType, ['description' => $description]);
    }
}
