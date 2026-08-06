@extends('layouts.admin')
@section('title', 'Öğrenci Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Listesi" description="Dershane öğrencilerinizin kayıtlarını, şubelerini ve akademik yaşam döngülerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.create') }}" variant="primary" icon="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                Yeni Öğrenci Kaydı
            </x-admin.button>
        </x-slot>

        <div class="mb-6 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Numara veya Ad Soyad ile ara..." class="pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 w-full md:w-72">
                </div>
                <select name="status" class="px-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Pasif</option>
                    <option value="Graduated" {{ request('status') === 'Graduated' ? 'selected' : '' }}>Mezun</option>
                    <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Ayrıldı</option>
                </select>
                <button type="submit" class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    Filtrele
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm">
                <span>✓</span>
                {{ session('success') }}
            </div>
        @endif

        @if($students->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Numara</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ad Soyad</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Şube</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sınıf</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($students as $student)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-blue-600 dark:text-blue-400">
                                {{ $student->student_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="text-sm font-bold text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $student->full_name }}
                                        </a>
                                        @if($student->primaryGuardian)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                <span>🛡️</span> {{ $student->primaryGuardian->guardian_name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                {{ $student->branch?->name ?? 'Ana Şube' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300 font-semibold">
                                {{ $student->classroom ? $student->classroom->name : 'Sınıf Atanmadı' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $student->status === 'Active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                    {{ $student->status === 'Inactive' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                    {{ $student->status === 'Graduated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}
                                    {{ $student->status === 'Suspended' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : '' }}
                                ">
                                    {{ $student->status === 'Active' ? 'Aktif' : ($student->status === 'Graduated' ? 'Mezun' : ($student->status === 'Suspended' ? 'Ayrıldı' : 'Pasif')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 rounded-lg transition-colors" title="Profili Görüntüle">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->id) }}" class="p-2 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 rounded-lg transition-colors" title="Düzenle">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu öğrenciyi silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition-colors" title="Sil">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                @if(method_exists($students, 'links'))
                    <x-slot name="pagination">
                        {{ $students->links() }}
                    </x-slot>
                @endif
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="Öğrenci Bulunamadı" 
                description="Sistemde henüz bir öğrenci kaydı bulunmuyor. Yeni bir öğrenci kaydı oluşturarak başlayabilirsiniz."
                actionText="Yeni Öğrenci Kaydı"
                actionRoute="{{ route('admin.students.create') }}"
                icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
            />
        @endif
    </x-admin.crud.index-layout>
@endsection
