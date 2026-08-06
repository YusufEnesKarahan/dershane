@extends('layouts.admin')

@section('title', 'Haftalık Çalışma Programı Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-calendar-alt text-blue-600"></i> Haftalık Çalışma Programları
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Öğrenciler için ödev, çalışma programı, kaynak kitap ve hedef süre takibini yönetin.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.homeworks.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Program Hazırla
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Program Listesi -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase text-slate-500 tracking-wider">
                        <th class="px-6 py-4">Hafta & Başlık</th>
                        <th class="px-6 py-4">Ders & Sınıf</th>
                        <th class="px-6 py-4">Kaynak & Konu</th>
                        <th class="px-6 py-4">Öncelik & Süre</th>
                        <th class="px-6 py-4">İlerleme Oranı</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($homeworks as $hw)
                        @php
                            $progress = $hw->progress_percentage;
                            $priorityColors = [
                                'low' => 'bg-slate-100 text-slate-700',
                                'medium' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                'high' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'urgent' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                            ];
                            $priorityLabels = [
                                'low' => 'Düşük',
                                'medium' => 'Orta',
                                'high' => 'Yüksek',
                                'urgent' => 'Acil',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-xs">
                                        H{{ $hw->week_number ?? '1' }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $hw->title }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                                            Bitiş: {{ $hw->due_date ? $hw->due_date->format('d.m.Y H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $hw->course?->name ?? 'Ders' }}
                                    </span>
                                    <div class="text-xs text-slate-500">
                                        Sınıf: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $hw->classroom?->name ?? 'Genel' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $hw->subject ?: ($hw->description ? Str::limit($hw->description, 25) : 'Konu Tanımsız') }}</div>
                                    @if($hw->source_book)
                                        <div class="text-slate-500 mt-0.5"><i class="fas fa-book text-slate-400 mr-1"></i> {{ $hw->source_book }} {{ $hw->page_range ? '(S. '.$hw->page_range.')' : '' }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $priorityColors[$hw->priority ?? 'medium'] }}">
                                        {{ $priorityLabels[$hw->priority ?? 'medium'] }}
                                    </span>
                                    <div class="text-xs text-slate-500 font-mono">
                                        <i class="far fa-clock mr-1"></i> {{ $hw->estimated_minutes ?? 45 }} Dk
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-32 space-y-1">
                                    <div class="flex justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                                        <span>Tamamlanan</span>
                                        <span>%{{ $progress }}</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($hw->status === 'draft')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        Taslak
                                    </span>
                                @elseif($hw->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        Yayında
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        Tamamlandı
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.homeworks.edit', $hw->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($hw->status === 'draft')
                                        <form action="{{ route('admin.homeworks.publish', $hw->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Yayınla">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.homeworks.destroy', $hw->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Çalışma programını silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 italic">
                                Kayıtlı haftalık çalışma programı bulunamadı. Yeni bir program ekleyerek başlayabilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
