@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral">Yoklama Oturumlarım ({{ $date }})</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Sınıf</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Saat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($sessions as $session)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ $session->classroom->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $session->start_time ?? '-' }} / {{ $session->end_time ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $session->status == 'completed' ? 'green' : 'yellow' }}-100 text-{{ $session->status == 'completed' ? 'green' : 'yellow' }}-800">
                            {{ ucfirst($session->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('teacher.attendance.show', $session) }}" class="text-primary hover:text-primary/80">Yoklama Al / İncele</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-neutral-500">Bugün için yoklama oturumunuz bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
