@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sistem Aktivite & Denetim Logları</h1>
            <p class="text-sm text-gray-500 mt-1">Platform genelinde gerçekleştirilen işlemler ve güvenlik kayıtları</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 border border-gray-200">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kullanıcı</label>
                <select name="user_id" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tüm Kullanıcılar</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">İşlem Tipi</label>
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Örn: update, create, delete" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tarih</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">Filtrele</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm transition">Temizle</a>
            </div>
        </form>
    </div>

    <!-- Log Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Kullanıcı</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">İşlem (Action)</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Model / Hedef</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Detay / Meta</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $log->user ? $log->user->name : 'Sistem / Anonim' }}
                                    @if($log->user)
                                        <span class="block text-xs text-gray-500">{{ $log->user->email }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $log->target_type ? class_basename($log->target_type) : '-' }}
                                    @if($log->target_id)
                                        <span class="text-xs text-gray-400">#{{ $log->target_id }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {{ is_array($log->metadata) ? json_encode($log->metadata, JSON_UNESCAPED_UNICODE) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at ? $log->created_at->format('d.m.Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @else
            <x-admin.empty-state
                title="Henüz log kaydı bulunmuyor"
                message="Filtrelere uygun aktivite log kaydı bulunamadı."
            />
        @endif
    </div>
</div>
@endsection
