@extends('layouts.admin')
@section('title', 'Kurs Seviyeleri')
@section('content')
    <x-admin.crud.index-layout title="Seviye Yönetimi" description="Kurs programlarınız için seviyeler (Örn: Başlangıç, Orta, İleri) oluşturun.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Seviye Ekle</h3>
                <x-admin.form.layout :action="route('admin.courses.levels.store')" method="POST">
                    <x-admin.form.field-group label="Seviye Adı" id="name">
                        <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                    </x-admin.form.field-group>
                    <div class="pt-4">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Seviye Oluştur
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Seviyeler Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Tanımlı Seviyeler</h3>
                
                <div class="divide-y divide-neutral-100">
                    @forelse($levels as $level)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-semibold text-neutral-800">{{ $level->name }}</span>
                                <span class="text-neutral-400 ml-2">/{{ $level->slug }}</span>
                            </div>
                            <form action="{{ route('admin.courses.levels.destroy', $level->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-sm text-neutral-400 py-8">Henüz seviye tanımlanmamış.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
