@extends('layouts.admin')
@section('title', 'Duyurular & Bildirimler')
@section('content')
    <x-admin.crud.index-layout title="Duyurular" description="Öğrenci, Veli ve Personele yönelik duyuru ve bildirimleri yönetin.">
        <x-slot name="actions">
            <!-- Modal Trigger Button for Create -->
            <button type="button" onclick="document.getElementById('createAnnouncementModal').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Duyuru
            </button>
        </x-slot>

        <div class="mb-6 bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form action="{{ route('admin.announcements.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <select name="status" class="px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Yayınlandı</option>
                </select>
                <button type="submit" class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-100 transition-colors">
                    Filtrele
                </button>
            </form>
        </div>

        @if($announcements->count() > 0)
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Başlık</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tip</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Hedef Kitle</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
                </x-slot>
                <x-slot name="body">
                    @foreach($announcements as $announcement)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-neutral-900 dark:text-white">{{ $announcement->title }}</div>
                                <div class="text-xs text-neutral-500 mt-1">{{ Str::limit($announcement->content, 50) }}</div>
                                <div class="text-xs text-neutral-400 mt-1"><i class="fas fa-clock mr-1"></i> {{ $announcement->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $announcement->type === 'system' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $announcement->type === 'announcement' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $announcement->type === 'absence' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $announcement->type === 'payment' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                ">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $announcement->target_role ?? 'Herkese Açık' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($announcement->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <i class="fas fa-check-circle mr-1"></i> Yayınlandı
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-400">
                                        <i class="fas fa-edit mr-1"></i> Taslak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($announcement->status === 'draft')
                                        <form action="{{ route('admin.announcements.publish', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu duyuruyu yayınlamak istediğinize emin misiniz? Yayınlandıktan sonra tüm hedef kitleye bildirim gidecektir.');">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-colors tooltip" data-tip="Yayınla">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors tooltip" data-tip="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
                
                @if(method_exists($announcements, 'links'))
                    <x-slot name="pagination">
                        {{ $announcements->links() }}
                    </x-slot>
                @endif
            </x-admin.table.layout>
        @else
            <x-admin.empty-state 
                title="Duyuru Bulunamadı" 
                description="Sistemde henüz bir duyuru bulunmuyor."
                actionText="Yeni Duyuru Oluştur"
                actionRoute="javascript:document.getElementById('createAnnouncementModal').classList.remove('hidden')"
                icon="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
            />
        @endif
    </x-admin.crud.index-layout>

    <!-- Create Announcement Modal -->
    <div id="createAnnouncementModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-neutral-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('createAnnouncementModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-neutral-200 dark:border-neutral-800">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-6" id="modal-title">Yeni Duyuru Oluştur</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Başlık <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" required class="w-full px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Tip <span class="text-rose-500">*</span></label>
                                <select name="type" required class="w-full px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="announcement">Genel Duyuru</option>
                                    <option value="system">Sistem Mesajı</option>
                                    <option value="absence">Yoklama Bilgilendirmesi</option>
                                    <option value="payment">Ödeme Hatırlatması</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Hedef Kitle</label>
                                <select name="target_role" class="w-full px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Herkese Açık (Tüm Kullanıcılar)</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-neutral-500 mt-1">Belirli bir role sahip kullanıcıları hedefler. Boş bırakılırsa tüm kullanıcılara gönderilir.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">İçerik <span class="text-rose-500">*</span></label>
                                <textarea name="content" required rows="5" class="w-full px-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-xl bg-neutral-50 dark:bg-neutral-800 text-sm focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="document.getElementById('createAnnouncementModal').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                            İptal
                        </button>
                        <button type="submit" name="save_draft" value="1" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                            Taslak Kaydet
                        </button>
                        <button type="submit" name="publish" value="1" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm flex justify-center items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Kaydet ve Yayınla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
