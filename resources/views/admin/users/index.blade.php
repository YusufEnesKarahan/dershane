@extends('layouts.admin')
@section('title', 'Kullanıcı Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Kullanıcı Yönetimi" description="Sistemdeki kullanıcıları, rollerini ve şubelerini yönetin.">
        <x-slot name="actions">
            @permission('users.create')
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Yeni Kullanıcı Ekle
                </a>
            @endpermission
        </x-slot>

        <!-- Filtreler -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
            </div>
            <div>
                <select name="role" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                    <option value="">Tüm Roller</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                    <option value="">Tüm Durumlar</option>
                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                    <option value="PASSIVE" {{ request('status') == 'PASSIVE' ? 'selected' : '' }}>Pasif</option>
                    <option value="SUSPENDED" {{ request('status') == 'SUSPENDED' ? 'selected' : '' }}>Askıda</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-lg transition">
                    Filtrele
                </button>
            </div>
        </form>

        <!-- Toplu İşlem Formu -->
        <form method="POST" action="{{ route('admin.users.bulk') }}">
            @csrf
            <div class="flex items-center gap-2 mb-4 bg-white dark:bg-neutral-900 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                <select name="bulk_action" required class="text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-1.5 text-neutral-800 dark:text-neutral-200">
                    <option value="">Seçilenlere Uygula...</option>
                    <option value="delete">Sil</option>
                    <option value="status_active">Aktif Yap</option>
                    <option value="status_passive">Pasif Yap</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-medium rounded-lg transition">
                    Uygula
                </button>
            </div>

            <!-- Tablo -->
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left"><input type="checkbox" onclick="let checkboxes = document.querySelectorAll('.user-checkbox'); checkboxes.forEach(c => c.checked = this.checked)"></th>
                    <x-admin.table.th>İsim</x-admin.table.th>
                    <x-admin.table.th>E-Posta</x-admin.table.th>
                    <x-admin.table.th>Telefon</x-admin.table.th>
                    <x-admin.table.th>Rol</x-admin.table.th>
                    <x-admin.table.th>Şube</x-admin.table.th>
                    <x-admin.table.th>Durum</x-admin.table.th>
                    <x-admin.table.th>İşlemler</x-admin.table.th>
                </x-slot>
                <x-slot name="body">
                    @forelse($users as $user)
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30">
                            <td class="px-6 py-4"><input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                            <x-admin.table.td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->getAvatarUrl() }}" class="w-8 h-8 rounded-full">
                                    <span class="font-medium text-neutral-900 dark:text-white">{{ $user->name }}</span>
                                </div>
                            </x-admin.table.td>
                            <x-admin.table.td>{{ $user->email }}</x-admin.table.td>
                            <x-admin.table.td>{{ $user->phone ?? '-' }}</x-admin.table.td>
                            <x-admin.table.td>
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-0.5 text-xs font-semibold bg-primary/10 text-primary rounded-full">{{ $role->name }}</span>
                                @endforeach
                            </x-admin.table.td>
                            <x-admin.table.td>{{ $user->branch->name ?? 'Tüm Şubeler' }}</x-admin.table.td>
                            <x-admin.table.td>
                                @if($user->status->value === 'ACTIVE')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">Aktif</span>
                                @elseif($user->status->value === 'PASSIVE')
                                    <span class="px-2 py-1 text-xs font-medium bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-400 rounded-full">Pasif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">Askıda</span>
                                @endif
                            </x-admin.table.td>
                            <x-admin.table.td>
                                <div class="flex items-center gap-2">
                                    @permission('users.update')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-primary hover:text-primary-dark">Düzenle</a>
                                    @endpermission
                                    @permission('users.delete')
                                        @if(auth()->id() !== $user->id)
                                            <button type="button" onclick="if(confirm('Silmek istediğinize emin misiniz?')) { document.getElementById('delete-user-{{ $user->id }}').submit(); }" class="text-red-500 hover:text-red-700 ml-2">Sil</button>
                                        @endif
                                    @endpermission
                                </div>
                            </x-admin.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-neutral-400">Kullanıcı bulunamadı.</td>
                        </tr>
                    @endforelse
                </x-slot>
                <x-slot name="pagination">
                    {{ $users->links() }}
                </x-slot>
            </x-admin.table.layout>
        </form>

        @foreach($users as $user)
            @permission('users.delete')
                <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endpermission
        @endforeach
    </x-admin.crud.index-layout>
@endsection
