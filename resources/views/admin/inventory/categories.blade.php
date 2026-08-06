@extends('layouts.admin')
@section('title', 'Kategoriler & Lokasyonlar')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <h1 class="text-lg font-bold text-slate-900 dark:text-white">Demirbaş Kategorileri & Lokasyonlar</h1>
            <p class="text-xs text-slate-500 mt-1">Demirbaşları sınıflandırmak için kategoriler oluşturun ve bulundukları fiziksel lokasyonları/şubeleri tanımlayın.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Sol Panel: Kategoriler -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Kategoriler</h3>
                    <x-admin.button type="button" onclick="toggleModal('category-modal')" variant="primary" size="sm">Yeni Kategori</x-admin.button>
                </div>

                <div class="space-y-3">
                    @forelse($categories as $cat)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $cat->name }}</span>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">Kod: {{ $cat->code }}</div>
                            </div>
                            <span class="px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-[10px] font-bold rounded-lg">{{ $cat->assets_count }} Ürün</span>
                        </div>
                    @empty
                        <div class="text-center text-xs text-slate-400 py-6">Kategori bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- Sağ Panel: Lokasyonlar -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Fiziksel Lokasyonlar / Depolar</h3>
                    <x-admin.button type="button" onclick="toggleModal('location-modal')" variant="secondary" size="sm">Yeni Lokasyon</x-admin.button>
                </div>

                <div class="space-y-3">
                    @forelse($locations as $loc)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $loc->name }}</span>
                                <div class="text-[10px] text-slate-400 mt-0.5">Şube: {{ $loc->branch->name ?? 'Merkez' }}</div>
                            </div>
                            <p class="text-[10px] text-slate-500 max-w-xs">{{ $loc->description }}</p>
                        </div>
                    @empty
                        <div class="text-center text-xs text-slate-400 py-6">Lokasyon tanımlanmamıştır.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kategori Modal -->
        <div id="category-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Yeni Demirbaş Kategorisi</h3>
                    <button onclick="toggleModal('category-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.categories.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Kategori Adı</label>
                        <input type="text" name="name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Kategori Kodu</label>
                        <input type="text" name="code" required placeholder="ELK, MOB, KRT vb." class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('category-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Kaydet</x-admin.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lokasyon Modal -->
        <div id="location-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Yeni Lokasyon / Depo Tanımı</h3>
                    <button onclick="toggleModal('location-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.categories.store-location') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Lokasyon / Depo Adı</label>
                        <input type="text" name="name" required placeholder="Bilişim Laboratuvarı, Arşiv vb." class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Bağlı Olduğu Şube</label>
                        <select name="branch_id" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <option value="">Merkez / Genel</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('location-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Kaydet</x-admin.button>
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
