@extends('layouts.admin')
@section('title', 'Mesaj Şablonları')
@section('content')
    <x-admin.crud.index-layout title="Mesaj & Bildirim Şablonları" description="Sistem tarafından otomatik gönderilen SMS, E-Posta veya sistem bildirim metinlerini düzenleyin.">
        <form action="{{ route('admin.notifications.templates.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-6 rounded-2xl shadow-premium-sm mb-6">@csrf
            <input name="name" required placeholder="Şablon adı" class="rounded-lg border-neutral-200"><input name="slug" placeholder="şablon-slug" class="rounded-lg border-neutral-200"><input name="title_template" required placeholder="Başlık ({{name}} destekler)" class="rounded-lg border-neutral-200"><select name="channel" class="rounded-lg border-neutral-200"><option value="panel">Panel</option><option value="email">E-posta</option><option value="sms">SMS</option></select><textarea name="body_template" required placeholder="Mesaj gövdesi" class="md:col-span-2 rounded-lg border-neutral-200"></textarea><button class="w-fit px-4 py-2 rounded-xl bg-primary text-white text-sm">Şablon ekle</button>
        </form>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kod</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Başlık</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kanal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($templates as $tpl)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $tpl->code }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-neutral-800">
                                {{ $tpl->title }}
                                <div class="text-[10px] text-neutral-400 font-mono mt-1">{{ $tpl->body }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold text-primary">{{ $tpl->channel }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $tpl->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tpl->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz tanımlı şablon bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </x-admin.crud.index-layout>
@endsection
