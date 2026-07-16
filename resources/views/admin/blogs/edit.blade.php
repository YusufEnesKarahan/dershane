@extends('layouts.admin')
@section('title', isset($blog) ? 'Makale Düzenle' : 'Yeni Makale Ekle')
@section('content')
    <div class="space-y-6" x-data="{ 
        content: '{{ isset($blog) ? addslashes($blog->content) : '' }}',
        get wordCount() {
            return this.content.trim().split(/\s+/).filter(w => w.length > 0).length;
        },
        get charCount() {
            return this.content.length;
        }
    }">
        <x-admin.crud.index-layout title="{{ isset($blog) ? 'Makaleyi Düzenle' : 'Yeni Makale Oluştur' }}" description="Markdown içerik editörü ve canlı SEO skoru denetleyicisi ile makalenizi hazırlayın.">
            <x-slot name="actions">
                <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                    Listeye Geri Dön
                </a>
            </x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sol Panel: Editör Formu -->
                <div class="lg:col-span-3 space-y-6">
                    <x-admin.form.layout :action="isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store')" method="POST">
                        @if(isset($blog))
                            @method('PUT')
                        @endif

                        <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
                            
                            <x-admin.form.field-group label="Başlık" id="title">
                                <input type="text" name="title" required value="{{ $blog->title ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="İçerik (Markdown)" id="content">
                                <textarea name="content" x-model="content" required class="w-full h-80 text-sm font-mono bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">{{ $blog->content ?? '' }}</textarea>
                                <div class="flex items-center justify-between text-[10px] text-neutral-400 mt-1">
                                    <span x-text="wordCount + ' kelime'"></span>
                                    <span x-text="charCount + ' karakter'"></span>
                                </div>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Özet" id="excerpt">
                                <textarea name="excerpt" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-20">{{ $blog->excerpt ?? '' }}</textarea>
                            </x-admin.form.field-group>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-admin.form.field-group label="Kategori" id="category_id">
                                    <select name="category_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                        <option value="">Seçiniz</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ (isset($blog) && $blog->category_id === $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Yayın Durumu" id="status">
                                    <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                        <option value="Draft" {{ (isset($blog) && $blog->status === 'Draft') ? 'selected' : '' }}>Draft (Taslak)</option>
                                        <option value="Review" {{ (isset($blog) && $blog->status === 'Review') ? 'selected' : '' }}>Review (İncelemede)</option>
                                        <option value="Published" {{ (isset($blog) && $blog->status === 'Published') ? 'selected' : '' }}>Published (Yayınlandı)</option>
                                        <option value="Archived" {{ (isset($blog) && $blog->status === 'Archived') ? 'selected' : '' }}>Archived (Arşivlendi)</option>
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Öne Çıkan Görsel" id="featured_image">
                                <x-admin.media-picker name="featured_image" value="{{ $blog->featured_image ?? '' }}" />
                            </x-admin.form.field-group>

                            @if(isset($blog))
                                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl space-y-4">
                                    <h4 class="text-xs font-bold text-neutral-800 dark:text-white">İlişkili Makaleler & Etiketler</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-admin.form.field-group label="Etiketler" id="tags">
                                            <select name="tags[]" multiple class="w-full h-24 text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2">
                                                @foreach($tags as $t)
                                                    <option value="{{ $t->id }}" {{ (isset($blog) && $blog->tags->contains($t->id)) ? 'selected' : '' }}>{{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>

                                        <x-admin.form.field-group label="Benzer Makaleler" id="related_posts">
                                            <select name="related_posts[]" multiple class="w-full h-24 text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2">
                                                @foreach($blogs as $otherBlog)
                                                    <option value="{{ $otherBlog->id }}" {{ (isset($blog) && $blog->relatedPosts->contains($otherBlog->id)) ? 'selected' : '' }}>{{ $otherBlog->title }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>
                                    </div>
                                </div>
                            @endif

                            <div class="pt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                    Kaydet
                                </button>
                                @if(isset($blog) && $blog->status !== 'Published')
                                    <form action="{{ route('admin.blogs.publish', $blog->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-xl transition">
                                            Yayınla
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>
                    </x-admin.form.layout>
                </div>

                <!-- Sağ Panel: SEO Skoru & Revizyon Listesi -->
                <div class="space-y-6">
                    <!-- SEO Content Analyzer Widget -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                        <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">İçerik Analiz Raporu</h4>
                        @if(isset($analysis))
                            <div class="flex items-center justify-between p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl">
                                <span class="text-xs font-semibold">SEO Skoru:</span>
                                <span class="text-lg font-bold text-primary">{{ $analysis['score'] }}/100</span>
                            </div>
                            <div class="space-y-2">
                                <div class="text-[10px] font-bold text-neutral-400">Kontrol Listesi</div>
                                @forelse($analysis['checklist'] as $item)
                                    <div class="text-[10px] text-red-500 flex items-start gap-1">
                                        <span>⚠️</span> <span>{{ $item }}</span>
                                    </div>
                                @empty
                                    <div class="text-[10px] text-green-500">🎉 Mükemmel! SEO optimizasyonu tam görünüyor.</div>
                                @endforelse
                            </div>
                        @else
                            <div class="text-xs text-neutral-400">Analiz sonucu için makaleyi kaydedin.</div>
                        @endif
                    </div>

                    <!-- Revizyon Geçmişi (Revisions Timeline) -->
                    @if(isset($blog))
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Revizyon Tarihçesi</h4>
                            <div class="space-y-3">
                                @forelse($blog->revisions as $rev)
                                    <div class="flex items-center justify-between text-xs p-2.5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl">
                                        <div>
                                            <div class="font-semibold">Versiyon #{{ $rev->revision_no }}</div>
                                            <div class="text-[10px] text-neutral-400">{{ $rev->created_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                        <form action="{{ route('admin.blogs.revisions.restore', ['blog' => $blog->id, 'revision' => $rev->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-primary hover:underline text-[10px] font-semibold">Geri Yükle</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="text-[10px] text-neutral-400">Henüz geçmiş revizyon bulunmamaktadır.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-admin.crud.index-layout>
    </div>
@endsection
