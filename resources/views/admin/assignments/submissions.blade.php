@extends('layouts.admin')
@section('title', 'Ödev Teslimleri & Değerlendirme')
@section('content')
    <x-admin.crud.index-layout title="Ödev Teslimleri & Puanlama" description="{{ $assignment->title }} ({{ $assignment->code }}) — Son Teslim: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y H:i') }}">
        <x-slot name="actions">
            <a href="{{ route('admin.assignments.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Ödev Listesine Dön
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Manuel Teslim Girişi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Manuel Ödev Teslim Kaydı</h3>
                
                <x-admin.form.layout :action="route('admin.assignments.submissions.store', $assignment->id)" method="POST">
                    
                    <x-admin.form.field-group label="Öğrenci" id="student_id">
                        <select name="student_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_number }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Öğrenci Notu / Açıklaması" id="remarks">
                        <textarea name="remarks" rows="2" placeholder="Fiziki ödev teslim edildi..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Teslim Kaydını İşle
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Teslim Listesi ve Puanlama -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Teslim Edilen Ödevler ve Değerlendirme</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Teslim Zamanı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Puan</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Değerlendir</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($submissions as $sub)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $sub->student->full_name }}
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">
                                    {{ \Carbon\Carbon::parse($sub->submission_date)->format('d.m.Y H:i') }}
                                    @if($sub->is_late)
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-red-100 text-red-800 rounded">Geç Teslim</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sub->status === 'Graded' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $sub->status === 'Graded' ? 'Puanlandı' : 'Bekliyor' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-primary">
                                    {{ $sub->score ? $sub->score->score . ' / ' . $sub->score->max_score : '--' }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <form action="{{ route('admin.assignments.submissions.evaluate', $assignment->id) }}" method="POST" class="flex items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                        <input type="number" name="score" required min="0" max="100" value="{{ $sub->score->score ?? '' }}" placeholder="Puan" class="w-16 text-xs bg-neutral-50 border rounded px-1 py-1 font-bold">
                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white text-[10px] font-bold rounded hover:bg-green-700">Kaydet</button>
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
