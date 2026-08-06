@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate">Yoklama Durumunuz</h1>
    </div>

    <!-- Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-primary">
            <h3 class="text-sm font-medium text-slate-500">Toplam Ders</h3>
            <p class="text-2xl font-bold text-slate">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-slate-500">Katılım (Var)</h3>
            <p class="text-2xl font-bold text-green-600">{{ $stats['present'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
            <h3 class="text-sm font-medium text-slate-500">Devamsızlık (Yok)</h3>
            <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-slate-500">Geç Kalma</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['late'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Yoklama Kayıtları Listesi -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-medium text-slate-900">Geçmiş Yoklamalar</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ders / Sınıf</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Öğretmen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($records as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $record->session->session_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $record->session->classroom->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $record->session->teacher->first_name }} {{ $record->session->teacher->last_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($record->status == 'present')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Var</span>
                        @elseif($record->status == 'absent')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Yok</span>
                        @elseif($record->status == 'late')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Geç</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">İzinli</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500">Henüz yoklama kaydınız bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
