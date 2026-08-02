@extends('layouts.admin')
@section('title', 'İndirim Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="İndirim Kampanyaları" description="Sistemdeki tüm indirim tanımlarını (Kardeş, Peşin vb.) yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.discounts.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni İndirim Ekle
            </x-admin.button>
        </x-slot>

        @if($discounts->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İndirim Adı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kod</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tür</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Değer</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider text-right">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($discounts as $discount)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">
                                {{ $discount->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400 font-mono">
                                {{ $discount->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                <span class="px-2.5 py-1 text-xs font-medium bg-neutral-100 dark:bg-neutral-800 rounded-lg">
                                    {{ $discount->type === 'percentage' ? 'Yüzdelik (%)' : 'Sabit Tutar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                                {{ $discount->type === 'percentage' ? $discount->value . '%' : number_format($discount->value, 2) . ' TL' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full {{ $discount->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-500' : 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                    @if($discount->is_active)
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    @endif
                                    {{ $discount->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 text-right">
                                <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Düzenle</a>
                                <x-admin.delete-modal :action="route('admin.discounts.destroy', $discount->id)" title="İndirimi Sil" message="'{{ $discount->name }}' isimli indirimi silmek istediğinize emin misiniz? Bu işlem geri alınamaz." />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                <x-slot name="pagination">
                    {{ $discounts->links() }}
                </x-slot>
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="İndirim Bulunamadı" 
                description="Sistemde henüz bir indirim kampanyası tanımlanmamış. Hemen yeni bir indirim ekleyebilirsiniz."
                actionText="Yeni İndirim Ekle"
                actionRoute="{{ route('admin.discounts.create') }}"
            />
        @endif
    </x-admin.crud.index-layout>
@endsection
