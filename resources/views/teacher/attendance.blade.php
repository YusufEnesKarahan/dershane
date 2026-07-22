@extends('layouts.admin')
@section('title', 'Ders Yoklama Girişi')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Yoklama Oturumu Seçimi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Ders Oturum Seçimi</h3>
            <p class="text-xs text-neutral-400">Yoklama listesini görüntülemek için ilgili ders oturumunu seçin.</p>
            
            <form method="GET" action="{{ route('teacher.attendance') }}" class="space-y-4">
                <x-admin.form.field-group label="Oturumlar" id="session_id">
                    <select name="session_id" onchange="this.form.submit()" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        <option value="">Lütfen Oturum Seçiniz</option>
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}" {{ $session && $session->id === $sess->id ? 'selected' : '' }}>
                                {{ $sess->session_date }} - {{ $sess->classroom->name }} (Oturum: {{ $sess->id }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>
            </form>
        </div>

        <!-- Sağ Panel: Yoklama Listesi & Kaydetme Formu -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            @if(!$session)
                <div class="text-center text-xs text-neutral-400 py-12">Lütfen sol panelden bir ders oturumu seçin.</div>
            @else
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yoklama Listesi: {{ $session->classroom->name }}</h3>
                        <p class="text-xs text-neutral-400 mt-0.5">Oturum Tarihi: {{ $session->session_date }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('teacher.attendance.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">

                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci Adı Soyadı</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($students as $st)
                                <tr>
                                    <td class="px-4 py-3 text-xs font-bold text-neutral-900 font-mono">{{ $st->student_number }}</td>
                                    <td class="px-4 py-3 text-xs font-semibold text-neutral-700">{{ $st->first_name }} {{ $st->last_name }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        <select name="records[{{ $st->id }}]" class="text-xs bg-neutral-50 border border-neutral-200 rounded px-2 py-1">
                                            <option value="Present">Katıldı</option>
                                            <option value="Absent">Katılmadı</option>
                                            <option value="Late">Geç Kaldı</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-xs text-neutral-400">Sınıfta henüz kayıtlı öğrenci bulunmamaktadır.</td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Yoklama Girişini Tamamla & Kaydet
                        </button>
                    </div>
                </form>
            @endif
        </div>

    </div>
@endsection
