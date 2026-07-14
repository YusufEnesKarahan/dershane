@extends('layouts.admin')
@section('title', 'Rol İnceleme')
@section('content')
    <x-admin.crud.index-layout title="Rol İnceleme" description="Rol yetkilerini ve kullanan kullanıcıları görüntüleyin.">
        <x-slot name="actions">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl transition">Geri Dön</a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Panel: İzinler -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                    <h3 class="text-md font-bold text-neutral-900 dark:text-white mb-4">Sahip Olduğu İzinler</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($role->permissions as $perm)
                            <div class="flex items-center gap-2 bg-neutral-50 dark:bg-neutral-800/40 px-3 py-2 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                <span class="text-xs text-neutral-800 dark:text-neutral-200">{{ $perm->name }}</span>
                            </div>
                        @empty
                            <div class="col-span-2 text-neutral-400 text-sm">Bu role henüz izin atanmamış.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Kullanan Kullanıcılar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Bu Rolü Kullanan Kullanıcılar</h3>
                    <div class="text-2xl font-extrabold text-primary">{{ $userCount }} Kullanıcı</div>
                    <div class="space-y-2 border-t border-neutral-100 dark:border-neutral-800 pt-3">
                        @forelse($users as $u)
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->getAvatarUrl() }}" class="w-8 h-8 rounded-full">
                                <span class="text-xs font-semibold text-neutral-800 dark:text-neutral-200">{{ $u->name }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-neutral-400">Bu role atanmış hiçbir kullanıcı bulunamadı.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
