@extends('layouts.admin')
@section('title', 'Ödev Yönetimi & Değerlendirme')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Yeni Ödev Ver & Mevcut Ödevler -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
            <div>
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Ödev Oluştur</h3>
                <p class="text-xs text-neutral-400 mt-1">Sınıflarınıza yeni bireysel veya sınıf ödevleri tanımlayın.</p>
            </div>

            <x-admin.form.layout :action="route('teacher.homework.store')" method="POST">
                
                <x-admin.form.field-group label="Hedef Sınıf" id="classroom_id">
                    <select name="classroom_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        @foreach($classrooms as $cr)
                            <option value="{{ $cr->id }}">{{ $cr->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Ders / Müfredat" id="course_id">
                    <select name="course_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        @foreach($courses as $co)
                            <option value="{{ $co->id }}">{{ $co->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Ödev Başlığı" id="title">
                    <input type="text" name="title" required placeholder="Örn: Limit ve Süreklilik Fasikülü" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Ödev Detayı & Talimatları" id="content">
                    <textarea name="content" required rows="4" placeholder="Ödev detayları..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Son Teslim Tarihi" id="due_date">
                    <input type="date" name="due_date" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                </x-admin.form.field-group>

                <div class="pt-4">
                    <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Ödevi Yayınla & Öğrencilere Gönder
                    </button>
                </div>

            </x-admin.form.layout>
        </div>

        <!-- Sağ Panel: Ödev Teslim Değerlendirme Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
            
            <!-- Ödev Seçimi -->
            <div class="space-y-2 border-b border-neutral-100 pb-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Ödev Değerlendirme & Puanlama</h3>
                <form method="GET" action="{{ route('teacher.homework') }}">
                    <select name="assignment_id" onchange="this.form.submit()" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        <option value="">Lütfen Ödev Seçiniz</option>
                        @foreach($assignments as $asg)
                            <option value="{{ $asg->id }}" {{ $assignment && $assignment->id === $asg->id ? 'selected' : '' }}>
                                {{ $asg->title }} (Son Vade: {{ $asg->due_date }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if(!$assignment)
                <div class="text-center text-xs text-neutral-400 py-12">Lütfen değerlendirmek istediğiniz ödevi seçin.</div>
            @else
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Teslim Zamanı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Skor</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($submissions as $sub)
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $sub->student->first_name }} {{ $sub->student->last_name }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-neutral-500">{{ $sub->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-primary font-mono">{{ $sub->score ?? 'N/A' }} / 100</td>
                                <td class="px-4 py-3 text-xs">
                                    <form method="POST" action="{{ route('teacher.homework.evaluate') }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                        <input type="number" name="score" required min="0" max="100" placeholder="Skor" class="w-16 text-xs bg-neutral-50 border border-neutral-200 rounded px-1 py-0.5">
                                        <input type="text" name="teacher_feedback" placeholder="Geri bildirim..." class="w-32 text-xs bg-neutral-50 border border-neutral-200 rounded px-1 py-0.5">
                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white text-[10px] font-bold rounded hover:bg-green-700 transition">Puanla</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz teslim edilmiş ödev bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            @endif
        </div>

    </div>
@endsection
