@extends('layouts.admin')
@section('title', 'Doküman Detayı')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white" style="background-color: {{ $document->category->color ?? '#0d9488' }}">
                        {{ $document->category->name ?? 'Genel' }}
                    </span>
                    <h1 class="text-lg font-bold text-neutral-900 dark:text-white">{{ $document->title }}</h1>
                </div>
                <p class="text-xs text-neutral-500 mt-1">Dosya: {{ $document->file_name }} ({{ round($document->file_size / 1024, 1) }} KB) - Yükleyen: {{ $document->uploader->name ?? 'Sistem' }}</p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('admin.documents.download', $document->id) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950 flex items-center gap-1">
                    <span>⬇️ Dosyayı İndir</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Detaylar & Düzenleme -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Doküman Bilgileri</h3>
                
                <form method="POST" action="{{ route('admin.documents.update', $document->id) }}" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Başlık</label>
                        <input type="text" name="title" value="{{ $document->title }}" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Durum</label>
                        <select name="status" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="active" {{ $document->status === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="archived" {{ $document->status === 'archived' ? 'selected' : '' }}>Arşivlendi</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="3" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">{{ $document->description }}</textarea>
                    </div>

                    <button type="submit" class="w-full p-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Bilgileri Güncelle</button>
                </form>
            </div>

            <!-- Orta/Sağ Panel: Versiyon Geçmişi & Yetkiler & Loglar -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Versiyon Geçmişi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Versiyon Geçmişi</h3>
                        <button onclick="toggleModal('version-modal')" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-lg transition">+ Yeni Versiyon Yükle</button>
                    </div>

                    <div class="space-y-3">
                        @forelse($versions as $ver)
                            <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                                <div>
                                    <span class="px-2 py-0.5 bg-neutral-200 dark:bg-neutral-700 font-mono font-bold rounded text-[10px]">v{{ $ver->version_number }}</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-200 ml-2">{{ $ver->notes }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">Yükleyen: {{ $ver->uploader->name ?? 'Sistem' }} | {{ $ver->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs text-neutral-400 py-4">Versiyon kaydı bulunamadı.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Erişim & Paylaşım Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Rol Bazlı Erişim Yetkileri</h3>
                        <button onclick="toggleModal('share-modal')" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-lg transition">+ Yetki Tanımla</button>
                    </div>

                    <div class="space-y-2">
                        @forelse($permissions as $perm)
                            <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $perm->role->name ?? 'Tüm Roller' }}</span>
                                <div class="flex gap-2 text-[10px] font-bold">
                                    <span class="{{ $perm->can_view ? 'text-green-600' : 'text-neutral-400' }}">Görüntüleme: {{ $perm->can_view ? 'Evet' : 'Hayır' }}</span>
                                    <span class="{{ $perm->can_download ? 'text-blue-600' : 'text-neutral-400' }}">İndirme: {{ $perm->can_download ? 'Evet' : 'Hayır' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs text-neutral-400 py-4">Özel erişim kuralı tanımlanmamış (Varsayılan Admin/Genel yetki geçerli).</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Versiyon Yükleme Modal -->
        <div id="version-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Versiyon Yükle</h3>
                    <button onclick="toggleModal('version-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.documents.version', $document->id) }}" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Yeni Dosya</label>
                        <input type="file" name="file" required class="w-full p-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Versiyon Notu</label>
                        <textarea name="notes" rows="2" placeholder="Nelerin güncellendiğini yazın..." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('version-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Yükle</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Yetki Tanımlama Modal -->
        <div id="share-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Rol Bazlı Erişim Tanımla</h3>
                    <button onclick="toggleModal('share-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.documents.share', $document->id) }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Erişim Verilecek Rol</label>
                        <select name="role_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2 font-bold">
                            <input type="checkbox" name="can_view" value="1" checked class="rounded text-teal-600">
                            Görüntüleme Yetkisi
                        </label>
                        <label class="flex items-center gap-2 font-bold">
                            <input type="checkbox" name="can_download" value="1" checked class="rounded text-teal-600">
                            Dosya İndirme Yetkisi
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="toggleModal('share-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Yetkiyi Kaydet</button>
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
