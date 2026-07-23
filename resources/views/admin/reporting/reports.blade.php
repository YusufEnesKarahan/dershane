@extends('layouts.admin')
@section('title', 'Kurumsal Raporlama & Dışa Aktarımlar')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Rapor Planlama & Manuel Dışa Aktarma -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
            
            <!-- Manuel Export -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Manuel Rapor Dışa Aktar</h3>
                <p class="text-xs text-neutral-400">Canlı verileri anında Excel, CSV veya PDF formatında dışa aktarın.</p>
                
                <form method="POST" action="{{ route('admin.reporting.export') }}" class="space-y-4">
                    @csrf
                    <x-admin.form.field-group label="Rapor Tipi" id="report_type">
                        <select name="report_type" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="Executive Dashboard Summary">Executive Dashboard Özet Raporu</option>
                            <option value="Academic Success & Attendance">Akademik Başarı & Yoklama Raporu</option>
                            <option value="Financial Debt & Billing">Finansal Borç & Tahsilat Raporu</option>
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Format" id="format">
                        <select name="format" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="PDF">PDF Raporu</option>
                            <option value="Excel">Excel Raporu</option>
                            <option value="CSV">CSV Tablosu</option>
                        </select>
                    </x-admin.form.field-group>

                    <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Raporu Üret & Aktar
                    </button>
                </form>
            </div>

            <hr class="border-neutral-100 dark:border-neutral-800">

            <!-- Planlanmış Rapor -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Rapor Planı Oluştur</h3>
                <p class="text-xs text-neutral-400">İstediğiniz raporu belirli periyotlarda otomatik üreterek e-posta adreslerine gönderin.</p>
                
                <form method="POST" action="{{ route('admin.reporting.schedules.store') }}" class="space-y-4">
                    @csrf
                    <x-admin.form.field-group label="Rapor Tipi" id="report_type_sch">
                        <select name="report_type" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="Weekly Manager Summary">Haftalık Yönetici Özeti</option>
                            <option value="Monthly Financial Balance">Aylık Mali Bilanço Raporu</option>
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Format" id="format_sch">
                        <select name="format" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="PDF">PDF</option>
                            <option value="Excel">Excel</option>
                            <option value="CSV">CSV</option>
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Gönderilecek E-posta(lar)" id="email_recipients">
                        <input type="text" name="email_recipients" required placeholder="manager@dershane.com, finance@dershane.com" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Cron İfadesi (Sıklık)" id="cron_expression">
                        <input type="text" name="cron_expression" required value="0 9 * * 1" placeholder="0 9 * * 1 (Her Pazartesi 09:00)" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm border border-indigo-500">
                        Otomatik Plan Kaydet
                    </button>
                </form>
            </div>

        </div>

        <!-- Sağ Panel: Dışa Aktarılan Raporlar & Aktif Planlar -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Aktif Rapor Dışa Aktarımları -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Dışa Aktarılan Raporlar</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Rapor Tipi / Format</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İsteyen</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($exports as $exp)
                            <tr>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-bold text-neutral-900">{{ $exp->report_type }}</span>
                                    <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $exp->format }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-600 font-semibold">{{ $exp->user->name ?? 'Sistem' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $exp->status === 'Completed' ? 'bg-green-150 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $exp->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-400 font-mono">{{ $exp->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($exp->status === 'Completed')
                                        <a href="{{ route('admin.reporting.download', $exp->id) }}" class="text-primary hover:underline font-bold">Raporu İndir</a>
                                    @else
                                        <span class="text-neutral-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz üretilmiş bir rapor dışa aktarımı bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

            <!-- Aktif Rapor Planları -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Planlanmış Raporlar (Schedules)</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Rapor Tipi</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Alıcılar</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sıklık (Cron)</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($schedules as $sch)
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $sch->report_type }} ({{ $sch->format }})</td>
                                <td class="px-4 py-3 text-xs text-neutral-600 font-semibold">{{ $sch->email_recipients }}</td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">{{ $sch->cron_expression }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz planlanmış otomatik rapor bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>

    </div>
@endsection
