@extends('layouts.admin')
@section('title', 'Yorumlar')
@section('content')
    <x-admin.crud.index-layout title="Yorum Moderasyonu" description="Yazılarınıza gelen ziyaretçi yorumlarını denetleyin, onaylayın veya spam olarak işaretleyin.">
        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Yazar</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Yorum</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Makale</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tarih</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($comments as $comment)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $comment->author_name }}</span>
                                <span class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $comment->author_email }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate">
                            {{ $comment->content }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $comment->blog ? $comment->blog->title : 'Makale Silinmiş' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($comment->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Onaylı
                                </span>
                            @elseif($comment->status === 'Pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400 border border-amber-200/50 dark:border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                    Bekliyor
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400 border border-red-200/50 dark:border-red-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    {{ $comment->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300 font-mono">
                                {{ $comment->created_at->format('d.m.Y H:i') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($comment->status !== 'Approved')
                                    <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-neutral-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-colors" title="Onayla">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    </form>
                                @endif
                                @if($comment->status !== 'Rejected')
                                    <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-neutral-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors" title="Reddet">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                @endif
                                @if($comment->status !== 'Spam')
                                    <form action="{{ route('admin.comments.spam', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Spam İşaretle">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="inline" onsubmit="return confirm('Kalıcı olarak silinecek, emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Sil">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
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
