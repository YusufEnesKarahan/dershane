@extends('layouts.admin')
@section('title', 'Öğrenci Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Listesi" description="Dershane öğrencilerinizin kayıtlarını, şubelerini ve akademik yaşam döngülerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.create') }}" variant="primary" icon="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                Yeni Öğrenci Kaydı
            </x-admin.button>
        </x-slot>

        <div class="mb-6 bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-neutral-400"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Numara veya Ad Soyad ile ara..." class="pl-10 pr-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500 w-full md:w-72">
                </div>
                <select name="status" class="px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Pasif</option>
                    <option value="Graduated" {{ request('status') === 'Graduated' ? 'selected' : '' }}>Mezun</option>
                    <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Ayrıldı</option>
                </select>
                <button type="submit" class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-100 transition-colors">
                    Filtrele
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-sm flex items-center gap-2 font-bold">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($students->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Numara</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Ad Soyad</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Şube</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Sınıf</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($students as $student)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $student->student_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="text-sm font-bold text-neutral-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            {{ $student->full_name }}
                                        </a>
                                        @if($student->primaryGuardian)
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                                <i class="fas fa-user-shield text-[10px] mr-1 text-emerald-500"></i> {{ $student->primaryGuardian->guardian_name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                {{ $student->branch?->name ?? 'Ana Şube' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300 font-semibold">
                                {{ $student->classroom ? $student->classroom->name : 'Sınıf Atanmadı' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $student->status === 'Active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                    {{ $student->status === 'Inactive' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                    {{ $student->status === 'Graduated' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                    {{ $student->status === 'Suspended' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : '' }}
                                ">
                                    {{ $student->status === 'Active' ? 'Aktif' : ($student->status === 'Graduated' ? 'Mezun' : ($student->status === 'Suspended' ? 'Ayrıldı' : 'Pasif')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-colors tooltip" data-tip="Profili Görüntüle">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->id) }}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors tooltip" data-tip="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu öğrenciyi silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors tooltip" data-tip="Sil">
                                            <i class="fas fa-trash"></i>
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
