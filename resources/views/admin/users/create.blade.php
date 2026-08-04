@extends('layouts.admin')

@section('title', 'Yeni Kullanıcı Ekle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Yeni Kullanıcı Tanımla</h1>
            <p class="text-xs text-slate-500 mt-1">Sisteme yeni bir yönetici, öğretmen veya personel hesabı ekleyin.</p>
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
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ad Soyad</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ahmet Yılmaz" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-Posta Adresi</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="ahmet@dershane.com" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05550000000" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Şifre</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hesap Durumu</label>
                    <select name="status" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="ACTIVE" {{ old('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="PASSIVE" {{ old('status') === 'PASSIVE' ? 'selected' : '' }}>Pasif</option>
                        <option value="SUSPENDED" {{ old('status') === 'SUSPENDED' ? 'selected' : '' }}>Askıda</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bağlı Şube</label>
                    <select name="branch_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tüm Şubeler (Merkez)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Profil Resmi (Avatar)</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kullanıcı Rolleri</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ is_array(old('roles')) && in_array($role->id, old('roles')) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-all">İptal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">Kullanıcı Oluştur</button>
            </div>
        </form>
    </div>
</div>
@endsection
