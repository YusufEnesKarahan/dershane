@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-slate-800">Yeni Ödeme Planı</h2>
        <a href="{{ route('admin.finance.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Geri
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.finance.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Öğrenci</label>
                        <select name="student_id" class="form-control" required>
                            <option value="">Öğrenci Seçin</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name ?? 'Bilinmiyor' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Akademik Dönem</label>
                        <select name="academic_term_id" class="form-control" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Plan Başlığı</label>
                        <input type="text" name="title" class="form-control" placeholder="Örn: 2026-2027 Eğitim Ücreti" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Toplam Tutar (TRY)</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>İndirim (Opsiyonel)</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Taksit Sayısı</label>
                        <input type="number" name="installment_count" class="form-control" value="1" min="1" max="24" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Oluştur</button>
            </form>
        </div>
    </div>
</div>
@endsection
