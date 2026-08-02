@extends('layouts.guest')

@section('content')
<div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-md border border-neutral-100 dark:border-neutral-800">
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Welcome to Dershane ERP</h1>
        <p class="text-sm text-neutral-500 mt-2">Let's set up your institution in just a few steps.</p>
    </div>

    <!-- Stepper -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $step >= 1 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500' }} font-bold text-sm">
                1
            </div>
            <div class="w-12 h-1 {{ $step >= 2 ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $step >= 2 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500' }} font-bold text-sm">
                2
            </div>
        </div>
    </div>

    @if($step == 1)
        <!-- Step 1: System Identity -->
        <form action="{{ route('admin.onboarding.identity') }}" method="POST">
            @csrf
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Step 1: Institution Details</h2>
            
            <x-admin.form.field-group label="Company Name *" id="company_name" class="mb-4" error="{{ $errors->first('company_name') }}">
                <input type="text" name="company_name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5" placeholder="e.g. Boğaziçi Eğitim Kurumları A.Ş.">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Brand Name *" id="brand_name" class="mb-6" help="This will be displayed on the application header." error="{{ $errors->first('brand_name') }}">
                <input type="text" name="brand_name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5" placeholder="e.g. Boğaziçi Dershanesi">
            </x-admin.form.field-group>

            <x-admin.button type="submit" variant="primary" class="w-full">
                Continue to Next Step
            </x-admin.button>
        </form>
    @elseif($step == 2)
        <!-- Step 2: Academic Term -->
        <form action="{{ route('admin.onboarding.term') }}" method="POST">
            @csrf
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Step 2: Active Academic Term</h2>
            <p class="text-sm text-neutral-500 mb-4">Set up your current academic year. You can change this later.</p>

            <x-admin.form.field-group label="Term Name *" id="name" class="mb-4" error="{{ $errors->first('name') }}">
                <input type="text" name="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5" placeholder="e.g. 2026-2027 Academic Year">
            </x-admin.form.field-group>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <x-admin.form.field-group label="Start Date *" id="start_date" error="{{ $errors->first('start_date') }}">
                    <input type="date" name="start_date" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5">
                </x-admin.form.field-group>
                <x-admin.form.field-group label="End Date *" id="end_date" error="{{ $errors->first('end_date') }}">
                    <input type="date" name="end_date" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5">
                </x-admin.form.field-group>
            </div>

            <x-admin.button type="submit" variant="primary" class="w-full">
                Complete Setup
            </x-admin.button>
        </form>
    @endif
</div>
@endsection
