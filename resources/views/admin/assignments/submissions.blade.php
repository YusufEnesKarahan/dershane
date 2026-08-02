@extends('layouts.admin')
@section('title', 'Ödev Teslimleri & Değerlendirme')
@section('content')
    <x-admin.crud.index-layout title="Ödev Teslimleri & Puanlama" description="{{ $assignment->title }} ({{ $assignment->code }}) — Son Teslim: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y H:i') }}">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.assignments.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Ödev Listesine Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Manuel Teslim Girişi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Manuel Ödev Teslim Kaydı</h3>
                
                <x-admin.form.layout :action="route('admin.assignments.submissions.store', $assignment->id)" method="POST">
                    
                    <x-admin.form.field-group label="Öğrenci" id="student_id">
                        <select name="student_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_number }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Öğrenci Notu / Açıklaması" id="remarks">
                        <textarea name="remarks" rows="2" placeholder="Fiziki ödev teslim edildi..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                        <x-admin.button type="submit" variant="primary" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" class="w-full justify-center">
                            Teslim Kaydını İşle
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Teslim Listesi ve Puanlama -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Teslim Edilen Ödevler ve Değerlendirme</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Öğrenci</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Teslim Zamanı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Puan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Değerlendir</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($submissions as $sub)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $sub->student->full_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300 font-mono w-fit">
                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ \Carbon\Carbon::parse($sub->submission_date)->format('d.m.Y H:i') }}
                                        </span>
                                        @if($sub->is_late)
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400 rounded w-fit">Geç Teslim</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($sub->status === 'Graded')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Puanlandı
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                            Bekliyor
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-primary font-mono">
                                        {{ $sub->score ? $sub->score->score . ' / ' . $sub->score->max_score : '--' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.assignments.submissions.evaluate', $assignment->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                        <input type="number" name="score" required min="0" max="100" value="{{ $sub->score->score ?? '' }}" placeholder="Puan" class="w-16 text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-2 py-1.5 font-bold text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-center font-mono">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Kaydet</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz ödev teslimi bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
