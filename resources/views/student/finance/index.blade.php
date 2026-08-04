@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Ödemelerim</h2>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Toplam Borç</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_debt'], 2) }} ₺</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ödenen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['net_paid'], 2) }} ₺</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kalan Borç</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['remaining_debt'], 2) }} ₺</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Gecikmiş</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['overdue_amount'], 2) }} ₺</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Installments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yaklaşan/Geciken Taksitler</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingInstallments as $installment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Taksit {{ $installment->installment_no }}</h6>
                                    <small class="text-muted">Son Ödeme: {{ $installment->due_date->format('d.m.Y') }}</small>
                                </div>
                                <span class="badge {{ $installment->status == 'overdue' ? 'badge-danger' : 'badge-warning' }} badge-pill">
                                    {{ number_format($installment->remaining_amount, 2) }} ₺
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Bekleyen taksit bulunmuyor.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Payments History -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Son Ödemeler</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($payments->take(5) as $payment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $payment->payment_date->format('d.m.Y') }}</h6>
                                    <small class="text-muted">Yöntem: {{ ucfirst($payment->payment_method) }}</small>
                                </div>
                                <span class="badge badge-success badge-pill">
                                    {{ number_format($payment->amount, 2) }} ₺
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Ödeme geçmişi bulunmuyor.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
