@extends('layouts.admin')
@section('title', 'Eğitmen Performansı')
@section('content')
    <x-admin.crud.index-layout title="Eğitmen Performansı" description="Eğitmen {{ $teacher->user->name }} için performans puanlaması ve değerlendirmesi yapın.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Performans Skoru Kaydet -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    Yeni Değerlendirme Ekle
                </h3>
            
            <x-admin.form.layout :action="route('admin.teachers.performance.store')" method="POST">
                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">

                <x-admin.form.field-group label="Değerlendirme Kriteri" id="metric_type">
                    <select name="metric_type" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                        <option value="Student Success Rate">Öğrenci Başarı Oranı (TYT/AYT)</option>
                        <option value="Classroom Management">Sınıf Yönetimi & Disiplin</option>
                        <option value="Parent Satisfaction">Veli Memnuniyet Skoru</option>
                        <option value="Attendance Tracking Quality">Yoklama & Ödev Takip Kalitesi</option>
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Performans Skoru (0 - 100)" id="score">
                    <input type="number" name="score" required min="0" max="100" value="90" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Değerlendirme Yorumları" id="comments">
                    <textarea name="comments" rows="4" placeholder="Değerlendirme detayları ve zümre notları..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors"></textarea>
                </x-admin.form.field-group>

                <div class="pt-4 mt-auto">
                    <button type="submit" class="w-full py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-500 transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Değerlendirmeyi Kaydet
                    </button>
                </div>

            </x-admin.form.layout>
        </div>

        <!-- Sağ Panel: Performans Logları Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Performans Değerlendirme Geçmişi
                </h3>
            </div>
            
            <div class="p-0 flex-1 overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50/80 dark:bg-neutral-900/80 backdrop-blur-sm">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Kriter</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Skor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Yorum</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50 bg-white dark:bg-neutral-900">
                        @forelse($logs as $log)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $log->metric_type }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 text-xs font-bold border border-green-200/50 dark:border-green-500/20 font-mono">{{ $log->score }} <span class="text-xs font-normal opacity-70 ml-1">/ 100</span></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[11px] text-neutral-600 dark:text-neutral-400 line-clamp-1" title="{{ $log->comments }}">{{ $log->comments ?? 'Yorumsuz' }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    <span class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">{{ \Carbon\Carbon::parse($log->evaluated_at)->format('d.m.Y') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-admin.empty-state
                                        icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        title="Kayıt Bulunamadı"
                                        description="Henüz performans kaydı bulunmamaktadır."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </x-admin.crud.index-layout>
@endsection
