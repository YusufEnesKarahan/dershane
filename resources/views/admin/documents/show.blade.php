@extends('layouts.admin')
@section('title', 'Doküman Detayı')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-white shadow-sm" style="background-color: {{ $document->category->color ?? '#0d9488' }}">
                        {{ $document->category->name ?? 'Genel' }}
                    </span>
                    <h1 class="text-lg font-bold text-neutral-900 dark:text-white">{{ $document->title }}</h1>
                </div>
                <p class="text-xs text-neutral-500 mt-1">Dosya: {{ $document->file_name }} ({{ round($document->file_size / 1024, 1) }} KB) - Yükleyen: {{ $document->uploader->name ?? 'Sistem' }}</p>
            </div>
            
            <div class="flex gap-2">
                <x-admin.button href="{{ route('admin.documents.download', $document->id) }}" variant="success">
                    ⬇️ Dosyayı İndir
                </x-admin.button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Detaylar & Düzenleme -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Doküman Bilgileri</h3>
                
                <form method="POST" action="{{ route('admin.documents.update', $document->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <x-admin.form.field-group label="Başlık" id="title" required>
                        <input type="text" name="title" value="{{ $document->title }}" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Durum" id="status" required>
                        <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            <option value="active" {{ $document->status === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="archived" {{ $document->status === 'archived' ? 'selected' : '' }}>Arşivlendi</option>
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Açıklama" id="description">
                        <textarea name="description" rows="3" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none">{{ $document->description }}</textarea>
                    </x-admin.form.field-group>

                    <div class="pt-2">
                        <x-admin.button type="submit" variant="primary" class="w-full justify-center">Bilgileri Güncelle</x-admin.button>
                    </div>
                </form>
            </div>

            <!-- Orta/Sağ Panel: Versiyon Geçmişi & Yetkiler & Loglar -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Versiyon Geçmişi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Versiyon Geçmişi</h3>
                        <x-admin.button type="button" onclick="toggleModal('version-modal')" variant="primary" size="sm">
                            + Yeni Versiyon Yükle
                        </x-admin.button>
                    </div>

                    <div class="space-y-3">
                        @forelse($versions as $ver)
                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-sm">
                                <div>
                                    <span class="px-2 py-0.5 bg-neutral-200 dark:bg-neutral-700 font-mono font-bold rounded text-[11px] text-neutral-700 dark:text-neutral-300">v{{ $ver->version_number }}</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-200 ml-2">{{ $ver->notes }}</span>
                                    <div class="text-xs text-neutral-500 mt-1">Yükleyen: {{ $ver->uploader->name ?? 'Sistem' }} | {{ $ver->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-neutral-400 py-4">Versiyon kaydı bulunamadı.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Erişim & Paylaşım Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Rol Bazlı Erişim Yetkileri</h3>
                        <x-admin.button type="button" onclick="toggleModal('share-modal')" variant="secondary" size="sm">
                            + Yetki Tanımla
                        </x-admin.button>
                    </div>

                    <div class="space-y-2">
                        @forelse($permissions as $perm)
                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-sm">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $perm->role->name ?? 'Tüm Roller' }}</span>
                                <div class="flex gap-3 text-xs font-bold">
                                    <span class="{{ $perm->can_view ? 'text-emerald-600' : 'text-neutral-400' }}">Görüntüleme: {{ $perm->can_view ? 'Evet' : 'Hayır' }}</span>
                                    <span class="{{ $perm->can_download ? 'text-blue-600' : 'text-neutral-400' }}">İndirme: {{ $perm->can_download ? 'Evet' : 'Hayır' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-neutral-400 py-4">Özel erişim kuralı tanımlanmamış (Varsayılan Admin/Genel yetki geçerli).</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Versiyon Yükleme Modal -->
        <div id="version-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Yeni Versiyon Yükle</h3>
                    <button onclick="toggleModal('version-modal')" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('admin.documents.version', $document->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <x-admin.form.field-group label="Yeni Dosya" id="file" required>
                        <input type="file" name="file" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Versiyon Notu" id="notes">
                        <textarea name="notes" rows="2" placeholder="Nelerin güncellendiğini yazın..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
                    </x-admin.form.field-group>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('version-modal')" variant="secondary">
                            Vazgeç
                        </x-admin.button>
                        <x-admin.button type="submit" variant="primary">
                            Yükle
                        </x-admin.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Yetki Tanımlama Modal -->
        <div id="share-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Rol Bazlı Erişim Tanımla</h3>
                    <button onclick="toggleModal('share-modal')" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('admin.documents.share', $document->id) }}" class="space-y-4">
                    @csrf
                    
                    <x-admin.form.field-group label="Erişim Verilecek Rol" id="role_id" required>
                        <select name="role_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-center gap-3 font-bold text-sm text-neutral-700 dark:text-neutral-300 cursor-pointer">
                            <input type="checkbox" name="can_view" value="1" checked class="w-4 h-4 rounded border-neutral-300 text-primary focus:ring-primary dark:border-neutral-600 dark:bg-neutral-800">
                            Görüntüleme Yetkisi
                        </label>
                        <label class="flex items-center gap-3 font-bold text-sm text-neutral-700 dark:text-neutral-300 cursor-pointer">
                            <input type="checkbox" name="can_download" value="1" checked class="w-4 h-4 rounded border-neutral-300 text-primary focus:ring-primary dark:border-neutral-600 dark:bg-neutral-800">
                            Dosya İndirme Yetkisi
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <x-admin.button type="button" onclick="toggleModal('share-modal')" variant="secondary">
                            Vazgeç
                        </x-admin.button>
                        <x-admin.button type="submit" variant="primary">
                            Yetkiyi Kaydet
                        </x-admin.button>
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
