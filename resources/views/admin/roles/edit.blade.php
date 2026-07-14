@extends('layouts.admin')
@section('title', 'Rol Düzenle')
@section('content')
    <x-admin.crud.index-layout title="Rol Düzenle" description="{{ $role->name }} rolünü düzenleyin.">
        <div x-data="{ 
            applyPreset(presetName) {
                let checkBoxes = document.querySelectorAll('.matrix-checkbox');
                checkBoxes.forEach(c => c.checked = false);
                
                if (presetName === 'FullAccess') {
                    checkBoxes.forEach(c => c.checked = true);
                } else if (presetName === 'ReadOnly') {
                    checkBoxes.forEach(c => {
                        if (c.dataset.action === 'view') c.checked = true;
                    });
                } else if (presetName === 'CRUD' || presetName === 'Management') {
                    checkBoxes.forEach(c => {
                        let act = c.dataset.action;
                        if (act === 'view' || act === 'create' || act === 'update' || act === 'delete') {
                            c.checked = true;
                        }
                    });
                }
            }
        }">
            <x-admin.form.layout :action="route('admin.roles.update', $role)" method="PUT">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Sol Taraf: Temel Bilgiler & Yetki Matrisi -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <x-admin.form.field-group label="Rol Adı" id="name" :error="$errors->first('name')">
                                <input type="text" name="name" id="name" value="{{ $role->name }}" required {{ $role->isSystemRole() ? 'readonly' : '' }} class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Açıklama" id="description" :error="$errors->first('description')">
                                <textarea name="description" id="description" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-24">{{ $role->description }}</textarea>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Renk" id="color" :error="$errors->first('color')">
                                <input type="color" name="color" id="color" value="{{ $role->color ?? '#6366f1' }}" class="w-16 h-8 bg-neutral-50 border border-neutral-200 dark:border-neutral-700 rounded cursor-pointer">
                            </x-admin.form.field-group>
                        </div>

                        <!-- Yetki Matrisi Accordion -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4" x-data="{ activeGroup: null }">
                            <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-3">
                                <h3 class="text-md font-semibold text-neutral-900 dark:text-white">Yetki Matrisi</h3>
                                <!-- Presets -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-neutral-400 mr-2">Şablonlar:</span>
                                    <button type="button" @click="applyPreset('CRUD')" class="px-2 py-1 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 text-xs rounded">CRUD</button>
                                    <button type="button" @click="applyPreset('ReadOnly')" class="px-2 py-1 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 text-xs rounded">Salt Okunur</button>
                                    <button type="button" @click="applyPreset('FullAccess')" class="px-2 py-1 bg-primary/10 text-primary hover:bg-primary/20 text-xs font-semibold rounded">Tam Yetki</button>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @php $index = 0; @endphp
                                @foreach($matrix as $groupLabel => $perms)
                                    <div class="border border-neutral-100 dark:border-neutral-800 rounded-xl overflow-hidden">
                                        <button type="button" @click="activeGroup = (activeGroup === {{ $index }} ? null : {{ $index }})" class="w-full flex items-center justify-between bg-neutral-50 dark:bg-neutral-800/40 p-4 text-left font-medium text-neutral-800 dark:text-neutral-200">
                                            <span>{{ $groupLabel }}</span>
                                            <svg class="w-4 h-4 transform transition-transform" :class="activeGroup === {{ $index }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <div x-show="activeGroup === {{ $index }}" class="p-4 bg-white dark:bg-neutral-900 border-t border-neutral-100 dark:border-neutral-800 grid grid-cols-1 md:grid-cols-3 gap-4" x-transition>
                                            @foreach($perms as $perm)
                                                @php
                                                    $act = explode('.', $perm['name'])[1] ?? '';
                                                    $fullPerm = $permissions->firstWhere('name', $perm['name']);
                                                @endphp
                                                @if($fullPerm)
                                                    <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300 cursor-pointer">
                                                        <input type="checkbox" name="permissions[]" value="{{ $fullPerm->id }}" data-action="{{ $act }}" {{ $perm['checked'] ? 'checked' : '' }} class="matrix-checkbox rounded text-primary focus:ring-primary border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800">
                                                        <span>{{ $perm['label'] }}</span>
                                                    </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @php $index++; @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf: Eylemler, Menü Önizleme & Effective Permissions -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                Değişiklikleri Güncelle
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="w-full block text-center px-4 py-2 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl text-neutral-600 dark:text-neutral-300 transition">
                                Vazgeç
                            </a>
                        </div>

                        <!-- Effective Permissions Summary -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-3">
                            <h4 class="text-sm font-bold text-neutral-900 dark:text-white">Etkin Yetkiler</h4>
                            <p class="text-xs text-neutral-500">Bu rolün sahip olduğu toplam yetki sayısı:</p>
                            <div class="text-3xl font-extrabold text-primary">{{ $role->permissions->count() }}</div>
                            <div class="border-t border-neutral-100 dark:border-neutral-800 pt-3">
                                <ul class="text-xs space-y-1 text-neutral-600 dark:text-neutral-400">
                                    <li>• {{ $role->permissions->filter(fn($p) => str_starts_with($p->name, 'users.'))->count() }} Kullanıcı yetkisi</li>
                                    <li>• {{ $role->permissions->filter(fn($p) => str_starts_with($p->name, 'blogs.'))->count() }} CMS yazısı yetkisi</li>
                                    <li>• {{ $role->permissions->filter(fn($p) => str_starts_with($p->name, 'courses.'))->count() }} Kurs yetkisi</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sidebar Menu Preview -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-3">
                            <h4 class="text-sm font-bold text-neutral-900 dark:text-white">Sidebar Önizleme</h4>
                            <p class="text-xs text-neutral-500">Bu role atanan kullanıcının görebileceği olası menüler:</p>
                            <div class="bg-neutral-50 dark:bg-neutral-800/40 p-3 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-2">
                                @php
                                    $menus = app(\App\Domain\Auth\Services\AdminMenuService::class)->getSidebarMenu();
                                @endphp
                                @foreach($menus as $menu)
                                    <div class="flex items-center gap-2 text-xs font-semibold text-neutral-800 dark:text-neutral-200">
                                        <span class="w-2 h-2 rounded-full bg-primary/70"></span>
                                        <span>{{ $menu['title'] }}</span>
                                    </div>
                                    @if(isset($menu['children']))
                                        <div class="pl-4 space-y-1">
                                            @foreach($menu['children'] as $child)
                                                <div class="text-[10px] text-neutral-500">• {{ $child['title'] }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
