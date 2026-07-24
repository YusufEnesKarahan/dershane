@extends('layouts.admin')
@section('title', 'Ön Kayıt Detay & Workflow')
@section('content')
    <div class="space-y-6">
        
        <!-- Workflow Durum Çubuğu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-neutral-400">Başvuru No: {{ $admission->admission_no }}</span>
                    <h2 class="text-lg font-black text-neutral-900 dark:text-white">{{ $admission->first_name }} {{ $admission->last_name }}</h2>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                        {{ $admission->status }}
                    </span>
                </div>
            </div>

            <!-- Workflow Adımları Visual -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 pt-2 text-center text-[10px] font-bold">
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['pre_registration','student_info_completed','document_pending','document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    1. Ön Kayıt
                </div>
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['document_pending','document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    2. Evraklar
                </div>
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['document_completed','contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    3. Evrak Onayı
                </div>
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['contract_ready','payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    4. Sözleşme
                </div>
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['payment_pending','enrolled','active_student']) ? 'bg-emerald-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    5. Finans / Peşinat
                </div>
                <div class="p-2 rounded-xl {{ in_array($admission->status, ['enrolled','active_student']) ? 'bg-green-600 text-white shadow' : 'bg-neutral-100 text-neutral-400' }}">
                    6. Kesin Kayıt
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Sütun: Kayıt Evrakları & Sözleşme & Kesin Kayıt Aksiyonu -->
            <div class="space-y-6 lg:col-span-2">
                
                <!-- 1. Kayıt Evrakları Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kayıt Evrakları (Yükleme & Onay)</h3>
                    </div>

                    <!-- Evrak Yükleme Formu -->
                    <form method="POST" action="{{ route('admin.enrollment.document.upload') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800">
                        @csrf
                        <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-neutral-500 mb-1">Belge Türü</label>
                            <select name="document_type" required class="w-full text-xs bg-white dark:bg-neutral-800 border border-neutral-200 rounded px-2 py-1.5">
                                <option value="Kimlik">Kimlik Fotokopisi</option>
                                <option value="Veli Belgesi">Veli Muvafakatnamesi</option>
                                <option value="Sözleşme">İmzalı Sözleşme</option>
                                <option value="Diploma">Diploma / Öğrenci Belgesi</option>
                                <option value="Fotoğraf">Vesikalık Fotoğraf</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-neutral-500 mb-1">Belge Adı / Açıklama</label>
                            <input type="text" name="file_name" required placeholder="Kimlik Ön Yüz" class="w-full text-xs bg-white dark:bg-neutral-800 border border-neutral-200 rounded px-2 py-1.5">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full py-1.5 bg-primary text-white text-xs font-bold rounded hover:bg-primary-dark transition">
                                Belge Yükle
                            </button>
                        </div>
                    </form>

                    <!-- Yüklü Evraklar Tablosu -->
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Belge Türü</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Dosya Adı</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Onay Durumu</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($admission->documents as $doc)
                                <tr>
                                    <td class="px-4 py-3 text-xs font-bold text-neutral-900 dark:text-white">{{ $doc->document_type }}</td>
                                    <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-300 font-mono">{{ $doc->file_name }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $doc->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $doc->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        @if($doc->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.enrollment.document.approve', $doc->id) }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-green-600 font-bold hover:underline">Onayla</button>
                                            </form>
                                        @else
                                            <span class="text-neutral-400">Onaylandı</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-xs text-neutral-400">Henüz belge yüklenmedi.</td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>
                </div>

                <!-- 2. Dinamik Sözleşme Yönetimi -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kayıt Sözleşmesi</h3>

                    <div class="space-y-3">
                        @forelse($admission->contracts as $cnt)
                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="font-mono text-neutral-900 dark:text-white">{{ $cnt->contract_no }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] {{ $cnt->status === 'signed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $cnt->status === 'signed' ? 'İmzalandı (' . $cnt->signed_at->format('d.m.Y') . ')' : 'İmza Bekliyor' }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-neutral-600 dark:text-neutral-300 max-h-24 overflow-y-auto p-2 bg-white dark:bg-neutral-900 rounded border">
                                    {!! nl2br(e($cnt->rendered_content)) !!}
                                </div>
                                @if($cnt->status !== 'signed')
                                    <form method="POST" action="{{ route('admin.contracts.sign', $cnt->id) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-[10px] font-bold rounded">
                                            Sözleşmeyi İmzalandı İşaretle
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <form method="POST" action="{{ route('admin.contracts.generate') }}" class="flex items-center gap-3">
                                @csrf
                                <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                                <input type="hidden" name="contract_template_id" value="1">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                                    Dinamik Sözleşme Üret (Şablon #1)
                                </button>
                            </form>
                        @endforelse
                    </div>
                </div>

                <!-- 3. KESİN KAYIT TAMAMLAMA BUTONU -->
                @if(!in_array($admission->status, ['enrolled', 'active_student']))
                    <div class="bg-gradient-to-r from-green-900 to-emerald-950 p-6 rounded-2xl text-white shadow-premium space-y-4">
                        <h3 class="text-sm font-black">Kesin Kayıt İşlemini Tamamla (Student & Invoice Generation)</h3>
                        <p class="text-xs text-green-100">Bu işlem sonucunda resmi öğrenci kartı oluşturulacak, Finans modülünde faturası kesilecek ve kesin kayıt tamamlanacaktır.</p>
                        
                        <form method="POST" action="{{ route('admin.enrollment.complete') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @csrf
                            <input type="hidden" name="student_admission_id" value="{{ $admission->id }}">
                            
                            <div>
                                <label class="block text-[10px] font-bold text-green-200 mb-1">Sınıf / Şube Ataması</label>
                                <select name="classroom_id" class="w-full text-xs bg-emerald-900 border border-emerald-700 text-white rounded px-3 py-2">
                                    <option value="">Atanmamış</option>
                                    @foreach($classrooms as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-green-200 mb-1">Kesinleşen Ücret (₺)</label>
                                <input type="number" step="0.01" name="final_fee" value="{{ $admission->total_amount }}" class="w-full text-xs bg-emerald-900 border border-emerald-700 text-white rounded px-3 py-2">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full py-2 bg-green-500 hover:bg-green-400 text-slate-950 font-black text-xs rounded-xl transition shadow">
                                    Kesin Kaydı Tamamla
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 text-xs font-bold">
                        ✓ Kesin kayıt ve finansal faturalandırma işlemi tamamlanmıştır.
                    </div>
                @endif

            </div>

            <!-- Sağ Sütun: Başvuru Notları & Timeline -->
            <div class="space-y-6">
                
                <!-- Başvuru Bilgileri -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-3 text-xs">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white border-b pb-2">Öğrenci Bilgileri</h3>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Telefon:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $admission->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">T.C. No:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $admission->tc_no ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Veli:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $admission->guardian_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Toplam Ücret:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">₺{{ number_format($admission->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Ödenen Kapora:</span>
                        <span class="font-bold text-emerald-600 font-mono">₺{{ number_format($admission->deposit_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Not Ekleme -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Görüşme / Kayıt Notu</h3>
                    <form method="POST" action="{{ route('admin.admission.note.store', $admission->id) }}" class="space-y-3">
                        @csrf
                        <textarea name="note_text" required rows="3" placeholder="Not ekleyin..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                        <button type="submit" class="w-full py-2 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-700 transition">Notu Kaydet</button>
                    </form>
                </div>

                <!-- Status Logs Timeline -->
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aşama Log Geçmişi</h3>
                    <div class="space-y-3">
                        @forelse($admission->statusLogs as $log)
                            <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-1">
                                <div class="flex justify-between text-[10px] font-bold">
                                    <span class="text-emerald-600">{{ $log->to_status }}</span>
                                    <span class="text-neutral-400 font-mono">{{ $log->created_at->format('d.m H:i') }}</span>
                                </div>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300">{{ $log->description }}</p>
                            </div>
                        @empty
                            <div class="text-center text-xs text-neutral-400 py-4">Log kaydı bulunmuyor.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
