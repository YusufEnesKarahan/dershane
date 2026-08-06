<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Branch;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Domain\Finance\Services\FinanceManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected FinanceManagementService $financeService) {}

    public function dashboard(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Ajax Live Search Students (min 3 chars)
     */
    public function searchStudents(Request $request)
    {
        $term = trim($request->input('q', ''));
        if (mb_strlen($term) < 3) {
            return response()->json([]);
        }

        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $students = Student::with(['classroom', 'branch', 'guardian'])
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->where(function ($q) use ($term) {
                $q->where('student_number', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('tc_no', 'like', "%{$term}%");
            })
            ->take(15)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'student_number' => $s->student_number,
                    'full_name' => $s->first_name . ' ' . $s->last_name,
                    'classroom_name' => $s->classroom?->name ?? 'Sınıfsız',
                    'branch_name' => $s->branch?->name ?? 'Genel Şube',
                    'branch_id' => $s->branch_id,
                    'guardian_id' => $s->guardian_id,
                    'guardian_name' => $s->guardian?->name ?? 'Veli Tanımlanmamış',
                    'card_display' => "{$s->student_number} - {$s->first_name} {$s->last_name} | " . ($s->classroom?->name ?? 'Sınıfsız') . " | " . ($s->branch?->name ?? 'Merkez Şube'),
                ];
            });

        return response()->json($students);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $query = Invoice::with(['student.classroom', 'student.branch', 'guardian', 'items', 'payments.receiver'])
            ->where('branch_id', $branchId)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $students = Student::where('branch_id', $branchId)->get();

        return view('admin.invoices.index', compact('invoices', 'branches', 'students'));
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        $branches = Branch::all();
        return view('admin.invoices.create', compact('branches'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('create', Invoice::class);

        $invoice = $this->financeService->createInvoice($request->validated());

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Fatura başarıyla oluşturuldu.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['student.classroom', 'student.branch', 'guardian', 'items', 'payments.receiver']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $this->financeService->recordPayment($invoice, $request->validated());

        return back()->with('success', 'Tahsilat başarıyla kaydedildi.');
    }

    public function destroyPayment(Payment $payment)
    {
        $this->authorize('delete', $payment->invoice);

        $this->financeService->deletePayment($payment);

        return back()->with('success', 'Tahsilat kaydı silindi.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Fatura silindi.');
    }
}
