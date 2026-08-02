@extends('layouts.admin')
@section('title', 'Blog Yazıları')
@section('content')
    <x-admin.crud.index-layout title="Blog Makale Yönetimi" description="Kurumsal haber, makale ve blog gönderilerini yayınlayın.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.blogs.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Makale Ekle
            </x-admin.button>
            <x-admin.button href="{{ route('admin.blogs.analytics') }}" variant="secondary" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                İstatistikler
            </x-admin.button>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Görsel</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Başlık</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Yazar</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($blogs as $blog)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm text-neutral-900 dark:text-neutral-200">
                            @if($blog->featured_image)
                                <img src="{{ $blog->featured_image }}" class="w-12 h-12 object-cover rounded-xl border border-neutral-200 dark:border-neutral-700">
                            @else
                                <div class="w-12 h-12 bg-neutral-100 dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 flex items-center justify-center text-xs text-neutral-400">Yok</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $blog->title }}</span>
                                <div class="text-[11px] text-neutral-400 font-mono mt-1 w-fit px-1.5 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800">/{{ $blog->slug }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300">
                                {{ $blog->category ? $blog->category->name : 'Genel' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($blog->status === 'Published')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Yayında
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-500/20 dark:text-neutral-400 border border-neutral-200/50 dark:border-neutral-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-500 mr-1.5"></span>
                                    Taslak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold uppercase">
                                    {{ substr($blog->author ? $blog->author->name : 'B', 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $blog->author ? $blog->author->name : 'Bilinmiyor' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="p-1.5 text-neutral-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="{{ route('admin.blogs.duplicate', $blog->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Kopyala">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu makaleyi silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-neutral-400">Henüz eklenmiş makale bulunmamaktadır.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
