@extends('layouts.admin')
@section('title', 'Ödeme Planları')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Ödeme Planları" description="Öğrencilerin taksitlendirme ve ödeme takvimlerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.payment-plans.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Ödeme Planı
            </x-admin.button>
        </x-slot>

        @if($plans->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Taksit Sayısı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Taksit Tutarı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Başlangıç Tarihi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider text-right">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($plans as $plan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                {{ optional($plan->student)->first_name }} {{ optional($plan->student)->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                                {{ $plan->total_installments }} Taksit
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                                {{ number_format($plan->installment_amount, 2) }} TL
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 text-right">
                                <a href="{{ route('admin.payment-plans.edit', $plan->id) }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Düzenle</a>
                                <x-admin.delete-modal :action="route('admin.payment-plans.destroy', $plan->id)" title="Ödeme Planını Sil" message="'{{ optional($plan->student)->first_name }} {{ optional($plan->student)->last_name }}' isimli öğrencinin ödeme planını silmek istediğinize emin misiniz? Bu işlem geri alınamaz." />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                <x-slot name="pagination">
                    {{ $plans->links() }}
                </x-slot>
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="Ödeme Planı Bulunamadı" 
                description="Sistemde henüz bir ödeme planı oluşturulmamış. Hemen yeni bir plan ekleyebilirsiniz."
                actionText="Yeni Ödeme Planı"
                actionRoute="{{ route('admin.payment-plans.create') }}"
            />
        @endif
    </x-admin.crud.index-layout>
@endsection
