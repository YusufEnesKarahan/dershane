@extends('layouts.admin')
@section('title', 'Kategoriler & Lokasyonlar')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Demirbaş Kategorileri & Lokasyonlar</h1>
            <p class="text-xs text-neutral-500 mt-1">Demirbaşları sınıflandırmak için kategoriler oluşturun ve bulundukları fiziksel lokasyonları/şubeleri tanımlayın.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Sol Panel: Kategoriler -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kategoriler</h3>
                    <button onclick="toggleModal('category-modal')" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-lg transition">Yeni Kategori</button>
                </div>

                <div class="space-y-3">
                    @forelse($categories as $cat)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $cat->name }}</span>
                                <div class="text-[10px] text-neutral-400 font-mono mt-0.5">Kod: {{ $cat->code }}</div>
                            </div>
                            <span class="px-2 py-0.5 bg-neutral-200 dark:bg-neutral-700 text-[10px] font-bold rounded-lg">{{ $cat->assets_count }} Ürün</span>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kategori bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- Sağ Panel: Lokasyonlar -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Fiziksel Lokasyonlar / Depolar</h3>
                    <button onclick="toggleModal('location-modal')" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-lg transition">Yeni Lokasyon</button>
                </div>

                <div class="space-y-3">
                    @forelse($locations as $loc)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $loc->name }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">Şube: {{ $loc->branch->name ?? 'Merkez' }}</div>
                            </div>
                            <p class="text-[10px] text-neutral-500 max-w-xs">{{ $loc->description }}</p>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Lokasyon tanımlanmamıştır.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kategori Modal -->
        <div id="category-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Demirbaş Kategorisi</h3>
                    <button onclick="toggleModal('category-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori Adı</label>
                        <input type="text" name="name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori Kodu</label>
                        <input type="text" name="code" required placeholder="ELK, MOB, KRT vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('category-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lokasyon Modal -->
        <div id="location-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Lokasyon / Depo Tanımı</h3>
                    <button onclick="toggleModal('location-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.categories.store-location') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Lokasyon / Depo Adı</label>
                        <input type="text" name="name" required placeholder="Bilişim Laboratuvarı, Arşiv vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Bağlı Olduğu Şube</label>
                        <select name="branch_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="">Merkez / Genel</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('location-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
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
