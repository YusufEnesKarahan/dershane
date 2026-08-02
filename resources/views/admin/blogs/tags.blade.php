@extends('layouts.admin')
@section('title', 'Etiketler')
@section('content')
    <x-admin.crud.index-layout title="Etiket Yönetimi" description="Yazılarınızı etiketleyin ve aynı anlama gelen mükerrer etiketleri birleştirin.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Etiket & Birleştirme Formları -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Etiket Ekle</h3>
                    <x-admin.form.layout :action="route('admin.tags.store')" method="POST">
                        <x-admin.form.field-group label="Etiket Adı" id="name">
                            <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                            <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                                Ekle
                            </x-admin.button>
                        </div>
                    </x-admin.form.layout>
                </div>

                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Etiketleri Birleştir</h3>
                    <p class="text-xs text-neutral-400 mb-4">Kaynak etikete bağlı tüm yazıları hedef etikete aktarır ve kaynak etiketi siler.</p>
                    <x-admin.form.layout :action="route('admin.tags.merge')" method="POST">
                        <x-admin.form.field-group label="Kaynak Etiket (Silinecek)" id="source_id">
                            <select name="source_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->usage_count }})</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Hedef Etiket (Kalacak)" id="target_id">
                            <select name="target_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->usage_count }})</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                            <x-admin.button type="submit" variant="secondary" icon="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" class="w-full justify-center">
                                Birleştir
                            </x-admin.button>
                        </div>
                    </x-admin.form.layout>
                </div>
            </div>

            <!-- Sağ Panel: Etiket Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Etiket Listesi</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @forelse($tags as $tag)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold">{{ $tag->name }}</span>
                                <span class="text-[9px] text-neutral-400 block">{{ $tag->usage_count }} yazı</span>
                            </div>
                            <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-3 text-center text-sm text-neutral-400 py-8">Henüz etiket bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
