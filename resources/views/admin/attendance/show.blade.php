@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral">Yoklama Detayı</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-neutral mb-4">Oturum Bilgileri</h3>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-neutral-500">Sınıf</dt>
                <dd class="mt-1 text-sm text-neutral-900">{{ $session->classroom->name }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-neutral-500">Öğretmen</dt>
                <dd class="mt-1 text-sm text-neutral-900">{{ $session->teacher->first_name }} {{ $session->teacher->last_name }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-neutral-500">Tarih</dt>
                <dd class="mt-1 text-sm text-neutral-900">{{ $session->session_date->format('d.m.Y') }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-neutral-500">Durum</dt>
                <dd class="mt-1 text-sm text-neutral-900">{{ ucfirst($session->status) }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Not</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @if(isset($session->records) && $session->records->count() > 0)
                    @foreach($session->records as $record)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ $record->student->first_name ?? 'Bilinmeyen' }} {{ $record->student->last_name ?? 'Öğrenci' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->status == 'present')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Var</span>
                            @elseif($record->status == 'absent')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Yok</span>
                            @elseif($record->status == 'late')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Geç</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">İzinli</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">{{ $record->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-neutral-500">Henüz yoklama alınmamış.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
