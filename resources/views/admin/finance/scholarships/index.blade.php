@extends('layouts.admin')
@section('title', 'Burs Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Bursları" description="Öğrencilere tanımlanmış burs oranlarını ve durumlarını yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.scholarships.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Burs Ekle
            </x-admin.button>
        </x-slot>

        @if($scholarships->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Burs Başlığı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Oran</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider text-right">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($scholarships as $scholarship)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                {{ optional($scholarship->student)->first_name }} {{ optional($scholarship->student)->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                                {{ $scholarship->title }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                                %{{ $scholarship->percentage }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full {{ $scholarship->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-500' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400' }}">
                                    @if($scholarship->is_active)
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    @endif
                                    {{ $scholarship->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 text-right">
                                <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Düzenle</a>
                                <x-admin.delete-modal :action="route('admin.scholarships.destroy', $scholarship->id)" title="Bursu Sil" message="'{{ $scholarship->title }}' bursunu silmek istediğinize emin misiniz? Bu işlem geri alınamaz." />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                <x-slot name="pagination">
                    {{ $scholarships->links() }}
                </x-slot>
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="Burs Bulunamadı" 
                description="Sistemde henüz bir burs kaydı bulunmuyor. Hemen yeni bir burs ekleyebilirsiniz."
                actionText="Yeni Burs Ekle"
                actionRoute="{{ route('admin.scholarships.create') }}"
            />
        @endif
    </x-admin.crud.index-layout>
@endsection
