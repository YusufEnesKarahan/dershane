@extends('layouts.admin')
@section('title', 'Öğrenci Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Listesi" description="Dershane öğrencilerinizin kayıtlarını, şubelerini ve akademik yaşam döngülerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.analytics') }}" variant="secondary" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Öğrenci Analitiği
            </x-admin.button>
            <x-admin.button href="{{ route('admin.students.create') }}" variant="primary" icon="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                Yeni Öğrenci Kaydı
            </x-admin.button>
        </x-slot>

        @if($students->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Öğrenci No</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Ad Soyad</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Şube</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Sınıf / Derslik</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Veli İletişim</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider text-right">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($students as $student)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">
                                {{ $student->student_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-neutral-900 dark:text-white">
                                    {{ $student->full_name }}
                                </div>
                                @if($student->identity_number)
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 font-mono mt-0.5">TC: {{ $student->identity_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400 font-medium">
                                {{ $student->branch ? $student->branch->name : 'Genel' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400 font-medium">
                                {{ $student->classroom ? $student->classroom->name : 'Atanmadı' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($student->primaryGuardian)
                                    <div class="font-medium text-neutral-700 dark:text-neutral-300">{{ $student->primaryGuardian->guardian_name }} <span class="text-neutral-400">({{ $student->primaryGuardian->relation }})</span></div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5 font-mono">{{ $student->primaryGuardian->phone }}</div>
                                @else
                                    <span class="text-neutral-400 italic">Tanımsız</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full {{ $student->status === 'Active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-500' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500' }}">
                                    @if($student->status === 'Active')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    @endif
                                    {{ $student->status === 'Active' ? 'Aktif Öğrenci' : $student->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 text-right">
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Düzenle</a>
                                <x-admin.delete-modal :action="route('admin.students.destroy', $student->id)" title="Öğrenciyi Sil" message="'{{ $student->full_name }}' isimli öğrenciyi silmek istediğinize emin misiniz? Öğrenciye ait akademik, finansal ve CRM geçmişi de etkilenebilir. Bu işlem geri alınamaz." />
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
