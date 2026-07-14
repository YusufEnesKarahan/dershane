@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <x-admin.crud.index-layout title="Overview" description="Welcome back to your administration dashboard.">
        <x-slot name="actions">
            <button class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                Generate Report
            </button>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-admin.widgets.stat title="Total Students" value="1,240" trend="12" />
            <x-admin.widgets.stat title="Active Courses" value="48" trend="4" />
            <x-admin.widgets.stat title="New Leads" value="156" trend="-2" />
            <x-admin.widgets.stat title="Revenue" value="₺450K" trend="18" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <x-admin.widgets.chart-placeholder title="Revenue Growth" />
            <x-admin.widgets.chart-placeholder title="Student Enrollment" />
        </div>
        
        <div class="mt-8">
            <h3 class="text-lg font-medium text-neutral-900 dark:text-white mb-4">Recent Registrations</h3>
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
    </x-admin.crud.index-layout>
@endsection