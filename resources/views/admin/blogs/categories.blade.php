@extends('layouts.admin')
@section('title', 'Kategoriler')
@section('content')
    <x-admin.crud.index-layout title="Kategori Yönetimi" description="Blog makalelerinizi düzenlemek için hiyerarşik kategoriler oluşturun.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Kategori Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Kategori Ekle</h3>
                    <x-admin.form.layout :action="route('admin.blog-categories.store')" method="POST">
                    <x-admin.form.field-group label="Kategori Adı" id="name">
                        <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Üst Kategori" id="parent_id">
                        <select name="parent_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            <option value="">Yok (Ana Kategori)</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Açıklama" id="description">
                        <textarea name="description" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors h-24 resize-none"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                        <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                            Kategori Oluştur
                        </x-admin.button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Kategori Ağacı -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kategori Hiyerarşisi</h3>
                
                <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($categories as $cat)
                        <div class="py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm">{{ $cat->name }}</span>
                                    <span class="text-xs text-neutral-400 ml-2">/{{ $cat->slug }}</span>
                                </div>
                                <form action="{{ route('admin.blog-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Alt Kategoriler (Children Nested tree list rendering) -->
                            @if($cat->children->count() > 0)
                                <div class="pl-6 mt-2 space-y-2 border-l-2 border-neutral-100 dark:border-neutral-800">
                                    @foreach($cat->children as $child)
                                        <div class="flex items-center justify-between text-xs py-1">
                                            <span class="text-neutral-600 dark:text-neutral-400">— {{ $child->name }}</span>
                                            <form action="{{ route('admin.blog-categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-sm text-neutral-400 py-8">Henüz kategori eklenmemiştir.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
