@extends('layouts.admin')
@section('title', 'Sayfa Düzenle')
@section('content')
    <x-admin.crud.index-layout title="Sayfayı Düzenle" description="{{ $page->title }} sayfa detaylarını güncelleyin.">
        <div x-data="{ 
            activeTab: 'content',
            contentMarkdown: @js($page->content ?? ''),
            titleText: @js($page->title ?? ''),
            slugText: @js($page->slug ?? ''),
            updateSlug() {
                this.slugText = this.titleText.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
            }
        }">
            <x-admin.form.layout :action="route('admin.pages.update', $page)" method="PUT">
                <!-- Sekme Butonları -->
                <div class="flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-4 mb-6">
                    <button type="button" @click="activeTab = 'content'" :class="activeTab === 'content' ? 'bg-primary text-white' : 'bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300'" class="px-4 py-2 text-sm font-medium rounded-xl transition">İçerik Editörü</button>
                    <button type="button" @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'bg-primary text-white' : 'bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300'" class="px-4 py-2 text-sm font-medium rounded-xl transition">SEO Ayarları</button>
                    <button type="button" @click="activeTab = 'revisions'" :class="activeTab === 'revisions' ? 'bg-primary text-white' : 'bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300'" class="px-4 py-2 text-sm font-medium rounded-xl transition">Revizyon Geçmişi</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Sol Panel: İçerik / SEO / Revizyon Formları -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Sekme 1: İçerik Editörü -->
                        <div x-show="activeTab === 'content'" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <x-admin.form.field-group label="Sayfa Başlığı" id="title" :error="$errors->first('title')">
                                <input type="text" name="title" id="title" required x-model="titleText" @input="updateSlug" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Slug" id="slug" :error="$errors->first('slug')">
                                <input type="text" name="slug" id="slug" required x-model="slugText" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Özet (Excerpt)" id="excerpt" :error="$errors->first('excerpt')">
                                <textarea name="excerpt" id="excerpt" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-16">{{ $page->excerpt }}</textarea>
                            </x-admin.form.field-group>

                            <!-- Markdown Editor + Live Preview -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-admin.form.field-group label="İçerik (Markdown)" id="content" :error="$errors->first('content')">
                                    <textarea name="content" id="content" x-model="contentMarkdown" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-72 font-mono" placeholder="# Başlık\n\nMetin..."></textarea>
                                </x-admin.form.field-group>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">Canlı Önizleme</label>
                                    <div class="w-full bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 h-72 overflow-y-auto text-sm text-neutral-800 dark:text-neutral-200 prose max-w-none" x-text="contentMarkdown">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sekme 2: SEO Paneli -->
                        <div x-show="activeTab === 'seo'" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <div x-data="{ metaTitle: @js($page->meta_title ?? ''), metaDesc: @js($page->meta_description ?? '') }">
                                <x-admin.form.field-group label="Meta Title" id="meta_title" :error="$errors->first('seo.meta_title')">
                                    <input type="text" name="seo[meta_title]" x-model="metaTitle" value="{{ $page->meta_title }}" maxlength="60" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <div class="text-[10px] text-neutral-400 mt-1" x-text="metaTitle.length + '/60 karakter'"></div>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Meta Description" id="meta_description" :error="$errors->first('seo.meta_description')">
                                    <textarea name="seo[meta_description]" x-model="metaDesc" maxlength="160" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-24">{{ $page->meta_description }}</textarea>
                                    <div class="text-[10px] text-neutral-400 mt-1" x-text="metaDesc.length + '/160 karakter'"></div>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Meta Keywords" id="meta_keywords" :error="$errors->first('seo.meta_keywords')">
                                <input type="text" name="seo[meta_keywords]" value="{{ $page->meta_keywords }}" placeholder="Örn: dershane, yks hazırlık" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Robots" id="robots" :error="$errors->first('seo.robots')">
                                <select name="seo[robots]" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <option value="index, follow" {{ $page->robots === 'index, follow' ? 'selected' : '' }}>Index, Follow</option>
                                    <option value="noindex, nofollow" {{ $page->robots === 'noindex, nofollow' ? 'selected' : '' }}>Noindex, Nofollow</option>
                                </select>
                            </x-admin.form.field-group>
                        </div>

                        <!-- Sekme 3: Revizyon Geçmişi -->
                        <div x-show="activeTab === 'revisions'" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Kaydedilmiş Revizyonlar (JSON Snapshots)</h3>
                            <div class="space-y-2">
                                @forelse($page->revisions ?? [] as $rev)
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 rounded-lg border border-neutral-100 dark:border-neutral-800 text-xs">
                                        <div class="flex items-center justify-between font-semibold mb-1">
                                            <span>{{ $rev['title'] }}</span>
                                            <span class="text-neutral-400">{{ \Carbon\Carbon::parse($rev['timestamp'])->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <p class="text-neutral-500 line-clamp-2">{{ $rev['content'] }}</p>
                                    </div>
                                @empty
                                    <p class="text-xs text-neutral-400">Henüz hiçbir revizyon geçmişi bulunmuyor.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Panel: Şablon & Yayın Eylemleri -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <!-- Yayın workflow durum butonları -->
                            <div class="border-b border-neutral-100 dark:border-neutral-800 pb-3">
                                <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Yayın Durumu</label>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    {{ $page->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300' : '' }}
                                    {{ $page->status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300' : '' }}
                                    {{ $page->status === 'archived' ? 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300' : '' }}
                                ">
                                    {{ ucfirst($page->status) }}
                                </span>
                            </div>

                            <x-admin.form.field-group label="Sayfa Şablonu" id="template" :error="$errors->first('template')">
                                <select name="template" id="template" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    @foreach($templates as $key => $lbl)
                                        <option value="{{ $key }}" {{ $page->template === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Üst Sayfa" id="parent_id" :error="$errors->first('parent_id')">
                                <select name="parent_id" id="parent_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <option value="">Yok (Ana Sayfa Seviyesi)</option>
                                    @foreach($pages as $p)
                                        <option value="{{ $p->id }}" {{ $page->parent_id === $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Sıralama" id="sort_order" :error="$errors->first('sort_order')">
                                <input type="number" name="sort_order" id="sort_order" value="{{ $page->sort_order }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300 cursor-pointer">
                                <input type="checkbox" name="is_homepage" value="1" {{ $page->isHomepage() ? 'checked' : '' }} class="rounded text-primary focus:ring-primary border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800">
                                <span>Ana Sayfa Olarak Ayarla</span>
                            </label>

                            <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                Değişiklikleri Güncelle
                            </button>
                            <a href="{{ route('admin.pages.index') }}" class="w-full block text-center px-4 py-2 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl text-neutral-600 dark:text-neutral-300 transition">
                                Vazgeç
                            </a>
                        </div>
                    </div>
                </div>
            </x-admin.form.layout>

            @permission('pages.update')
                <!-- Publish Workflow actions -->
                <div class="mt-4 bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                    <span class="text-xs text-neutral-500">Yayınlama Akışı (Publish Workflow):</span>
                    <div class="flex items-center gap-2">
                        @if($page->status !== 'published')
                            <form method="POST" action="{{ route('admin.pages.publish', $page) }}">
                                @csrf
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition shadow-sm">Yayınla</button>
                            </form>
                        @endif
                        @if($page->status !== 'archived')
                            <form method="POST" action="{{ route('admin.pages.publish', $page) }}">
                                @csrf
                                <input type="hidden" name="status" value="archived">
                                <button type="submit" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-semibold rounded-lg transition">Arşivle</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endpermission
        </div>
    </x-admin.crud.index-layout>
@endsection
