@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-slate-800">Çocuklarımın Ödemeleri</h2>

    @forelse($students as $student)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ $student->user->name ?? 'Bilinmiyor' }} ({{ $student->student_number }})</h6>
        </div>
        <div class="card-body">
            @php $summary = $summaries[$student->id]; @endphp
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-danger mb-1">{{ number_format($summary['total_debt'], 2) }} ₺</h5>
                            <small class="text-muted text-uppercase">Toplam Borç</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-success mb-1">{{ number_format($summary['net_paid'], 2) }} ₺</h5>
                            <small class="text-muted text-uppercase">Ödenen</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-warning mb-1">{{ number_format($summary['remaining_debt'], 2) }} ₺</h5>
                            <small class="text-muted text-uppercase">Kalan Borç</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-left-danger">
                        <div class="card-body text-center">
                            <h5 class="text-danger mb-1">{{ number_format($summary['overdue_amount'], 2) }} ₺</h5>
                            <small class="text-muted text-uppercase">Gecikmiş</small>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="font-weight-bold">Yaklaşan Taksitler</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Taksit No</th>
                            <th>Son Ödeme</th>
                            <th>Tutar</th>
                            <th>Kalan</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installments[$student->id] as $installment)
                            <tr>
                                <td>{{ $installment->installment_no }}</td>
                                <td>{{ $installment->due_date->format('d.m.Y') }}</td>
                                <td>{{ number_format($installment->amount, 2) }} ₺</td>
                                <td><strong class="text-danger">{{ number_format($installment->remaining_amount, 2) }} ₺</strong></td>
                                <td>
                                    @if($installment->status == 'pending')
                                        <span class="badge badge-warning">Bekliyor</span>
                                    @elseif($installment->status == 'overdue')
                                        <span class="badge badge-danger">Gecikmiş</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Bekleyen taksit yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
        <div class="alert alert-info">Kayıtlı öğrenci bulunamadı.</div>
    @endforelse
</div>
@endsection
