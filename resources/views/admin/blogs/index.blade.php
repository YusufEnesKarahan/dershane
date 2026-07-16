@extends('layouts.admin')
@section('title', 'Blog Yazıları')
@section('content')
    <x-admin.crud.index-layout title="Blog Makale Yönetimi" description="Kurumsal haber, makale ve blog gönderilerini yayınlayın.">
        <x-slot name="actions">
            <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                Yeni Makale Ekle
            </a>
            <a href="{{ route('admin.blogs.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                İstatistikler
            </a>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Görsel</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Başlık</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Yazar</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($blogs as $blog)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm text-neutral-900 dark:text-neutral-200">
                            @if($blog->featured_image)
                                <img src="{{ $blog->featured_image }}" class="w-12 h-12 object-cover rounded-lg border">
                            @else
                                <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center text-xs text-neutral-400">Yok</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $blog->title }}
                            <div class="text-[10px] text-neutral-400 mt-1">/{{ $blog->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $blog->category ? $blog->category->name : 'Genel' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $blog->status === 'Published' ? 'bg-green-100 text-green-800' : 'bg-neutral-100 text-neutral-800' }}">
                                {{ $blog->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $blog->author ? $blog->author->name : 'Bilinmiyor' }}
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="text-primary hover:underline">Düzenle</a>
                            <form action="{{ route('admin.blogs.duplicate', $blog->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-neutral-500 hover:underline">Kopyala</button>
                            </form>
                            <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
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
