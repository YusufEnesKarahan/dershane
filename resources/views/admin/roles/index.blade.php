@extends('layouts.admin')
@section('title', 'Rol Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Rol Yönetimi" description="Sistemdeki kullanıcı rollerini, yetki matrislerini ve şablonlarını yönetin.">
        <x-slot name="actions">
            @permission('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Yeni Rol Ekle
                </a>
            @endpermission
        </x-slot>

        <!-- Filtreler -->
        <form method="GET" action="{{ route('admin.roles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rol adı veya açıklama ara..." class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <x-admin.button type="submit" variant="secondary" class="w-full">
                    Filtrele
                </x-admin.button>
            </div>
        </form>

        <!-- Toplu İşlem Formu -->
        <form method="POST" action="{{ route('admin.roles.bulk') }}">
            @csrf
            <div class="flex items-center gap-2 mb-4 bg-white dark:bg-slate-900 p-3 rounded-lg border border-slate-100 dark:border-slate-800">
                <select name="bulk_action" required class="text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-800 dark:text-slate-200">
                    <option value="">Seçilenlere Uygula...</option>
                    <option value="delete">Sil</option>
                </select>
                <x-admin.button type="submit" variant="secondary" size="sm">
                    Uygula
                </x-admin.button>
            </div>

            <!-- Tablo -->
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left"><input type="checkbox" onclick="let checkboxes = document.querySelectorAll('.role-checkbox'); checkboxes.forEach(c => c.checked = this.checked)"></th>
                    <x-admin.table.th>Rol Adı</x-admin.table.th>
                    <x-admin.table.th>Açıklama</x-admin.table.th>
                    <x-admin.table.th>Yetki Sayısı</x-admin.table.th>
                    <x-admin.table.th>Kullanıcı Sayısı</x-admin.table.th>
                    <x-admin.table.th>Renk</x-admin.table.th>
                    <x-admin.table.th>Tarih</x-admin.table.th>
                    <x-admin.table.th>İşlemler</x-admin.table.th>
                </x-slot>
                <x-slot name="body">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4">
                                @if(!$role->isSystemRole())
                                    <input type="checkbox" name="ids[]" value="{{ $role->id }}" class="role-checkbox">
                                @else
                                    <span class="text-slate-300 dark:text-slate-700">-</span>
                                @endif
                            </td>
                            <x-admin.table.td>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $role->name }}</span>
                                    @if($role->isSystemRole())
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 rounded-full border border-slate-200 dark:border-slate-700">Sistem</span>
                                    @endif
                                </div>
                            </x-admin.table.td>
                            <x-admin.table.td>{{ $role->description ?? 'Açıklama girilmemiş.' }}</x-admin.table.td>
                            <x-admin.table.td>{{ $role->permissions_count }} Yetki</x-admin.table.td>
                            <x-admin.table.td>{{ $role->users_count }} Kullanıcı</x-admin.table.td>
                            <x-admin.table.td>
                                <span class="w-4 h-4 rounded-full inline-block" style="background-color: {{ $role->color ?? '#6366f1' }}"></span>
                            </x-admin.table.td>
                            <x-admin.table.td>{{ $role->created_at->format('d.m.Y H:i') }}</x-admin.table.td>
                            <x-admin.table.td>
                                <div class="flex items-center gap-2 text-sm">
                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-slate-500 hover:text-slate-700 dark:hover:text-white">İncele</a>
                                    @permission('roles.update')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary hover:text-primary-dark ml-2">Düzenle</a>
                                    @endpermission
                                    @permission('roles.create')
                                        <a href="{{ route('admin.roles.showClone', $role) }}" class="text-green-500 hover:text-green-700 ml-2">Klonla</a>
                                    @endpermission
                                    @permission('roles.delete')
                                        @if(!$role->isSystemRole())
                                            <x-admin.delete-modal action="{{ route('admin.roles.destroy', $role) }}" title="Rolü Sil" message="Bu rolü silmek istediğinize emin misiniz?" />
                                        @endif
                                    @endpermission
                                </div>
                            </x-admin.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4">
                                <x-admin.empty-state title="Rol Bulunamadı" description="Arama kriterlerinize uygun veya sistemde kayıtlı rol bulunamadı." />
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
                <x-slot name="pagination">
                    {{ $roles->links() }}
                </x-slot>
            </x-admin.table.layout>
        </form>

    </x-admin.crud.index-layout>
@endsection
