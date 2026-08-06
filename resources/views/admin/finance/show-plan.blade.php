@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-slate-800">Ödeme Planı Detayı: {{ $plan->title }}</h2>
        <a href="{{ route('admin.finance.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Geri
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Özet Kartı -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 py-2 border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Öğrenci Bilgisi</div>
                            <div class="h5 mb-0 font-weight-bold text-slate-800">{{ $plan->student->user->name ?? 'Bilinmiyor' }}</div>
                            <div class="mt-2">
                                <p class="mb-1">Toplam: {{ number_format($plan->total_amount, 2) }} {{ $plan->currency }}</p>
                                <p class="mb-1 text-success">İndirim: {{ number_format($plan->discount_amount, 2) }} {{ $plan->currency }}</p>
                                <p class="mb-0 text-danger font-weight-bold">Net Borç: {{ number_format($plan->net_amount, 2) }} {{ $plan->currency }}</p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-slate-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-success w-100" data-toggle="modal" data-target="#discountModal">İndirim Uygula</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taksitler -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Taksit Planı</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Son Ödeme</th>
                                    <th>Tutar</th>
                                    <th>Kalan</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plan->installments as $installment)
                                <tr>
                                    <td>{{ $installment->installment_no }}</td>
                                    <td>{{ $installment->due_date->format('d.m.Y') }}</td>
                                    <td>{{ number_format($installment->amount, 2) }}</td>
                                    <td><strong class="text-danger">{{ number_format($installment->remaining_amount, 2) }}</strong></td>
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
                                        @if($installment->remaining_amount > 0)
                                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#collectModal{{ $installment->id }}">Tahsil Et</button>
                                            
                                            <!-- Tahsilat Modal -->
                                            <div class="modal fade" id="collectModal{{ $installment->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tahsilat Al - Taksit {{ $installment->installment_no }}</h5>
                                                            <button class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <form action="{{ route('admin.installments.collect', $installment) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Ödenecek Tutar (Maks: {{ $installment->remaining_amount }})</label>
                                                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ $installment->remaining_amount }}" max="{{ $installment->remaining_amount }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Ödeme Yöntemi</label>
                                                                    <select name="payment_method" class="form-control" required>
                                                                        <option value="cash">Nakit</option>
                                                                        <option value="credit_card">Kredi Kartı</option>
                                                                        <option value="bank_transfer">Havale/EFT</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Referans No (Opsiyonel)</label>
                                                                    <input type="text" name="reference_no" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Notlar</label>
                                                                    <textarea name="notes" class="form-control"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-success">Kaydet</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted"><i class="fas fa-check"></i> Tamamlandı</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İndirim Modal -->
<div class="modal fade" id="discountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İndirim Uygula</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.finance.discount', $plan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Başlık</label>
                        <input type="text" name="title" class="form-control" placeholder="Örn: Kardeş İndirimi" required>
                    </div>
                    <div class="form-group">
                        <label>Tür</label>
                        <select name="type" class="form-control" required>
                            <option value="fixed">Sabit Tutar</option>
                            <option value="percentage">Yüzde (%)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Değer</label>
                        <input type="number" step="0.01" name="value" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Gerekçe</label>
                        <textarea name="reason" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Uygula</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
