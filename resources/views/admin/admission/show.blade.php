@extends('layouts.admin')
@section('title', 'Ön Kayıt Detay & Workflow')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Bar ve Geri Dönüş -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <x-admin.button href="{{ route('admin.admission.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Başvurulara Geri Dön
            </x-admin.button>
        </div>

        <!-- Workflow Durum Çubuğu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6 relative overflow-hidden">
            <!-- Arka plan süslemesi -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-emerald-50 dark:bg-emerald-900/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 text-[10px] font-mono font-bold border border-neutral-200 dark:border-neutral-700">Başvuru No: {{ $admission->admission_no }}</span>
                    </div>
                    <h2 class="text-xl font-black text-neutral-900 dark:text-white flex items-center gap-2">
                        {{ $admission->first_name }} {{ $admission->last_name }}
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $admission->status }}
                    </span>
                </div>
            </div>

            <!-- Workflow Adımları Visual -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 pt-4 text-center text-[11px] font-bold relative z-10">
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['pre_registration','student_info_completed','document_pending','document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    1. Ön Kayıt
                </div>
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['document_pending','document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    2. Evraklar
                </div>
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    3. Evrak Onayı
                </div>
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    4. Sözleşme
                </div>
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    5. Finans / Peşinat
                </div>
                <div class="p-3 rounded-xl transition-colors {{ in_array($admission->status, ['enrolled','active_student']) ? 'bg-emerald-600 text-white shadow-lg ring-2 ring-emerald-500/20' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500' }}">
                    6. Kesin Kayıt
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Sütun: Kayıt Evrakları & Sözleşme & Kesin Kayıt Aksiyonu -->
            <div class="space-y-6 lg:col-span-2">
                
                <!-- 1. Kayıt Evrakları Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-4">
                        <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Kayıt Evrakları (Yükleme & Onay)
                        </h3>
                    </div>

                    <!-- Evrak Yükleme Formu -->
                    <form method="POST" action="{{ route('admin.enrollment.document.upload') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-neutral-50/50 dark:bg-neutral-800/20 rounded-xl border border-neutral-100 dark:border-neutral-800">
                        @csrf
                        <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                        
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 mb-1.5">Belge Türü</label>
                            <select name="document_type" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-xs text-neutral-900 dark:text-white transition-colors">
                                <option value="Kimlik">Kimlik Fotokopisi</option>
                                <option value="Veli Belgesi">Veli Muvafakatnamesi</option>
                                <option value="Sözleşme">İmzalı Sözleşme</option>
                                <option value="Diploma">Diploma / Öğrenci Belgesi</option>
                                <option value="Fotoğraf">Vesikalık Fotoğraf</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-500 dark:text-neutral-400 mb-1.5">Belge Adı / Açıklama</label>
                            <input type="text" name="file_name" required placeholder="Örn: Kimlik Ön Yüz" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-xs text-neutral-900 dark:text-white transition-colors">
                        </div>

                        <div class="flex items-end">
                            <x-admin.button type="submit" variant="primary" icon="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" class="w-full justify-center text-xs py-2">
                                Belge Yükle
                            </x-admin.button>
                        </div>
                    </form>

                    <!-- Yüklü Evraklar Tablosu -->
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Belge Türü</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Dosya Adı</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Onay Durumu</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlem</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($admission->documents as $doc)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">{{ $doc->document_type }}</td>
                                    <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300 font-mono">{{ $doc->file_name }}</td>
                                    <td class="px-6 py-4">
                                        @if($doc->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Onaylandı
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                Beklemede
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($doc->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.enrollment.document.approve', $doc->id) }}">
                                                @csrf
                                                <button type="submit" class="text-sm text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Onayla</button>
                                            </form>
                                        @else
                                            <span class="text-neutral-400 dark:text-neutral-500 text-sm font-medium">Tamamlandı</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-0 py-0">
                                        <x-admin.empty-state
                                            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            title="Belge Bulunamadı"
                                            description="Bu başvuruya ait henüz hiçbir evrak yüklenmemiş. Yukarıdaki formu kullanarak ilk belgeyi yükleyin."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>
                </div>

                <!-- 2. Dinamik Sözleşme Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Kayıt Sözleşmesi
                    </h3>

                    <div class="space-y-4">
                        @forelse($admission->contracts as $cnt)
                            <div class="p-5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-4">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 shadow-sm font-mono text-neutral-900 dark:text-white">
                                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        {{ $cnt->contract_no }}
                                    </span>
                                    @if($cnt->status === 'signed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            İmzalandı ({{ $cnt->signed_at->format('d.m.Y') }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                            İmza Bekliyor
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-neutral-600 dark:text-neutral-300 h-32 overflow-y-auto p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-inner">
                                    {!! nl2br(e($cnt->rendered_content)) !!}
                                </div>
                                @if($cnt->status !== 'signed')
                                    <div class="pt-2">
                                        <form method="POST" action="{{ route('admin.contracts.sign', $cnt->id) }}">
                                            @csrf
                                            <x-admin.button type="submit" variant="primary" icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" class="w-full sm:w-auto">
                                                Sözleşmeyi İmzalandı İşaretle
                                            </x-admin.button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 bg-indigo-50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-800/30 flex flex-col items-center justify-center text-center space-y-4">
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-500 dark:text-indigo-400 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300">Sözleşme Hazırlanmadı</h4>
                                    <p class="text-xs text-indigo-700/70 dark:text-indigo-400/70 mt-1 max-w-md">Kayıt evrakları tamamlandıktan sonra öğrenci için sistem üzerinden otomatik sözleşme oluşturabilirsiniz.</p>
                                </div>
                                <form method="POST" action="{{ route('admin.contracts.generate') }}">
                                    @csrf
                                    <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                                    <input type="hidden" name="contract_template_id" value="1">
                                    <x-admin.button type="submit" variant="primary" icon="M13 10V3L4 14h7v7l9-11h-7z">
                                        Dinamik Sözleşme Üret (Şablon #1)
                                    </x-admin.button>
                                </form>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 3. KESİN KAYIT TAMAMLAMA BUTONU -->
                @if(!in_array($admission->status, ['enrolled', 'active_student']))
                    <div class="bg-gradient-to-r from-emerald-900 to-emerald-950 p-6 rounded-2xl text-white shadow-lg space-y-6 relative overflow-hidden">
                        <!-- Arka plan deseni -->
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                        
                        <div class="relative z-10 space-y-2">
                            <h3 class="text-base font-black flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Kesin Kayıt İşlemini Tamamla
                            </h3>
                            <p class="text-xs text-emerald-100/80 leading-relaxed max-w-2xl">Bu işlem sonucunda resmi öğrenci kartı oluşturulacak, Finans modülünde faturası kesilecek ve kesin kayıt tamamlanacaktır. Öğrencinin sistemdeki statüsü 'Aktif Öğrenci' olarak güncellenecektir.</p>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.enrollment.complete') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 relative z-10 bg-black/20 p-5 rounded-xl border border-white/10 backdrop-blur-sm">
                            @csrf
                            <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                            
                            <div>
                                <label class="block text-xs font-bold text-emerald-200 mb-1.5">Sınıf / Şube Ataması</label>
                                <select name="classroom_id" class="w-full bg-emerald-900/50 border-emerald-700/50 text-white rounded-lg shadow-sm focus:ring-emerald-400 focus:border-emerald-400 text-xs transition-colors">
                                    <option value="">Atanmamış (Sonra Atanabilir)</option>
                                    @foreach($classrooms as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-emerald-200 mb-1.5">Kesinleşen Ücret (₺)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-emerald-400 sm:text-sm font-bold">₺</span>
                                    </div>
                                    <input type="number" step="0.01" name="final_fee" value="{{ $admission->total_amount }}" class="w-full pl-8 bg-emerald-900/50 border-emerald-700/50 text-white rounded-lg shadow-sm focus:ring-emerald-400 focus:border-emerald-400 text-xs transition-colors font-mono">
                                </div>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black text-xs rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                                    <span>Kesin Kaydı Tamamla</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-5 rounded-2xl border border-emerald-200 dark:border-emerald-800/50 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-900 dark:text-emerald-100">Kayıt İşlemi Tamamlandı</h4>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">Kesin kayıt ve finansal faturalandırma işlemi başarıyla tamamlanmıştır. Öğrenci artık aktif statüdedir.</p>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Sağ Sütun: Başvuru Notları & Timeline -->
            <div class="space-y-6">
                
                <!-- Başvuru Bilgileri -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-3">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Öğrenci Bilgileri
                    </h3>
                    
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-neutral-50 dark:border-neutral-800/50 last:border-0">
                            <span class="text-neutral-500 dark:text-neutral-400 font-medium">Telefon:</span>
                            <span class="font-bold text-neutral-900 dark:text-white font-mono bg-neutral-50 dark:bg-neutral-800 px-2 py-0.5 rounded">{{ $admission->phone }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-neutral-50 dark:border-neutral-800/50 last:border-0">
                            <span class="text-neutral-500 dark:text-neutral-400 font-medium">T.C. No:</span>
                            <span class="font-bold text-neutral-900 dark:text-white font-mono bg-neutral-50 dark:bg-neutral-800 px-2 py-0.5 rounded">{{ $admission->tc_no ?? 'Belirtilmedi' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-neutral-50 dark:border-neutral-800/50 last:border-0">
                            <span class="text-neutral-500 dark:text-neutral-400 font-medium">Veli Adı:</span>
                            <span class="font-bold text-neutral-900 dark:text-white">{{ $admission->guardian_name ?? 'Belirtilmedi' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-neutral-50 dark:border-neutral-800/50 last:border-0">
                            <span class="text-neutral-500 dark:text-neutral-400 font-medium">Toplam Ücret:</span>
                            <span class="font-bold text-neutral-900 dark:text-white font-mono">₺{{ number_format($admission->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-neutral-50 dark:border-neutral-800/50 last:border-0">
                            <span class="text-neutral-500 dark:text-neutral-400 font-medium">Ödenen Kapora:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">₺{{ number_format($admission->deposit_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Not Ekleme -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Görüşme / Kayıt Notu
                    </h3>
                    <form method="POST" action="{{ route('admin.admission.note.store', $admission->id) }}" class="space-y-3">
                        @csrf
                        <textarea name="note_text" required rows="3" placeholder="Yeni bir görüşme notu ekleyin..." class="w-full bg-neutral-50/50 dark:bg-neutral-800/20 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-xs text-neutral-900 dark:text-white transition-colors resize-none"></textarea>
                        <x-admin.button type="submit" variant="secondary" icon="M12 6v6m0 0v6m0-6h6m-6 0H6" class="w-full justify-center">
                            Notu Kaydet
                        </x-admin.button>
                    </form>
                </div>

                <!-- Status Logs Timeline -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-3">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Aşama Geçmişi
                    </h3>
                    
                    <div class="relative pl-3 space-y-4 before:absolute before:inset-y-2 before:left-3.5 before:w-0.5 before:bg-neutral-100 dark:before:bg-neutral-800">
                        @forelse($admission->statusLogs as $log)
                            <div class="relative pl-5">
                                <!-- Timeline Noktası -->
                                <div class="absolute left-[-5px] top-1.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-neutral-900"></div>
                                
                                <div class="space-y-1 bg-neutral-50 dark:bg-neutral-800/30 p-3 rounded-xl border border-neutral-100 dark:border-neutral-800">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $log->to_status }}</span>
                                        <span class="text-neutral-500 dark:text-neutral-400 font-mono">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p class="text-[11px] text-neutral-600 dark:text-neutral-300 leading-relaxed">{{ $log->description }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs text-neutral-400 dark:text-neutral-500 py-6 border-2 border-dashed border-neutral-100 dark:border-neutral-800 rounded-xl">
                                Henüz bir işlem geçmişi bulunmuyor.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
