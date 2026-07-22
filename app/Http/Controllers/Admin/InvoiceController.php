<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\DTOs\Finance\CreateInvoiceDTO;
use App\Domain\Finance\Actions\CreateInvoice;
use App\Domain\Finance\Actions\CancelInvoice;
use App\Domain\Finance\Services\FinanceAnalyticsService;
use App\Core\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceRepositoryInterface $repository,
        protected FinanceAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $invoices = $this->repository->paginate(15, $request->all());
        $students = Student::all();

        return view('admin.invoices.index', compact('invoices', 'students'));
    }

    public function store(Request $request, CreateInvoice $action)
    {
        $this->authorize('create', Invoice::class);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $items = [
            [
                'description' => $request->description,
                'quantity' => 1,
                'unit_price' => (float) $request->amount,
            ]
        ];

        $dto = new CreateInvoiceDTO(
            $request->invoice_number,
            (int) $request->student_id,
            $request->issue_date,
            $request->due_date,
            (float) $request->amount,
            $items
        );

        $invoice = $action->execute($dto);

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Fatura başarıyla kesildi ve öğrenci borç hesabı oluşturuldu.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice = $this->repository->findById($invoice->id);
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();

        return view('admin.invoices.show', compact('invoice', 'paymentMethods'));
    }

    public function cancel(Invoice $invoice, CancelInvoice $action)
    {
        $action->execute($invoice);
        return redirect()->route('admin.invoices.index')->with('success', 'Fatura ve ilgili borç kaydı iptal edildi.');
    }

    public function dashboard()
    {
        $summary = $this->analyticsService->getSummary();
        $recentInvoices = Invoice::with(['student'])->orderBy('issue_date', 'desc')->take(10)->get();
        $recentPayments = \App\Models\Payment::with(['student', 'paymentMethod'])->orderBy('payment_date', 'desc')->take(10)->get();

        return view('admin.invoices.dashboard', compact('summary', 'recentInvoices', 'recentPayments'));
    }
}
