<?php

namespace App\Core\Repositories;

use App\Models\Invoice;
use App\Core\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Invoice::with(['student.branch', 'items', 'payments'])
            ->orderBy('issue_date', 'desc');

        if (!empty($filters['search'])) {
            $query->where('invoice_number', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Invoice
    {
        return Invoice::with(['student.branch', 'items', 'payments.paymentMethod'])->find($id);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }
}
