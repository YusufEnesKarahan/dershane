@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-slate-800">Tüm Taksitler</h2>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Öğrenci</th>
                            <th>Plan</th>
                            <th>No</th>
                            <th>Son Ödeme</th>
                            <th>Tutar</th>
                            <th>Kalan</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($installments as $installment)
                        <tr>
                            <td>{{ $installment->paymentPlan->student->user->name ?? 'Bilinmiyor' }}</td>
                            <td>{{ $installment->paymentPlan->title }}</td>
                            <td>{{ $installment->installment_no }}</td>
                            <td>{{ $installment->due_date->format('d.m.Y') }}</td>
                            <td>{{ number_format($installment->amount, 2) }}</td>
                            <td><strong>{{ number_format($installment->remaining_amount, 2) }}</strong></td>
                            <td>
                                @if($installment->status == 'pending')
                                    <span class="badge badge-warning">Bekliyor</span>
                                @elseif($installment->status == 'paid')
                                    <span class="badge badge-success">Ödendi</span>
                                @elseif($installment->status == 'partial')
                                    <span class="badge badge-info">Kısmi</span>
                                @elseif($installment->status == 'overdue')
                                    <span class="badge badge-danger">Gecikmiş</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.finance.show', $installment->paymentPlan) }}" class="btn btn-sm btn-info">Detay</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
