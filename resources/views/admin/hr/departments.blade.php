@extends('layouts.admin')
@section('title', 'Departman & Pozisyon Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Departmanlar & Pozisyonlar" description="Kurum bünyesindeki organizasyon şemasını, departman ve rol pozisyon tanımlarını yapın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Departman Tanımla -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Yeni Departman Oluştur
                        </h3>
                    </div>
                    
                    <div class="p-6 flex-1">
                        <x-admin.form.layout :action="route('admin.departments.store')" method="POST">
                            <div class="space-y-1 mb-4">
                                <label class="font-bold text-neutral-600 dark:text-neutral-400 text-xs">Departman Adı</label>
                                <input type="text" name="name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-xs">
                            </div>

                            <div class="space-y-1 mb-4">
                                <label class="font-bold text-neutral-600 dark:text-neutral-400 text-xs">Kodu</label>
                                <input type="text" name="code" required placeholder="Eğitim, Finans, İK vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-xs">
                            </div>

                            <div class="space-y-1 mb-4">
                                <label class="font-bold text-neutral-600 dark:text-neutral-400 text-xs">Yönetici / Müdür</label>
                                <select name="manager_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-xs">
                                    <option value="">Seçilmedi</option>
                                    @foreach($users as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1 mb-6">
                                <label class="font-bold text-neutral-600 dark:text-neutral-400 text-xs">Açıklama</label>
                                <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-xs"></textarea>
                            </div>

                            <div class="pt-6 border-t border-neutral-100 dark:border-neutral-800">
                                <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                                    Departmanı Kaydet
                                </x-admin.button>
                            </div>
                        </x-admin.form.layout>
                    </div>
                </div>
            </div>

            <!-- Orta/Sağ Panel: Departman Listesi & Pozisyonlar -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Departmanlar Listesi -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            Mevcut Departmanlar
                        </h3>
                        <button onclick="toggleModal('position-modal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-xs font-bold rounded-lg transition-colors text-neutral-700 dark:text-neutral-300 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Yeni Pozisyon Ekle
                        </button>
                    </div>

                    <div class="p-6 flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($departments as $dept)
                                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-3 group hover:border-violet-500/30 transition-colors">
                                    <div class="flex justify-between items-center text-xs font-bold">
                                        <span class="text-neutral-900 dark:text-white text-sm">{{ $dept->name }}</span>
                                        <span class="px-2 py-0.5 bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400 rounded-md font-mono text-[10px]">{{ $dept->code }}</span>
                                    </div>
                                    <div class="text-[11px] text-neutral-500 font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Müdür: {{ $dept->manager->name ?? 'Belirlenmedi' }}
                                    </div>
                                    <p class="text-[11px] text-neutral-500 line-clamp-2 leading-relaxed">{{ $dept->description }}</p>
                                    
                                    <div class="pt-3 mt-3 border-t border-neutral-200 dark:border-neutral-700/50">
                                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block mb-2">Tanımlı Roller:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($dept->positions as $pos)
                                                <span class="px-2 py-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-[10px] font-bold text-neutral-600 dark:text-neutral-300 rounded-md shadow-sm">{{ $pos->name }}</span>
                                            @empty
                                                <span class="text-[10px] text-neutral-400 italic">Rol tanımlanmamış.</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2">
                                    <x-admin.empty-state
                                        icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        title="Departman Bulunamadı"
                                        description="Sistemde henüz bir departman tanımlanmamış. Sol taraftaki formu kullanarak ilk departmanı ekleyebilirsiniz."
                                    />
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </x-admin.crud.index-layout>

        <!-- Pozisyon Ekleme Modal -->
        <div id="position-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Pozisyon Ekle</h3>
                    <button onclick="toggleModal('position-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.positions.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Departman</label>
                        <select name="department_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Rol / Pozisyon Adı</label>
                        <input type="text" name="name" required placeholder="Eğitmen, Satış Temsilcisi vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Seviye</label>
                        <select name="level" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="Junior">Junior</option>
                            <option value="Mid">Mid-level</option>
                            <option value="Senior">Senior</option>
                            <option value="Lead">Lead / Manager</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Taban Maaş</label>
                        <input type="number" name="base_salary" required step="0.01" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Rol Detayları</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('position-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
