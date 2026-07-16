@extends('layouts.admin')
@section('title', 'Sayfa Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Sayfa Yönetimi" description="Kurumsal web sitenizin sayfalarını, hiyerarşisini ve SEO ayarlarını yönetin.">
        <x-slot name="actions">
            @permission('pages.create')
                <a href="{{ route('admin.pages.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Yeni Sayfa Ekle
                </a>
            @endpermission
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sol Panel: Sayfa Ağaç Yapısı (Tree View) -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Sayfa Ağacı</h3>
                <div class="space-y-2">
                    @forelse($tree as $item)
                        <div class="text-xs space-y-1">
                            <div class="flex items-center justify-between p-2 bg-neutral-50 dark:bg-neutral-800/40 rounded-lg">
                                <span class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $item['title'] }}</span>
                                <span class="text-[10px] text-neutral-400">/{{ $item['slug'] }}</span>
                            </div>
                            @if(!empty($item['children']))
                                <div class="pl-4 border-l border-neutral-100 dark:border-neutral-800 space-y-1 mt-1">
                                    @foreach($item['children'] as $child)
                                        <div class="flex items-center justify-between p-1 text-[11px] text-neutral-600 dark:text-neutral-400">
                                            <span>├ {{ $child['title'] }}</span>
                                            <span class="text-[9px] text-neutral-400">/{{ $child['slug'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-neutral-400">Ağaç yapısı boş.</div>
                    @endforelse
                </div>
            </div>

            <!-- Sağ Panel: Liste -->
            <div class="lg:col-span-3">
                <!-- Filtreler -->
                <form method="GET" action="{{ route('admin.pages.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 mb-6">
                    <div class="md:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Sayfa başlığı veya slug ara..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                    </div>
                    <div>
                        <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            <option value="">Tüm Durumlar</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Yayında</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arşivlendi</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-lg transition">
                            Filtrele
                        </button>
                    </div>
                </form>

                <!-- Toplu İşlem Formu -->
                <form method="POST" action="{{ route('admin.pages.bulk') }}">
                    @csrf
                    <div class="flex items-center gap-2 mb-4 bg-white dark:bg-neutral-900 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                        <select name="bulk_action" required class="text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-1.5 text-neutral-800 dark:text-neutral-200">
                            <option value="">Seçilenlere Uygula...</option>
                            <option value="publish">Yayınla</option>
                            <option value="archive">Arşivle</option>
                            <option value="delete">Sil</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-medium rounded-lg transition">
                            Uygula
                        </button>
                    </div>

                    <!-- Tablo -->
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-6 py-3 text-left"><input type="checkbox" onclick="let checkboxes = document.querySelectorAll('.page-checkbox'); checkboxes.forEach(c => c.checked = this.checked)"></th>
                            <x-admin.table.th>Sayfa Başlığı</x-admin.table.th>
                            <x-admin.table.th>Slug</x-admin.table.th>
                            <x-admin.table.th>Üst Sayfa</x-admin.table.th>
                            <x-admin.table.th>Durum</x-admin.table.th>
                            <x-admin.table.th>Şablon</x-admin.table.th>
                            <x-admin.table.th>İşlemler</x-admin.table.th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($pages as $page)
                                <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30">
                                    <td class="px-6 py-4">
                                        @if(!$page->isSystemPage() && !$page->isHomepage())
                                            <input type="checkbox" name="ids[]" value="{{ $page->id }}" class="page-checkbox">
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-700">-</span>
                                        @endif
                                    </td>
                                    <x-admin.table.td>
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-neutral-900 dark:text-white">{{ $page->title }}</span>
                                            @if($page->isHomepage())
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-primary/10 text-primary rounded-full">Ana Sayfa</span>
                                            @endif
                                            @if($page->isSystemPage())
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300 rounded-full">Sistem</span>
                                            @endif
                                        </div>
                                    </x-admin.table.td>
                                    <x-admin.table.td>/{{ $page->slug }}</x-admin.table.td>
                                    <x-admin.table.td>{{ $page->parent?->title ?? '-' }}</x-admin.table.td>
                                    <x-admin.table.td>
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                            {{ $page->status->value === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300' : '' }}
                                            {{ $page->status->value === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300' : '' }}
                                            {{ $page->status->value === 'archived' ? 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300' : '' }}
                                            {{ $page->status->value === 'review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                                        ">
                                            {{ $page->status->label() }}
                                        </span>
                                    </x-admin.table.td>
                                    <x-admin.table.td>{{ ucfirst($page->template ?? 'default') }}</x-admin.table.td>
                                    <x-admin.table.td>
                                        <div class="flex items-center gap-2 text-sm">
                                            <a href="{{ route('admin.pages.preview', $page) }}" class="text-neutral-500 hover:text-neutral-700 dark:hover:text-white">Önizle</a>
                                            @permission('pages.update')
                                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-primary hover:text-primary-dark ml-2">Düzenle</a>
                                            @endpermission
                                            @permission('pages.create')
                                                <button type="button" onclick="document.getElementById('duplicate-page-{{ $page->id }}').submit();" class="text-green-500 hover:text-green-700 ml-2">Kopyala</button>
                                            @endpermission
                                            @permission('pages.delete')
                                                @if(!$page->isSystemPage() && !$page->isHomepage())
                                                    <button type="button" onclick="if(confirm('Sayfayı silmek istediğinize emin misiniz?')) { document.getElementById('delete-page-{{ $page->id }}').submit(); }" class="text-red-500 hover:text-red-700 ml-2">Sil</button>
                                                @endif
                                            @endpermission
                                        </div>
                                    </x-admin.table.td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-neutral-400">Sayfa bulunamadı.</td>
                                </tr>
                            @endforelse
                        </x-slot>
                        <x-slot name="pagination">
                            {{ $pages->links() }}
                        </x-slot>
                    </x-admin.table.layout>
                </form>

                @foreach($pages as $page)
                    @permission('pages.create')
                        <form id="duplicate-page-{{ $page->id }}" method="POST" action="{{ route('admin.pages.duplicate', $page) }}" class="hidden">
                            @csrf
                        </form>
                    @endpermission
                    @if(!$page->isSystemPage() && !$page->isHomepage())
                        @permission('pages.delete')
                            <form id="delete-page-{{ $page->id }}" method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endpermission
                    @endif
                @endforeach
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
