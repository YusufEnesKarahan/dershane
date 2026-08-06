@extends('layouts.admin')
@section('title', 'İzin Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="İzin İstekleri & Talepler" description="Personellerin izin haklarını, yıllık, mazeret ve hastalık izin başvurularını onaylayın veya düzenleyin.">
        <x-slot name="actions">
            <button onclick="toggleModal('leave-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-colors shadow-lg shadow-blue-900/20 border border-blue-500/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                İzin Talebi Ekle
            </button>
        </x-slot>

        <!-- İzin İstekleri Tablosu -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-0 flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Personel</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">İzin Türü</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Tarih Aralığı</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Gün Sayısı</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Durum</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30 w-32">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-white dark:bg-slate-900">
                            @forelse($requests as $req)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-100 dark:border-slate-800/50 last:border-0 group">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $req->employee->first_name }} {{ $req->employee->last_name }}</div>
                                        <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg> {{ $req->employee->department->name ?? 'Yok' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">{{ $req->leaveType->name ?? 'Yok' }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $req->start_date }} - {{ $req->end_date }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-sans line-clamp-1" title="{{ $req->reason }}">Sebep: {{ $req->reason }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-sm font-bold border border-blue-200/50 dark:border-blue-500/20">{{ $req->days }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($req->status === 'Approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Onaylandı
                                            </span>
                                        @elseif($req->status === 'Rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Reddedildi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                Bekliyor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($req->status === 'Pending')
                                                <form method="POST" action="{{ route('admin.leaves.approve', $req->id) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-green-50 hover:bg-green-100 dark:bg-green-500/10 dark:hover:bg-green-500/20 text-green-600 dark:text-green-400 text-[10px] font-bold rounded-lg transition-colors border border-green-200 dark:border-green-500/30">Onayla</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.leaves.reject', $req->id) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-[10px] font-bold rounded-lg transition-colors border border-red-200 dark:border-red-500/30">Reddet</button>
                                                </form>
                                            @else
                                                <span class="text-[10px] font-medium text-slate-400 flex items-center justify-end gap-1"><svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Tamamlandı <br> (Onay: {{ $req->approver->name ?? '-' }})</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-admin.empty-state
                                            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            title="İzin Talebi Bulunamadı"
                                            description="Sistemde henüz bir izin başvurusu bulunmuyor."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>

        <!-- İzin Ekleme Modal -->
        <div id="leave-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Yeni İzin Talebi Ekle</h3>
                    <button onclick="toggleModal('leave-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.leaves.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">İzin Türü</label>
                        <select name="leave_type_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} (Maks: {{ $type->max_days }} Gün)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-600 dark:text-slate-400">Başlangıç Tarihi</label>
                            <input type="date" name="start_date" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-600 dark:text-slate-400">Bitiş Tarihi</label>
                            <input type="date" name="end_date" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Açıklama / Sebep</label>
                        <textarea name="reason" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('leave-modal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
