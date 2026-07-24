@extends('layouts.admin')
@section('title', 'Kategoriler')
@section('content')
    <x-admin.crud.index-layout title="Kategori Yönetimi" description="Blog makalelerinizi düzenlemek için hiyerarşik kategoriler oluşturun.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Kategori Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Kategori Ekle</h3>
                    <x-admin.form.layout :action="route('admin.blog-categories.store')" method="POST">
                    <x-admin.form.field-group label="Kategori Adı" id="name">
                        <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Üst Kategori" id="parent_id">
                        <select name="parent_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            <option value="">Yok (Ana Kategori)</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Açıklama" id="description">
                        <textarea name="description" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-20"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Kategori Oluştur
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Kategori Ağacı -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kategori Hiyerarşisi</h3>
                
                <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($categories as $cat)
                        <div class="py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm">{{ $cat->name }}</span>
                                    <span class="text-xs text-neutral-400 ml-2">/{{ $cat->slug }}</span>
                                </div>
                                <form action="{{ route('admin.blog-categories.destroy', $cat->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">Sil</button>
                                </form>
                            </div>
                            
                            <!-- Alt Kategoriler (Children Nested tree list rendering) -->
                            @if($cat->children->count() > 0)
                                <div class="pl-6 mt-2 space-y-2 border-l-2 border-neutral-100 dark:border-neutral-800">
                                    @foreach($cat->children as $child)
                                        <div class="flex items-center justify-between text-xs py-1">
                                            <span class="text-neutral-600 dark:text-neutral-400">— {{ $child->name }}</span>
                                            <form action="{{ route('admin.blog-categories.destroy', $child->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
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
