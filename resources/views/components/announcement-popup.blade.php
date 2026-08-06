@props([])

@php
    $cmsService = app(\App\Domain\Notification\Services\AnnouncementCmsService::class);
    $popupAnnouncement = $cmsService->getPopupForUser(auth()->user());
@endphp

@if($popupAnnouncement)
<div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-neutral-900/75 backdrop-blur-sm transition-opacity" @click="closePopup({{ $popupAnnouncement->id }})"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Box -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-neutral-100 dark:border-neutral-800">
            
            @if($popupAnnouncement->cover_image)
                <img src="{{ asset('storage/' . $popupAnnouncement->cover_image) }}" alt="Cover" class="w-full h-48 object-cover">
            @endif

            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            {{ $popupAnnouncement->category?->name ?? 'Önemli Duyuru' }}
                        </span>
                        <h3 class="text-xl font-black text-neutral-900 dark:text-white mt-2" id="modal-title">
                            {{ $popupAnnouncement->title }}
                        </h3>
                    </div>
                    <button type="button" @click="closePopup({{ $popupAnnouncement->id }})" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                @if($popupAnnouncement->summary)
                    <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 leading-relaxed">
                        {{ $popupAnnouncement->summary }}
                    </p>
                @endif

                <div class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed prose dark:prose-invert max-h-60 overflow-y-auto">
                    {!! nl2br(e($popupAnnouncement->content)) !!}
                </div>

                @if($popupAnnouncement->attachments->count() > 0)
                    <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ek Dosyalar</div>
                        <div class="space-y-1">
                            @foreach($popupAnnouncement->attachments as $att)
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="flex items-center gap-2 p-2 bg-neutral-50 dark:bg-neutral-800 rounded-xl text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <i class="fas fa-file-download"></i> {{ $att->file_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-neutral-50 dark:bg-neutral-800/60 px-6 py-4 flex justify-end">
                <button type="button" @click="closePopup({{ $popupAnnouncement->id }})" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                    Anladım, Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function closePopup(announcementId) {
        fetch('/admin/announcements/' + announcementId + '/popup-seen', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(() => {
            window.location.reload();
        }).catch(() => {
            window.location.reload();
        });
    }
</script>
@endif
