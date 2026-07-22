@extends('layouts.admin')
@section('title', 'Veli Bilgi Sistemi')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Veli & Öğrenci Seçim Kartı -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-neutral-900 dark:text-white">Veli Portalı</h1>
                <p class="text-xs text-neutral-500 mt-1">Öğrencinizin akademik başarı, yoklama, ödev ve finansal durumunu anlık takip edin.</p>
            </div>
            
            @if($linkedStudents->isNotEmpty())
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-neutral-500 uppercase">Seçili Öğrenci:</label>
                    <form method="GET" action="{{ route('parent.dashboard') }}">
                        <select name="student_id" onchange="this.form.submit()" class="text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 border-none rounded-xl px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            @foreach($linkedStudents as $ls)
                                <option value="{{ $ls->id }}" {{ $student && $student->id === $ls->id ? 'selected' : '' }}>
                                    {{ $ls->first_name }} {{ $ls->last_name }} ({{ $ls->student_number }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

        @if(!$student)
            <div class="bg-amber-50 text-amber-800 p-6 rounded-2xl border border-amber-200 text-sm">
                Sisteme kayıtlı veya hesabınızla ilişkilendirilmiş bir öğrenci bulunamadı. Lütfen yönetimle iletişime geçiniz.
            </div>
        @else
            <!-- Özet İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                    <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Öğrenci Sınıfı</h4>
                    <div class="text-lg font-bold text-neutral-900 dark:text-white">{{ $student->classroom->name ?? 'Tanımsız' }}</div>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                    <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Devamsızlık</h4>
                    <div class="text-lg font-bold text-primary">{{ $attendance->whereIn('status', ['Absent', 'Late'])->count() }} Gün</div>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                    <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Sınav Ortalama Net</h4>
                    <div class="text-lg font-bold text-green-600">
                        @if($examResults->count() > 0)
                            {{ round($examResults->avg('net'), 2) }} Net
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                    <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Okunmamış Veli Bildirimi</h4>
                    <div class="text-lg font-bold text-amber-600">
                        <a href="{{ route('parent.notifications') }}" class="hover:underline">
                            {{ $analytics['unread_notifications_count'] }} Yeni
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tablı Detay Alanı -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 shadow-premium-sm overflow-hidden">
                <!-- Tab Başlıkları -->
                <div class="flex border-b border-neutral-100 bg-neutral-50/50 dark:bg-neutral-800/40 overflow-x-auto">
                    <button onclick="switchTab('profile')" id="btn-profile" class="tab-btn active px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Öğrenci Profili
                    </button>
                    <button onclick="switchTab('attendance')" id="btn-attendance" class="tab-btn px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Yoklama & Devamsızlık
                    </button>
                    <button onclick="switchTab('exams')" id="btn-exams" class="tab-btn px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Sınav Sonuçları
                    </button>
                    <button onclick="switchTab('homework')" id="btn-homework" class="tab-btn px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Ödev Takibi
                    </button>
                    <button onclick="switchTab('finance')" id="btn-finance" class="tab-btn px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Ödemeler & Faturalar
                    </button>
                    <button onclick="switchTab('announcements')" id="btn-announcements" class="tab-btn px-6 py-4 text-xs font-bold text-neutral-600 hover:text-neutral-900 border-b-2 border-transparent transition">
                        Duyurular
                    </button>
                </div>

                <!-- Tab İçerikleri -->
                <div class="p-6">
                    
                    <!-- 1. Profil Sekmesi -->
                    <div id="tab-profile" class="tab-content space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Genel Bilgiler</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 bg-neutral-50 rounded-xl">
                                <span class="text-neutral-400 font-semibold">T.C. Kimlik / Pasaport:</span>
                                <span class="float-right font-bold text-neutral-800">{{ $student->national_id }}</span>
                            </div>
                            <div class="p-4 bg-neutral-50 rounded-xl">
                                <span class="text-neutral-400 font-semibold">Öğrenci Numarası:</span>
                                <span class="float-right font-bold text-neutral-800">{{ $student->student_number }}</span>
                            </div>
                            <div class="p-4 bg-neutral-50 rounded-xl">
                                <span class="text-neutral-400 font-semibold">Şube:</span>
                                <span class="float-right font-bold text-neutral-800">{{ $student->branch->name ?? 'HQ Merkez' }}</span>
                            </div>
                            <div class="p-4 bg-neutral-50 rounded-xl">
                                <span class="text-neutral-400 font-semibold">Kayıt Tarihi:</span>
                                <span class="float-right font-bold text-neutral-800">{{ \Carbon\Carbon::parse($student->created_at)->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Yoklama Sekmesi -->
                    <div id="tab-attendance" class="tab-content hidden space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Ders Devamsızlık Listesi</h3>
                        <x-admin.table.layout>
                            <x-slot name="head">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ders Tarihi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Oturum Bilgisi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yoklama Durumu</th>
                            </x-slot>
                            <x-slot name="body">
                                @forelse($attendance as $att)
                                    <tr>
                                        <td class="px-4 py-3 text-xs font-bold text-neutral-900 font-mono">{{ $att->session->date ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-xs font-semibold text-neutral-700">Saat: {{ $att->session->start_time ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-xs">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $att->status === 'Present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $att->status === 'Present' ? 'Katıldı' : 'Katılmadı (Devamsız)' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-xs text-neutral-400">Yoklama kaydı bulunmamaktadır.</td>
                                    </tr>
                                @endforelse
                            </x-slot>
                        </x-admin.table.layout>
                    </div>

                    <!-- 3. Sınav Sonuçları Sekmesi -->
                    <div id="tab-exams" class="tab-content hidden space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Deneme & Yazılı Sonuçları</h3>
                        <x-admin.table.layout>
                            <x-slot name="head">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sınav Adı</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">D / Y / B</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Net</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Puan</th>
                            </x-slot>
                            <x-slot name="body">
                                @forelse($examResults as $res)
                                    <tr>
                                        <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $res->exam->title }}</td>
                                        <td class="px-4 py-3 text-xs text-neutral-600 font-mono">{{ $res->correct }} D / {{ $res->wrong }} Y / {{ $res->blank }} B</td>
                                        <td class="px-4 py-3 text-xs font-bold text-primary font-mono">{{ $res->net }} Net</td>
                                        <td class="px-4 py-3 text-xs font-bold text-green-600 font-mono">{{ $res->score }} Puan</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Sınav sonucu bulunmamaktadır.</td>
                                    </tr>
                                @endforelse
                            </x-slot>
                        </x-admin.table.layout>
                    </div>

                    <!-- 4. Ödev Sekmesi -->
                    <div id="tab-homework" class="tab-content hidden space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Verilen Ödevler & Teslim Durumu</h3>
                        <x-admin.table.layout>
                            <x-slot name="head">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ödev Başlığı</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Son Teslim Tarihi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Puan / Geri Bildirim</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                            </x-slot>
                            <x-slot name="body">
                                @forelse($homeworks as $hw)
                                    @php $sub = $hw->submissions->first(); @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $hw->title }}</td>
                                        <td class="px-4 py-3 text-xs text-neutral-500 font-mono">{{ \Carbon\Carbon::parse($hw->due_date)->format('d.m.Y') }}</td>
                                        <td class="px-4 py-3 text-xs">
                                            @if($sub && $sub->score)
                                                <span class="font-bold text-green-600 font-mono">{{ $sub->score }} Puan</span>
                                                <div class="text-[10px] text-neutral-400">{{ $sub->teacher_feedback }}</div>
                                            @else
                                                <span class="text-neutral-400">Henüz değerlendirilmedi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sub ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $sub ? 'Teslim Edildi' : 'Teslim Bekleniyor' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Verilmiş ödev kaydı bulunmamaktadır.</td>
                                    </tr>
                                @endforelse
                            </x-slot>
                        </x-admin.table.layout>
                    </div>

                    <!-- 5. Finans Sekmesi -->
                    <div id="tab-finance" class="tab-content hidden space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Ödeme Planı & Faturalar</h3>
                        <x-admin.table.layout>
                            <x-slot name="head">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Fatura No</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Toplam Tutar</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Vade Tarihi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kalan Borç</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                            </x-slot>
                            <x-slot name="body">
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="px-4 py-3 text-xs font-bold text-neutral-900 font-mono">{{ $inv->invoice_number }}</td>
                                        <td class="px-4 py-3 text-xs text-neutral-800 font-bold font-mono">₺{{ number_format($inv->total_amount, 2) }}</td>
                                        <td class="px-4 py-3 text-xs text-neutral-500 font-mono">{{ \Carbon\Carbon::parse($inv->due_date)->format('d.m.Y') }}</td>
                                        <td class="px-4 py-3 text-xs text-red-600 font-bold font-mono">₺{{ number_format($inv->total_amount - $inv->payments->sum('amount'), 2) }}</td>
                                        <td class="px-4 py-3 text-xs">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $inv->status === 'Paid' ? 'bg-green-100 text-green-800' : ($inv->status === 'Partial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $inv->status === 'Paid' ? 'Ödendi' : ($inv->status === 'Partial' ? 'Kısmi Ödeme' : 'Ödeme Bekliyor') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Fatura kaydı bulunmamaktadır.</td>
                                    </tr>
                                @endforelse
                            </x-slot>
                        </x-admin.table.layout>
                    </div>

                    <!-- 6. Duyurular Sekmesi -->
                    <div id="tab-announcements" class="tab-content hidden space-y-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-2">Güncel Genel Duyurular</h3>
                        <div class="space-y-4">
                            @forelse($announcements as $ann)
                                <div class="p-4 bg-neutral-50 rounded-2xl border border-neutral-100 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-primary">{{ $ann->group->name ?? 'Genel' }}</span>
                                        <span class="text-[10px] text-neutral-400 font-mono">{{ \Carbon\Carbon::parse($ann->published_at)->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-neutral-800">{{ $ann->title }}</h4>
                                    <p class="text-[11px] text-neutral-600 leading-relaxed">{{ $ann->content }}</p>
                                </div>
                            @empty
                                <div class="text-center text-xs text-neutral-400 py-6">Aktif duyuru bulunmamaktadır.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </div>

    <!-- Switch Tab Javascript Logic -->
    <script>
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active classes from buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-neutral-600');
            });

            // Show current tab content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            // Mark button active
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('border-transparent', 'text-neutral-600');
            activeBtn.classList.add('active', 'border-primary', 'text-primary');
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            switchTab('profile');
        });
    </script>

    <style>
        .tab-btn.active {
            border-bottom-color: var(--color-primary, #6366f1);
            color: var(--color-primary, #6366f1);
        }
    </style>
@endsection
