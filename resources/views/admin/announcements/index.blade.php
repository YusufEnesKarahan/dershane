@extends('layouts.admin')

@section('title', 'Duyuru ve Portal CMS Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-bullhorn text-emerald-600"></i> Duyuru & Portal CMS Yönetimi
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Öğrenci, veli, öğretmen portalları ve dashboard için yayınlanacak duyuru ve haberleri yönetin.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Duyuru Ekle
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Dashboard Widgetları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-lg">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Yayındaki Duyurular</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $widgetData['published_count'] }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-lg">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Taslak Sayısı</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $widgetData['draft_count'] }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Zamanlanmış Yayınlar</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ count($widgetData['upcoming']) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg">
                <i class="fas fa-th-list"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Toplam Duyuru</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $announcements->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Filtreleme ve Arama Çubuğu -->
    <form action="{{ route('admin.announcements.index') }}" method="GET" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Canlı Arama -->
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Başlık, özet, içerik veya kategoride ara..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                </div>
            </div>

            <!-- Kategori Filtresi -->
            <div>
                <select name="category_id" onchange="this.form.submit()" class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Durum Filtresi -->
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="Published" {{ request('status') === 'Published' ? 'selected' : '' }}>Yayında</option>
                    <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Zamanlanmış</option>
                    <option value="Archived" {{ request('status') === 'Archived' ? 'selected' : '' }}>Arşivde</option>
                </select>
            </div>

            <!-- Filtre Sıfırla -->
            <div>
                <a href="{{ route('admin.announcements.index') }}" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm flex items-center justify-center gap-2 transition-colors">
                    <i class="fas fa-undo"></i> Filtreleri Temizle
                </a>
            </div>
        </div>
    </form>

    <!-- Duyuru Listesi Tablosu -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase text-slate-500 tracking-wider">
                        <th class="px-6 py-4">Başlık & Kategori</th>
                        <th class="px-6 py-4">Hedef & Şube</th>
                        <th class="px-6 py-4">Öncelik / Mod</th>
                        <th class="px-6 py-4">Tarih / Yayın</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($announcements as $ann)
                        @php
                            $statusColors = [
                                'Published' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'published' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'Draft' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'draft' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'Scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                'Archived' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    @if($ann->cover_image)
                                        <img src="{{ asset('storage/' . $ann->cover_image) }}" alt="Cover" class="w-12 h-12 object-cover rounded-xl border border-slate-200">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg">
                                            <i class="fas {{ $ann->category?->icon ?? 'fa-bullhorn' }}"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            {{ $ann->title }}
                                            @if($ann->is_pinned)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300" title="Sabitlenmiş Duyuru">
                                                    <i class="fas fa-thumbtack mr-0.5"></i> Sabit
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($ann->category)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                    {{ $ann->category->name }}
                                                </span>
                                            @endif
                                            @if($ann->attachments->count() > 0)
                                                <span class="text-xs text-slate-400"><i class="fas fa-paperclip mr-1"></i> {{ $ann->attachments->count() }} Ek</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        Hedef: <span class="capitalize">{{ $ann->target_role ?: 'Tümü' }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        {{ $ann->is_all_branches ? 'Tüm Şubeler' : ($ann->branch?->name ?? 'Şube Özel') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($ann->is_popup)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            <i class="fas fa-window-restore mr-1"></i> Popup Modu
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">Standart</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-500 font-mono">
                                    <div>Yayın: {{ $ann->published_at ? $ann->published_at->format('d.m.Y H:i') : ($ann->publish_at ? $ann->publish_at->format('d.m.Y H:i') : '-') }}</div>
                                    @if($ann->expire_at)
                                        <div class="text-rose-500">Bitiş: {{ $ann->expire_at->format('d.m.Y') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColors[$ann->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $ann->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.announcements.edit', $ann->id) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($ann->status !== 'Published')
                                        <form action="{{ route('admin.announcements.publish', $ann->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Yayınla">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Duyuruyu silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 italic">
                                Seçilen filtrelere uygun duyuru bulunamadı. Yeni bir duyuru ekleyerek yayınlayabilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
