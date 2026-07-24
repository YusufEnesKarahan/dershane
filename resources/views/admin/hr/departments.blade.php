@extends('layouts.admin')
@section('title', 'Departman & Pozisyon Yönetimi')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Departmanlar & Pozisyonlar</h1>
            <p class="text-xs text-neutral-500 mt-1">Kurum bünyesindeki organizasyon şemasını, departman ve rol pozisyon tanımlarını yapın.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Departman Tanımla -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Departman Oluştur</h3>
                
                <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Departman Adı</label>
                        <input type="text" name="name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kodu</label>
                        <input type="text" name="code" required placeholder="Eğitim, Finans, İK vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Yönetici / Müdür</label>
                        <select name="manager_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="">Seçilmedi</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition shadow-lg shadow-violet-950">
                        Departmanı Kaydet
                    </button>
                </form>
            </div>

            <!-- Orta/Sağ Panel: Departman Listesi & Pozisyonlar -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Departmanlar Listesi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Mevcut Departmanlar</h3>
                        <button onclick="toggleModal('position-modal')" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-lg transition">Yeni Pozisyon Ekle</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($departments as $dept)
                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-2">
                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span>{{ $dept->name }}</span>
                                    <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded font-mono">{{ $dept->code }}</span>
                                </div>
                                <div class="text-[10px] text-neutral-400">Müdür: {{ $dept->manager->name ?? 'Belirlenmedi' }}</div>
                                <p class="text-[10px] text-neutral-500 line-clamp-2">{{ $dept->description }}</p>
                                
                                <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                                    <span class="text-[9px] font-bold text-neutral-400 uppercase">Tanımlı Roller:</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @forelse($dept->positions as $pos)
                                            <span class="px-1.5 py-0.5 bg-neutral-200 dark:bg-neutral-700 text-[9px] font-bold rounded">{{ $pos->name }}</span>
                                        @empty
                                            <span class="text-[9px] text-neutral-400">Rol tanımlanmamış.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs text-neutral-400 py-6 md:col-span-2">Departman bulunmamaktadır.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

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
