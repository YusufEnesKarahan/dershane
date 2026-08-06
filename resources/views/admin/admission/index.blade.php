@extends('layouts.admin')
@section('title', 'Ön Kayıt Başvuruları (Admission)')
@section('content')
    <x-admin.crud.index-layout title="Ön Kayıt Yönetimi" description="Yeni öğrenci başvuru ve ön kayıt işlemlerini yönetin, CRM leadlerini aday kaydına dönüştürün.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Hızlı Ön Kayıt Ekle / Lead Dönüştür -->
            <div class="space-y-6">
                
                <!-- Lead'den Dönüştür -->
                @if(count($leads) > 0)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl text-white shadow-sm space-y-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        
                        <h3 class="text-xs font-bold uppercase tracking-wider text-blue-300 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            CRM Lead Kaydından Dönüştür
                        </h3>
                        
                        <form method="POST" action="" id="convertLeadForm">
                            @csrf
                            <div class="space-y-4 relative z-10">
                                <select id="leadSelect" required class="w-full text-sm bg-slate-800/80 border border-slate-700/50 rounded-xl px-3 py-2.5 text-white shadow-inner focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                                    <option value="">Aday Öğrenci Seçin</option>
                                    @foreach($leads as $ld)
                                        <option value="{{ $ld->id }}">{{ $ld->first_name }} {{ $ld->last_name }} ({{ $ld->phone }})</option>
                                    @endforeach
                                </select>

                                <button type="button" onclick="submitConvert()" class="w-full py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
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
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        Manuel Ön Kayıt Oluştur
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.admission.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-admin.form.field-group label="Adı" id="first_name" required>
                                <input type="text" name="first_name" id="first_name" required placeholder="Ali" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>
                            
                            <x-admin.form.field-group label="Soyadı" id="last_name" required>
                                <input type="text" name="last_name" id="last_name" required placeholder="Kaya" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>
                        </div>

                        <x-admin.form.field-group label="Telefon No" id="phone" required>
                            <input type="text" name="phone" id="phone" required placeholder="0555 555 5555" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="T.C. Kimlik No" id="tc_no">
                            <input type="text" name="tc_no" id="tc_no" placeholder="11 haneli TC No" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Program / Alan" id="program">
                            <input type="text" name="program" id="program" placeholder="Örn: YKS Eşit Ağırlık" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                        </x-admin.form.field-group>

                        <div class="grid grid-cols-2 gap-4">
                            <x-admin.form.field-group label="Toplam Ücret (₺)" id="total_amount">
                                <input type="number" step="0.01" name="total_amount" id="total_amount" placeholder="45000" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors font-mono">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Kapora/Peşinat (₺)" id="deposit_amount">
                                <input type="number" step="0.01" name="deposit_amount" id="deposit_amount" placeholder="5000" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors font-mono">
                            </x-admin.form.field-group>
                        </div>

                        <x-admin.form.field-group label="Hedef Şube" id="branch_id">
                            <select name="branch_id" id="branch_id" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                                <option value="">Şube Seçin</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <x-admin.button type="submit" variant="primary" icon="M13 5l7 7-7 7M5 5l7 7-7 7" class="w-full justify-center mt-4">
                                Ön Kaydı Başlat
                            </x-admin.button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Sağ Panel: Ön Kayıt Listesi -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Başvuru Listesi
                    </h3>
                    
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ön Kayıt / Öğrenci</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Program / Şube</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ücret / Peşinat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider w-24">İşlem</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($admissions as $adm)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $adm->first_name }} {{ $adm->last_name }}</div>
                                        <div class="inline-flex items-center gap-1 mt-1">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] font-mono font-medium border border-slate-200 dark:border-slate-700">
                                                {{ $adm->admission_no }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $adm->program ?? 'Genel Program' }}</div>
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 text-[11px] font-medium text-slate-500 mt-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            {{ $adm->branch->name ?? 'Merkez' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white font-mono">₺{{ number_format($adm->total_amount, 2) }}</div>
                                        <div class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-0.5">Peşinat: ₺{{ number_format($adm->deposit_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                            {{ $adm->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.admission.show', $adm->id) }}" class="inline-flex items-center justify-center p-2 text-primary hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors group-hover:bg-white dark:group-hover:bg-slate-800 border border-transparent group-hover:border-primary/10" title="Yönet & Evraklar">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-0 py-0">
                                        <x-admin.empty-state
                                            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            title="Kayıt Bulunamadı"
                                            description="Sistemde henüz kaydedilmiş bir ön kayıt bulunmamaktadır. Sol taraftaki formu kullanarak manuel bir başvuru oluşturabilir veya CRM'den dönüştürebilirsiniz."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
