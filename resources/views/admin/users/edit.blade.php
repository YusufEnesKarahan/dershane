@extends('layouts.admin')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kullanıcı Düzenle</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $user->name }} hesabı ve yetkilerini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-xl transition-all">
            ← Geri Dön
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs space-y-1">
            <span class="font-bold block mb-1">Lütfen aşağıdaki hataları düzeltin:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2 flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <img src="{{ $user->getAvatarUrl() }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $user->email }} • Kayıt: {{ $user->created_at ? $user->created_at->format('d.m.Y') : '-' }}</p>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ad Soyad</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-Posta Adresi</label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hesap Durumu</label>
                    @php $currStatus = is_object($user->status) ? $user->status->value : $user->status; @endphp
                    <select name="status" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="ACTIVE" {{ old('status', $currStatus) === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="PASSIVE" {{ old('status', $currStatus) === 'PASSIVE' ? 'selected' : '' }}>Pasif</option>
                        <option value="SUSPENDED" {{ old('status', $currStatus) === 'SUSPENDED' ? 'selected' : '' }}>Askıda</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bağlı Şube</label>
                    <select name="branch_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm Şubeler (Merkez)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $user->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Profil Resmi (Avatar)</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kullanıcı Rolleri</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        @php $userRoleIds = $user->roles->pluck('id')->toArray(); @endphp
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-all">İptal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">Değişiklikleri Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection
