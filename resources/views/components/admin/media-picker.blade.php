@props(['name', 'label' => '', 'value' => ''])
<div class="space-y-1.5" x-data="{ 
    showModal: false, 
    selectedUrl: @js($value), 
    selectedUuid: '',
    items: [],
    searchQuery: '',
    folderId: '',
    folders: [],
    loadItems() {
        let url = '{{ route('admin.media.picker-list') }}?search=' + this.searchQuery + '&folder_id=' + this.folderId;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                this.items = data;
            });
    },
    selectMedia(item) {
        this.selectedUrl = item.url;
        this.selectedUuid = item.uuid;
        this.showModal = false;
        $refs.inputField.value = item.url;
    }
}">
    @if($label)
        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">{{ $label }}</label>
    @endif
    <div class="flex items-center gap-2">
        <input type="text" name="{{ $name }}" x-ref="inputField" value="{{ $value }}" class="flex-1 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-slate-800 dark:text-slate-200">
        <button type="button" @click="showModal = true; loadItems();" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition">Seç</button>
    </div>
    
    <template x-if="selectedUrl">
        <div class="mt-2 w-32 h-32 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 flex items-center justify-center relative">
            <img :src="selectedUrl" class="w-full h-full object-cover">
            <button type="button" @click="selectedUrl = ''; $refs.inputField.value = '';" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full text-[10px] hover:bg-red-600">×</button>
        </div>
    </template>

    <!-- Modal Explorer overlay -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 max-w-3xl w-full max-h-[80vh] flex flex-col p-6 shadow-premium">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <h4 class="text-sm font-bold text-slate-950 dark:text-white">Dosya Seçici</h4>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>
            
            <div class="flex gap-2 mb-4">
                <input type="text" x-model="searchQuery" @input.debounce="loadItems()" placeholder="Dosya adı ara..." class="flex-1 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5">
            </div>

            <!-- List Grid -->
            <div class="flex-1 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 pb-4">
                <template x-for="item in items" :key="item.id">
                    <div @click="selectMedia(item)" class="border border-slate-200 dark:border-slate-700 rounded-lg p-2 flex flex-col items-center justify-center cursor-pointer hover:border-primary transition bg-slate-50 dark:bg-slate-800/40 relative">
                        <template x-if="item.mime_type.startsWith('image/')">
                            <img :src="item.thumb" class="w-16 h-16 object-cover rounded">
                        </template>
                        <template x-if="!item.mime_type.startsWith('image/')">
                            <div class="w-16 h-16 flex items-center justify-center bg-slate-100 dark:bg-slate-800 rounded text-slate-400 font-bold uppercase text-[10px]" x-text="item.mime_type.split('/')[1] || 'file'"></div>
                        </template>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400 text-center truncate w-full mt-1.5" x-text="item.title"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
