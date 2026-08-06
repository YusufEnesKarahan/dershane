@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-slate-800">Tahsilatlar</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Öğrenci</th>
                            <th>Tarih</th>
                            <th>Tutar</th>
                            <th>Yöntem</th>
                            <th>Kasiyer</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->student->user->name ?? 'Bilinmiyor' }}</td>
                            <td>{{ $payment->payment_date->format('d.m.Y H:i') }}</td>
                            <td><strong class="text-success">{{ number_format($payment->amount, 2) }}</strong></td>
                            <td>
                                @if($payment->payment_method == 'cash') Nakit
                                @elseif($payment->payment_method == 'credit_card') Kredi Kartı
                                @else Havale/EFT
                                @endif
                            </td>
                            <td>{{ $payment->receiver->name ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#refundModal{{ $payment->id }}">İade</button>
                                
                                <!-- İade Modal -->
                                <div class="modal fade" id="refundModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">İade İşlemi</h5>
                                                <button class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>İade Edilecek Tutar (Maks: {{ $payment->amount }})</label>
                                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payment->amount }}" max="{{ $payment->amount }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>İade Gerekçesi</label>
                                                        <textarea name="reason" class="form-control" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-danger">İadeyi Onayla</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
