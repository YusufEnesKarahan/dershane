@extends('layouts.admin')

@section('title', 'Ön Kayıt Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-user-clock text-indigo-600"></i> Ön Kayıt Yönetimi
            </h1>
            <p class="text-sm text-neutral-500 mt-1">Aday öğrenci görüşmelerini takip edin ve tek tuşla kesin kayıda dönüştürün.</p>
        </div>
        <a href="{{ route('admin.pre-registrations.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Yeni Ön Kayıt Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filtreler -->
    <form action="{{ route('admin.pre-registrations.index') }}" method="GET" class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Öğrenci adı, telefon veya e-posta ile ara..." class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="Yeni" {{ request('status') === 'Yeni' ? 'selected' : '' }}>Yeni</option>
                    <option value="Arandı" {{ request('status') === 'Arandı' ? 'selected' : '' }}>Arandı</option>
                    <option value="Randevu" {{ request('status') === 'Randevu' ? 'selected' : '' }}>Randevu</option>
                    <option value="Kayıt Oldu" {{ request('status') === 'Kayıt Oldu' ? 'selected' : '' }}>Kayıt Oldu</option>
                    <option value="İptal" {{ request('status') === 'İptal' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>

            <div>
                <select name="source" onchange="this.form.submit()" class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Tüm Kaynaklar</option>
                    <option value="Instagram">Instagram</option>
                    <option value="Google">Google</option>
                    <option value="Referans">Referans</option>
                    <option value="Web">Web</option>
                    <option value="Telefon">Telefon</option>
                    <option value="Diğer">Diğer</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Tablo -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-800/50 border-b border-neutral-100 dark:border-neutral-800 text-xs font-bold uppercase text-neutral-500">
                        <th class="px-6 py-4">Aday Öğrenci</th>
                        <th class="px-6 py-4">İletişim & Sınıf</th>
                        <th class="px-6 py-4">Kaynak & Program</th>
                        <th class="px-6 py-4">Temsilci / Not</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800 text-sm">
                    @forelse($preRegistrations as $item)
                        @php
                            $statusBadge = match($item->status) {
                                'Yeni' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                'Arandı' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'Randevu' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                'Kayıt Oldu' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'İptal' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                default => 'bg-neutral-100 text-neutral-700',
                            };
                        @endphp
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-neutral-900 dark:text-white">{{ $item->student_name }}</div>
                                <div class="text-xs text-neutral-400">Tarih: {{ $item->created_at->format('d.m.Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-neutral-800 dark:text-neutral-200"><i class="fas fa-phone mr-1 text-xs text-neutral-400"></i>{{ $item->phone }}</div>
                                <div class="text-xs text-neutral-500">{{ $item->classroom_name ?: 'Sınıf Belirtilmedi' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                                    {{ $item->source }}
                                </span>
                                <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $item->interested_program ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-neutral-800 dark:text-neutral-200">{{ $item->assignedUser?->name ?? 'Atanmadı' }}</div>
                                @if($item->notes)
                                    <div class="text-xs text-neutral-400 truncate max-w-xs" title="{{ $item->notes }}">{{ $item->notes }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusBadge }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($item->status !== 'Kayıt Oldu')
                                        <a href="{{ route('admin.pre-registrations.convert', $item->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i> Kesin Kayıda Dönüştür
                                        </a>
                                    @else
                                        <span class="text-xs text-emerald-600 font-bold"><i class="fas fa-user-check mr-1"></i> Kaydedildi</span>
                                    @endif

                                    <a href="{{ route('admin.pre-registrations.edit', $item->id) }}" class="p-1.5 text-neutral-400 hover:text-indigo-600" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.pre-registrations.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Ön kaydı silmek istiyor musunuz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-neutral-400 hover:text-rose-600" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-neutral-500 italic">
                                Kayıtlı ön kayıt bulunamadı. Yeni bir aday öğrenci ekleyebilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($preRegistrations->hasPages())
            <div class="p-4 border-t border-neutral-100 dark:border-neutral-800">
                {{ $preRegistrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
