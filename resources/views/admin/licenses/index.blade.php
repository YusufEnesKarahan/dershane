@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Platform Lisans Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Dershane lisanslarının manuel aktifleştirilmesi, süre uzatılması ve askıya alınması</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-slate-200">
        @if($licenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-slate-600">Lisans Anahtarı / Şube</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-600">Paket</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-600">Başlangıç</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-600">Bitiş</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-600">Durum</th>
                            <th class="px-6 py-3 text-right font-semibold text-slate-600">Manuel İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach($licenses as $lic)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">
                                    {{ $lic->license_key }}
                                    @if($lic->subscription && $lic->subscription->branch)
                                        <span class="block text-xs text-blue-600 font-semibold">{{ $lic->subscription->branch->name }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    {{ $lic->planModel ? $lic->planModel->name : ($lic->plan ?? 'Standart') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    {{ $lic->starts_at ? $lic->starts_at->format('d.m.Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    {{ $lic->expires_at ? $lic->expires_at->format('d.m.Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $lic->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ strtoupper($lic->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if(!$lic->isActive())
                                        <form method="POST" action="{{ route('admin.licenses.activate', $lic->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white py-1 px-2.5 rounded">Aktif Et</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.licenses.renew', $lic->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="days" value="365">
                                        <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-2.5 rounded">+1 Yıl Uzat</button>
                                    </form>

                                    @if($lic->status !== 'suspended')
                                        <form method="POST" action="{{ route('admin.licenses.suspend', $lic->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs bg-yellow-600 hover:bg-yellow-700 text-white py-1 px-2.5 rounded">Askıya Al</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $licenses->links() }}
            </div>
        @else
            <x-admin.empty-state
                title="Henüz lisans kaydı bulunmuyor"
                message="Sistemde tanımlı dershane lisansı bulunamadı."
            />
        @endif
    </div>
</div>
@endsection
