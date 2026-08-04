@extends('layouts.admin')

@section('title', 'Sistem Bildirimleri')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bildirim & İletişim Merkezi</h1>
        <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Sol Panel: Bildirim Gönder Formu -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yeni Bildirim Gönder</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="receiver_id">Alıcı Kullanıcı <span class="text-danger">*</span></label>
                            <select name="receiver_id" id="receiver_id" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="receiver_type">Alıcı Rolü</label>
                            <select name="receiver_type" id="receiver_type" class="form-control">
                                <option value="admin">Yönetici</option>
                                <option value="teacher">Öğretmen</option>
                                <option value="student">Öğrenci</option>
                                <option value="parent">Veli</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">Bildirim Türü <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="system">Sistem</option>
                                <option value="homework">Ödev</option>
                                <option value="attendance">Devamsızlık</option>
                                <option value="exam">Sınav</option>
                                <option value="guidance">Rehberlik</option>
                                <option value="announcement">Duyuru</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="title">Bildirim Başlığı <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Örn: Sınav Sonucu Açıklandı" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="message">Mesaj İçeriği <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="4" class="form-control" placeholder="Bildirim detay metni..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-paper-plane"></i> Bildirimi Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sağ Panel: Bildirim Listesi -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bildirim Gelen Kutusu & Loglar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Türü</th>
                                    <th>Başlık & Mesaj</th>
                                    <th>Alıcı</th>
                                    <th>Tarih</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notif)
                                <tr>
                                    <td>
                                        <span class="badge badge-info">{{ strtoupper($notif->type ?? 'SYSTEM') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $notif->title }}</strong>
                                        <div class="small text-muted">{{ $notif->message ?? $notif->content }}</div>
                                    </td>
                                    <td>{{ $notif->receiver->name ?? $notif->user->name ?? 'Kullanıcı' }}</td>
                                    <td class="small">{{ $notif->created_at ? $notif->created_at->format('d.m.Y H:i') : '-' }}</td>
                                    <td>
                                        @if($notif->isRead())
                                            <span class="badge badge-success">Okundu</span>
                                        @else
                                            <form action="{{ route('admin.notifications.read', $notif) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-primary">Okundu Yap</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Bildirim bulunmuyor.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
