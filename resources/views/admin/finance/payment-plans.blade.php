@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Ödeme Planları</h2>
        <a href="{{ route('admin.finance.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Yeni Plan Oluştur
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Öğrenci</th>
                            <th>Başlık</th>
                            <th>Tutar</th>
                            <th>İndirim</th>
                            <th>Net Tutar</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        <tr>
                            <td>{{ $plan->student->user->name ?? 'Bilinmiyor' }}</td>
                            <td>{{ $plan->title }}</td>
                            <td>{{ number_format($plan->total_amount, 2) }} {{ $plan->currency }}</td>
                            <td>{{ number_format($plan->discount_amount, 2) }} {{ $plan->currency }}</td>
                            <td><strong>{{ number_format($plan->net_amount, 2) }} {{ $plan->currency }}</strong></td>
                            <td>
                                @if($plan->status == 'active')
                                    <span class="badge badge-primary">Aktif</span>
                                @elseif($plan->status == 'completed')
                                    <span class="badge badge-success">Tamamlandı</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($plan->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.finance.show', $plan) }}" class="btn btn-sm btn-info">Detay</a>
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
