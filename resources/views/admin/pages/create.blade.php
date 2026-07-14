@extends('layouts.admin')
@section('title', 'Sayfa Ekle')
@section('content')
    <x-admin.crud.index-layout title="Yeni Sayfa Ekle" description="Web siteniz için zengin içerikli, SEO dostu yeni bir sayfa oluşturun.">
        <div x-data="{ 
            activeTab: 'content',
            contentMarkdown: '',
            titleText: '',
            slugText: '',
            updateSlug() {
                this.slugText = this.titleText.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
            }
        }">
            <x-admin.form.layout :action="route('admin.pages.store')" method="POST">
                <!-- Sekme Butonları -->
                <div class="flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-4 mb-6">
                    <button type="button" @click="activeTab = 'content'" :class="activeTab === 'content' ? 'bg-primary text-white' : 'bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300'" class="px-4 py-2 text-sm font-medium rounded-xl transition">İçerik Editörü</button>
                    <button type="button" @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'bg-primary text-white' : 'bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300'" class="px-4 py-2 text-sm font-medium rounded-xl transition">SEO Ayarları</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Sol Panel: İçerik / SEO Formları -->
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
                                <textarea name="excerpt" id="excerpt" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-16"></textarea>
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
                            <div x-data="{ metaTitle: '', metaDesc: '' }">
                                <x-admin.form.field-group label="Meta Title" id="meta_title" :error="$errors->first('seo.meta_title')">
                                    <input type="text" name="seo[meta_title]" x-model="metaTitle" maxlength="60" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <div class="text-[10px] text-neutral-400 mt-1" x-text="metaTitle.length + '/60 karakter'"></div>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Meta Description" id="meta_description" :error="$errors->first('seo.meta_description')">
                                    <textarea name="seo[meta_description]" x-model="metaDesc" maxlength="160" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-24"></textarea>
                                    <div class="text-[10px] text-neutral-400 mt-1" x-text="metaDesc.length + '/160 karakter'"></div>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Meta Keywords" id="meta_keywords" :error="$errors->first('seo.meta_keywords')">
                                <input type="text" name="seo[meta_keywords]" placeholder="Örn: dershane, yks hazırlık, butik eğitim" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Robots" id="robots" :error="$errors->first('seo.robots')">
                                <select name="seo[robots]" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <option value="index, follow">Index, Follow</option>
                                    <option value="noindex, nofollow">Noindex, Nofollow</option>
                                </select>
                            </x-admin.form.field-group>
                        </div>
                    </div>

                    <!-- Sağ Panel: Şablon & Yayın Eylemleri -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <x-admin.form.field-group label="Sayfa Şablonu" id="template" :error="$errors->first('template')">
                                <select name="template" id="template" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    @foreach($templates as $key => $lbl)
                                        <option value="{{ $key }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Üst Sayfa" id="parent_id" :error="$errors->first('parent_id')">
                                <select name="parent_id" id="parent_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    <option value="">Yok (Ana Sayfa Seviyesi)</option>
                                    @foreach($pages as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Sıralama" id="sort_order" :error="$errors->first('sort_order')">
                                <input type="number" name="sort_order" id="sort_order" value="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300 cursor-pointer">
                                <input type="checkbox" name="is_homepage" value="1" class="rounded text-primary focus:ring-primary border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800">
                                <span>Ana Sayfa Olarak Ayarla</span>
                            </label>

                            <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                Sayfayı Kaydet
                            </button>
                            <a href="{{ route('admin.pages.index') }}" class="w-full block text-center px-4 py-2 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl text-neutral-600 dark:text-neutral-300 transition">
                                Vazgeç
                            </a>
                        </div>
                    </div>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
