@extends('layouts.admin')
@section('title', 'Yorumlar')
@section('content')
    <x-admin.crud.index-layout title="Yorum Moderasyonu" description="Yazılarınıza gelen ziyaretçi yorumlarını denetleyin, onaylayın veya spam olarak işaretleyin.">
        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Yazar</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Yorum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Makale</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($comments as $comment)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $comment->author_name }}
                            <div class="text-[10px] text-neutral-400 mt-1">{{ $comment->author_email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate">
                            {{ $comment->content }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $comment->blog ? $comment->blog->title : 'Makale Silinmiş' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $comment->status === 'Approved' ? 'bg-green-100 text-green-800' : ($comment->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $comment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $comment->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            @if($comment->status !== 'Approved')
                                <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-500 hover:underline">Onayla</button>
                                </form>
                            @endif
                            @if($comment->status !== 'Rejected')
                                <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-neutral-500 hover:underline">Reddet</button>
                                </form>
                            @endif
                            @if($comment->status !== 'Spam')
                                <form action="{{ route('admin.comments.spam', $comment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-500 hover:underline">Spam</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-neutral-400">Henüz yapılmış bir yorum bulunmamaktadır.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
