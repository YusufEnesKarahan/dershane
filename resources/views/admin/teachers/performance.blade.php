@extends('layouts.admin')
@section('title', 'Eğitmen Performansı')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Yeni Performans Skoru Kaydet -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Değerlendirme Ekle</h3>
            <p class="text-xs text-neutral-400">Eğitmen <b>{{ $teacher->user->name }}</b> için performans puanlaması ve değerlendirmesi yapın.</p>
            
            <x-admin.form.layout :action="route('admin.teachers.performance.store')" method="POST">
                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">

                <x-admin.form.field-group label="Değerlendirme Kriteri" id="metric_type">
                    <select name="metric_type" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        <option value="Student Success Rate">Öğrenci Başarı Oranı (TYT/AYT)</option>
                        <option value="Classroom Management">Sınıf Yönetimi & Disiplin</option>
                        <option value="Parent Satisfaction">Veli Memnuniyet Skoru</option>
                        <option value="Attendance Tracking Quality">Yoklama & Ödev Takip Kalitesi</option>
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Performans Skoru (0 - 100)" id="score">
                    <input type="number" name="score" required min="0" max="100" value="90" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Değerlendirme Yorumları" id="comments">
                    <textarea name="comments" rows="4" placeholder="Değerlendirme detayları ve zümre notları..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                </x-admin.form.field-group>

                <div class="pt-4">
                    <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Değerlendirmeyi Kaydet
                    </button>
                </div>

            </x-admin.form.layout>
        </div>

        <!-- Sağ Panel: Performans Logları Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Performans Değerlendirme Geçmişi</h3>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kriter</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Skor</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yorum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $log->metric_type }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-primary font-mono">{{ $log->score }} / 100</td>
                            <td class="px-4 py-3 text-xs text-neutral-600">{{ $log->comments ?? 'Yorumsuz' }}</td>
                            <td class="px-4 py-3 text-xs text-neutral-400 font-mono">{{ \Carbon\Carbon::parse($log->evaluated_at)->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz performans kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
