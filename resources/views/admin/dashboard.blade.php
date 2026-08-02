@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <x-admin.crud.index-layout title="Overview" description="Welcome back to your administration dashboard.">
        <x-slot name="actions">
            <x-admin.button type="button" variant="primary" icon="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                Generate Report
            </x-admin.button>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-admin.widgets.stat title="Toplam Öğrenci" value="1,240" trend="12" color="blue" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            <x-admin.widgets.stat title="Aktif Kurslar" value="48" trend="4" color="purple" icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            <x-admin.widgets.stat title="Yeni Adaylar" value="156" trend="-2" color="amber" icon="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            <x-admin.widgets.stat title="Aylık Gelir" value="₺450K" trend="18" color="green" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <x-admin.widgets.chart-placeholder title="Revenue Growth" />
            <x-admin.widgets.chart-placeholder title="Student Enrollment" />
        </div>
        
        <div class="mt-8 bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm p-6">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                Recent Registrations
            </h3>
            <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800">
                <x-admin.table.layout>
                <x-slot name="head">
                    <x-admin.table.th>Name</x-admin.table.th>
                    <x-admin.table.th>Course</x-admin.table.th>
                    <x-admin.table.th>Status</x-admin.table.th>
                    <x-admin.table.th>Date</x-admin.table.th>
                </x-slot>
                <x-slot name="body">
                    <tr>
                        <x-admin.table.td>Ahmet Yılmaz</x-admin.table.td>
                        <x-admin.table.td>YKS Sayısal</x-admin.table.td>
                        <x-admin.table.td><span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span></x-admin.table.td>
                        <x-admin.table.td>Today</x-admin.table.td>
                    </tr>
                    <tr>
                        <x-admin.table.td>Ayşe Demir</x-admin.table.td>
                        <x-admin.table.td>LGS Hazırlık</x-admin.table.td>
                        <x-admin.table.td><span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span></x-admin.table.td>
                        <x-admin.table.td>Yesterday</x-admin.table.td>
                    </tr>
                </x-slot>
            </x-admin.table.layout>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection