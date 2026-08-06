@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest bg-blue-50 text-blue-700 rounded-full border border-blue-200">Kullanıcı & Rol Yönetimi</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Sistem Kullanıcıları</h1>
            <p class="text-xs text-slate-500 mt-0.5">Dershane çalışanları, yöneticiler, öğretmenler ve sisteme erişimi olan tüm kullanıcıları yönetin.</p>
        </div>
        <div>
            @permission('users.create')
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Yeni Kullanıcı Ekle
                </a>
            @endpermission
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm flex items-center justify-between shadow-sm">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad, e-posta veya telefon ile ara..." class="w-full text-xs rounded-xl border-slate-200 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select name="role" class="w-full text-xs rounded-xl border-slate-200 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tüm Roller</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                    <option value="PASSIVE" {{ request('status') == 'PASSIVE' ? 'selected' : '' }}>Pasif</option>
                    <option value="SUSPENDED" {{ request('status') == 'SUSPENDED' ? 'selected' : '' }}>Askıda</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-xl transition-all">
                    Filtrele
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl font-medium">Temizle</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('admin.users.bulk') }}">
            @csrf
            
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <select name="bulk_action" class="text-xs rounded-xl border-slate-200 font-medium">
                        <option value="">Seçilenlere Uygula...</option>
                        <option value="status_active">Aktif Yap</option>
                        <option value="status_passive">Pasif Yap</option>
                        <option value="delete">Sil</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-xl">Uygula</button>
                </div>
                <span class="text-xs text-slate-500 font-medium">Toplam {{ $users->total() }} Kullanıcı</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="p-4 w-10"><input type="checkbox" onclick="document.querySelectorAll('.user-cb').forEach(c => c.checked = this.checked)" class="rounded border-slate-300 text-blue-600"></th>
                            <th class="p-4">Kullanıcı</th>
                            <th class="p-4">İletişim</th>
                            <th class="p-4">Rol(ler)</th>
                            <th class="p-4">Şube</th>
                            <th class="p-4">Durum</th>
                            <th class="p-4">Son Giriş</th>
                            <th class="p-4 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-4">
                                    <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-cb rounded border-slate-300 text-blue-600">
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->getAvatarUrl() }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                            <span class="text-[11px] text-slate-500">ID: #{{ $user->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="block font-medium text-slate-800">{{ $user->email }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $user->phone ?? '-' }}</span>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->roles as $r)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                                {{ $r->name }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400 italic">Rol yok</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="font-medium text-slate-800">{{ $user->branch->name ?? 'Tüm Şubeler' }}</span>
                                </td>
                                <td class="p-4">
                                    @php $statusVal = is_object($user->status) ? $user->status->value : $user->status; @endphp
                                    @if($statusVal === 'ACTIVE')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                    @elseif($statusVal === 'PASSIVE')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">Pasif</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">Askıda</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-500">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Henüz girmedi' }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @permission('users.update')
                                            <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        @endpermission

                                        @permission('users.delete')
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Sil">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endpermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400">
                                    Kullanıcı bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        </form>
    </div>
</div>
@endsection
