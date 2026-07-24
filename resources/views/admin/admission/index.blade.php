@extends('layouts.admin')
@section('title', 'Ön Kayıt Başvuruları (Admission)')
@section('content')
    <x-admin.crud.index-layout title="Ön Kayıt Yönetimi" description="Yeni öğrenci başvuru ve ön kayıt işlemlerini yönetin, CRM leadlerini aday kaydına dönüştürün.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Hızlı Ön Kayıt Ekle / Lead Dönüştür -->
            <div class="space-y-6">
                
                <!-- Lead'den Dönüştür -->
                @if(count($leads) > 0)
                    <div class="bg-gradient-to-br from-indigo-900 to-slate-900 p-6 rounded-2xl text-white shadow-premium space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-300">CRM Lead Kaydından Dönüştür</h3>
                        <form method="POST" action="" id="convertLeadForm">
                            @csrf
                            <div class="space-y-3">
                                <select id="leadSelect" required class="w-full text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                    <option value="">Aday Öğrenci Seçin</option>
                                    @foreach($leads as $ld)
                                        <option value="{{ $ld->id }}">{{ $ld->first_name }} {{ $ld->last_name }} ({{ $ld->phone }})</option>
                                    @endforeach
                                </select>

                                <button type="button" onclick="submitConvert()" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-xs font-bold rounded-xl transition shadow-md">
                                    Ön Kayıta Dönüştür
                                </button>
                            </div>
                        </form>
                        <script>
                            function submitConvert() {
                                var val = document.getElementById('leadSelect').value;
                                if (!val) return alert('Lütfen bir aday öğrenci seçin.');
                                var form = document.getElementById('convertLeadForm');
                                form.action = '/admin/admission/convert/' + val;
                                form.submit();
                            }
                        </script>
                    </div>
                @endif

                <!-- Manuel Ön Kayıt Formu -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Manuel Ön Kayıt Oluştur</h3>
                    
                    <form method="POST" action="{{ route('admin.admission.store') }}" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-admin.form.field-group label="Adı" id="first_name">
                                <input type="text" name="first_name" required placeholder="Ali" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            </x-admin.form.field-group>
                            
                            <x-admin.form.field-group label="Soyadı" id="last_name">
                                <input type="text" name="last_name" required placeholder="Kaya" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            </x-admin.form.field-group>
                        </div>

                        <x-admin.form.field-group label="Telefon No" id="phone">
                            <input type="text" name="phone" required placeholder="0555 555 5555" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="T.C. Kimlik No" id="tc_no">
                            <input type="text" name="tc_no" placeholder="11 haneli TC No" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Program / Alan" id="program">
                            <input type="text" name="program" placeholder="Örn: YKS Eşit Ağırlık" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <div class="grid grid-cols-2 gap-4">
                            <x-admin.form.field-group label="Toplam Ücret (₺)" id="total_amount">
                                <input type="number" step="0.01" name="total_amount" placeholder="45000" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Kapora/Peşinat (₺)" id="deposit_amount">
                                <input type="number" step="0.01" name="deposit_amount" placeholder="5000" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            </x-admin.form.field-group>
                        </div>

                        <x-admin.form.field-group label="Hedef Şube" id="branch_id">
                            <select name="branch_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                <option value="">Şube Seçin</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Ön Kaydı Başlat
                        </button>
                    </form>
                </div>

            </div>

            <!-- Sağ Panel: Ön Kayıt Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Başvuru Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ön Kayıt / Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Program / Şube</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ücret / Peşinat</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($admissions as $adm)
                            <tr>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $adm->first_name }} {{ $adm->last_name }}</span>
                                    <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $adm->admission_no }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-300">
                                    <span>{{ $adm->program ?? 'Genel' }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">{{ $adm->branch->name ?? 'Merkez' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs font-mono">
                                    <span class="font-bold text-neutral-800 dark:text-neutral-200">₺{{ number_format($adm->total_amount, 2) }}</span>
                                    <div class="text-[10px] text-emerald-600">Peşinat: ₺{{ number_format($adm->deposit_amount, 2) }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700">
                                        {{ $adm->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.admission.show', $adm->id) }}" class="text-primary hover:underline font-bold">Yönet & Evraklar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz kaydedilmiş ön kayıt bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
