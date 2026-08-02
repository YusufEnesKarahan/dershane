@extends('layouts.admin')
@section('title', 'İade Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci İadeleri" description="İptal edilen kayıtlar ve fazla tahsilatlar için iade süreçlerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.refunds.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni İade Oluştur
            </x-admin.button>
        </x-slot>

        @if($refunds->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Ödeme İzni</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İade Tutarı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İade Tarihi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider text-right">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($refunds as $refund)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">
                                {{ optional(optional($refund->payment)->student)->first_name }} {{ optional(optional($refund->payment)->student)->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                {{ optional($refund->payment)->payment_number ?? 'Ödeme #' . $refund->payment_id }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-rose-600 dark:text-rose-500">
                                {{ number_format($refund->amount, 2) }} TL
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400 font-medium">
                                {{ \Carbon\Carbon::parse($refund->refund_date)->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 text-right">
                                <a href="{{ route('admin.refunds.edit', $refund->id) }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Düzenle</a>
                                <x-admin.delete-modal :action="route('admin.refunds.destroy', $refund->id)" title="İadeyi Sil" message="'{{ number_format($refund->amount, 2) }} TL' tutarındaki iadeyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz." />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                <x-slot name="pagination">
                    {{ $refunds->links() }}
                </x-slot>
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="İade Bulunamadı" 
                description="Sistemde henüz bir iade işlemi bulunmuyor."
                actionText="Yeni İade Oluştur"
                actionRoute="{{ route('admin.refunds.create') }}"
            />
        @endif
    </x-admin.crud.index-layout>
@endsection
