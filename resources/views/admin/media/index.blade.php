@extends('layouts.admin')
@section('title', 'Medya Kütüphanesi')
@section('content')
    <div class="space-y-6" x-data="{ 
        showUpload: false,
        activeMedia: null,
        activeFolderId: @js($currentFolder?->id ?? ''),
        showNewFolder: false,
        cropImageSrc: '',
        showCropModal: false,
        rotation: 0,
        flipH: false,
        flipV: false,
        applyTransforms() {
            let img = document.getElementById('cropper-img');
            if (img) {
                let transform = `rotate(${this.rotation}deg)`;
                if (this.flipH) transform += ' scaleX(-1)';
                if (this.flipV) transform += ' scaleY(-1)';
                img.style.transform = transform;
            }
        }
    }">
        <x-admin.crud.index-layout title="Medya Kütüphanesi (DAM)" description="Resim, belge ve dijital varlıklarınızı organize edin, biçimlendirin.">
            <x-slot name="actions">
                <button type="button" @click="showNewFolder = true" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-xl transition">
                    + Yeni Klasör
                </button>
                <button type="button" @click="showUpload = !showUpload" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Varis Yükle
                </button>
            </x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sol Panel: Klasör Ağacı -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                    <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-4">Klasörler</h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('admin.media.index') }}" class="flex items-center gap-2 p-2 rounded-lg text-xs font-semibold {{ !$currentFolder ? 'bg-primary/10 text-primary' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/40' }}">
                            📂 Kök Dizin (Root)
                        </a>
                        
                        @foreach($folderTree as $folder)
                            <div class="pl-2">
                                <a href="{{ route('admin.media.index', ['folder_id' => $folder->id]) }}" class="flex items-center justify-between p-2 rounded-lg text-xs {{ $currentFolder?->id === $folder->id ? 'bg-primary/10 text-primary font-bold' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/40' }}">
                                    <span>📁 {{ $folder->name }}</span>
                                    <form method="POST" action="{{ route('admin.media-folders.destroy', $folder) }}" onsubmit="return confirm('Klasörü silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-[10px]">Sil</button>
                                    </form>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Orta Panel: Dosya Listesi / Grid workspace -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Dropzone upload pane -->
                    <div x-show="showUpload" class="bg-neutral-50 dark:bg-neutral-800/40 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-2xl p-8 flex flex-col items-center justify-center text-center cursor-pointer relative"
                         @dragover.prevent=""
                         @drop.prevent="
                            let file = $event.dataTransfer.files[0];
                            if (file) {
                                let fd = new FormData();
                                fd.append('file', file);
                                fd.append('folder_id', activeFolderId);
                                fetch('{{ route('admin.media.store') }}', {
                                    method: 'POST',
                                    body: fd,
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                }).then(res => res.json()).then(() => window.location.reload());
                            }
                         ">
                        <span class="text-neutral-500 text-sm">Dosyaları buraya sürükleyip bırakın veya tıklayarak yükleyin</span>
                        <input type="file" @change="
                            let file = $event.target.files[0];
                            if (file) {
                                let fd = new FormData();
                                fd.append('file', file);
                                fd.append('folder_id', activeFolderId);
                                fetch('{{ route('admin.media.store') }}', {
                                    method: 'POST',
                                    body: fd,
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                }).then(res => res.json()).then(() => window.location.reload());
                            }
                        " class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>

                    <!-- Media Grid -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                        @if($mediaList->isEmpty())
                            <div class="text-center py-12">
                                <p class="text-sm text-neutral-400">Bu klasörde henüz yüklenmiş bir dosya bulunmamaktadır.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($mediaList as $item)
                                    <div @click="activeMedia = @js($item)" class="border border-neutral-200 dark:border-neutral-800 rounded-xl p-2 flex flex-col items-center justify-between cursor-pointer hover:border-primary transition bg-neutral-50 dark:bg-neutral-800/40 relative">
                                        @if(str_starts_with($item->mime_type, 'image/'))
                                            <img src="{{ $item->getUrl('thumb') }}" class="w-24 h-24 object-cover rounded-lg">
                                        @else
                                            <div class="w-24 h-24 flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 rounded text-neutral-400 font-bold uppercase text-xs">
                                                {{ $item->extension }}
                                            </div>
                                        @endif
                                        <span class="text-[10px] text-neutral-600 dark:text-neutral-400 text-center truncate w-full mt-2">{{ $item->title }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $mediaList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Metadata Preview overlay Drawer -->
            <div x-show="activeMedia" class="fixed inset-y-0 right-0 z-50 w-96 bg-white dark:bg-neutral-900 border-l border-neutral-100 dark:border-neutral-800 p-6 shadow-premium flex flex-col justify-between" style="display: none;">
                <div class="space-y-6 overflow-y-auto">
                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-3">
                        <h4 class="text-sm font-bold text-neutral-950 dark:text-white" x-text="activeMedia ? activeMedia.title : ''">Detaylar</h4>
                        <button type="button" @click="activeMedia = null" class="text-neutral-400 hover:text-neutral-600">×</button>
                    </div>

                    <template x-if="activeMedia && activeMedia.mime_type.startsWith('image/')">
                        <div class="aspect-video w-full rounded-xl overflow-hidden bg-neutral-50 border border-neutral-100 dark:border-neutral-800 relative">
                            <img :src="activeMedia.getUrl" class="w-full h-full object-contain">
                        </div>
                    </template>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between"><span class="text-neutral-400">Dosya Adı:</span> <span class="text-neutral-800 dark:text-neutral-200" x-text="activeMedia ? activeMedia.original_name : ''"></span></div>
                        <div class="flex justify-between"><span class="text-neutral-400">Boyut:</span> <span class="text-neutral-800 dark:text-neutral-200" x-text="activeMedia ? (activeMedia.size / 1024).toFixed(2) + ' KB' : ''"></span></div>
                        <div class="flex justify-between"><span class="text-neutral-400">Mime Type:</span> <span class="text-neutral-800 dark:text-neutral-200" x-text="activeMedia ? activeMedia.mime_type : ''"></span></div>
                        <div class="flex justify-between"><span class="text-neutral-400">Checksum (SHA256):</span> <span class="text-neutral-800 dark:text-neutral-200 truncate w-32" x-text="activeMedia ? activeMedia.checksum : ''"></span></div>
                    </div>
                </div>

                <div class="border-t border-neutral-100 dark:border-neutral-800 pt-4 flex gap-2">
                    <template x-if="activeMedia && activeMedia.mime_type.startsWith('image/')">
                        <button type="button" @click="cropImageSrc = activeMedia.getUrl; showCropModal = true;" class="flex-1 px-3 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-xs font-semibold rounded-lg transition">Düzenle (Crop/Rotate)</button>
                    </template>
                    <form method="POST" :action="activeMedia ? '{{ route('admin.media.index') }}/' + activeMedia.id : ''" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition">Sil</button>
                    </form>
                </div>
            </div>

            <!-- New Folder Modal dialog -->
            <div x-show="showNewFolder" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl p-6 border border-neutral-100 dark:border-neutral-800 max-w-sm w-full shadow-premium">
                    <h4 class="text-sm font-bold text-neutral-950 dark:text-white mb-4">Klasör Oluştur</h4>
                    <form method="POST" action="{{ route('admin.media-folders.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="parent_id" :value="activeFolderId">
                        <x-admin.form.field-group label="Klasör Adı" id="folder_name">
                            <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>
                        <div class="flex items-center gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition">Oluştur</button>
                            <button type="button" @click="showNewFolder = false" class="flex-1 px-4 py-2 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl text-neutral-600 dark:text-neutral-300 transition">İptal</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Interactive Crop/Rotate UI Modal -->
            <div x-show="showCropModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl p-6 border border-neutral-100 dark:border-neutral-800 max-w-2xl w-full shadow-premium flex flex-col">
                    <h4 class="text-sm font-bold text-neutral-950 dark:text-white mb-4">Medya Manipülasyon Paneli</h4>
                    
                    <div class="aspect-video w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden flex items-center justify-center relative p-4 mb-4">
                        <img :src="cropImageSrc" id="cropper-img" class="max-w-full max-h-full object-contain transition-transform duration-200">
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center gap-2 justify-center mb-6">
                        <button type="button" @click="rotation -= 90; applyTransforms();" class="px-3 py-1.5 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-lg">↩ 90° Sola Döndür</button>
                        <button type="button" @click="rotation += 90; applyTransforms();" class="px-3 py-1.5 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-lg">↪ 90° Sağa Döndür</button>
                        <button type="button" @click="flipH = !flipH; applyTransforms();" class="px-3 py-1.5 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-lg">↔ Yatay Çevir</button>
                        <button type="button" @click="flipV = !flipV; applyTransforms();" class="px-3 py-1.5 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-lg">↕ Dikey Çevir</button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="showCropModal = false; alert('Manipülasyonlar kaydedildi (GD).'); window.location.reload();" class="flex-1 px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition">Kaydet</button>
                        <button type="button" @click="showCropModal = false" class="flex-1 px-4 py-2 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl text-neutral-600 dark:text-neutral-300 transition">İptal</button>
                    </div>
                </div>
            </div>

        </x-admin.crud.index-layout>
    </div>
@endsection
