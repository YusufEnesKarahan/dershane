@extends('layouts.admin')
@section('title', 'Etiketler')
@section('content')
    <x-admin.crud.index-layout title="Etiket Yönetimi" description="Yazılarınızı etiketleyin ve aynı anlama gelen mükerrer etiketleri birleştirin.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Etiket & Birleştirme Formları -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Etiket Ekle</h3>
                    <x-admin.form.layout :action="route('admin.tags.store')" method="POST">
                        <x-admin.form.field-group label="Etiket Adı" id="name">
                            <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>
                        <div class="pt-4">
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                                Ekle
                            </button>
                        </div>
                    </x-admin.form.layout>
                </div>

                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Etiketleri Birleştir</h3>
                    <p class="text-xs text-neutral-400 mb-4">Kaynak etikete bağlı tüm yazıları hedef etikete aktarır ve kaynak etiketi siler.</p>
                    <x-admin.form.layout :action="route('admin.tags.merge')" method="POST">
                        <x-admin.form.field-group label="Kaynak Etiket (Silinecek)" id="source_id">
                            <select name="source_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->usage_count }})</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Hedef Etiket (Kalacak)" id="target_id">
                            <select name="target_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->usage_count }})</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <div class="pt-4">
                            <button type="submit" class="px-4 py-2 bg-neutral-800 hover:bg-neutral-950 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                Birleştir
                            </button>
                        </div>
                    </x-admin.form.layout>
                </div>
            </div>

            <!-- Sağ Panel: Etiket Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Etiket Listesi</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @forelse($tags as $tag)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold">{{ $tag->name }}</span>
                                <span class="text-[9px] text-neutral-400 block">{{ $tag->usage_count }} yazı</span>
                            </div>
                            <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline">Sil</button>
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
