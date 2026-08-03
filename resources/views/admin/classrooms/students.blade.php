@extends('layouts.admin')

@section('title', 'Öğrenci Yönetimi: ' . $classroom->name)

@section('content')
<div class="p-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Öğrenci Yönetimi: {{ $classroom->name }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Bu sınıfa öğrenci atayın veya mevcut öğrencileri çıkarın.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.classrooms.show', $classroom->id) }}" class="btn-secondary">Sınıf Detayına Dön</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4">
            <x-alert type="success" dismissible="true">
                {{ session('success') }}
            </x-alert>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4">
            <x-alert type="danger" dismissible="true">
                {{ session('error') }}
            </x-alert>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1 min-h-0">
        
        <!-- Available Students -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                <h2 class="text-lg font-medium text-slate-800 dark:text-white">Uygun Öğrenciler</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                    {{ $availableStudents->count() }} Öğrenci
                </span>
            </div>
            
            <form action="{{ route('admin.classrooms.students.attach', $classroom->id) }}" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="p-3 border-b border-slate-200 dark:border-slate-700">
                    <input type="text" id="search-available" placeholder="İsim ile ara..." class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                </div>
                
                <div class="flex-1 overflow-y-auto p-2">
                    @if($availableStudents->count() > 0)
                        <ul class="space-y-1" id="available-list">
                            @foreach($availableStudents as $student)
                            <li>
                                <label class="flex items-center p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-600 transition-all">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-checkbox h-5 w-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-700">
                                    <div class="ml-3 flex-1 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white student-name">{{ $student->user->name ?? 'Bilinmiyor' }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->student_number }}</p>
                                        </div>
                                    </div>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="h-full flex items-center justify-center text-slate-500 dark:text-slate-400 p-8 text-center">
                            <p class="text-sm">Atanabilecek uygun öğrenci bulunamadı.</p>
                        </div>
                    @endif
                </div>
                
                <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <button type="submit" class="btn-primary w-full flex justify-center items-center" {{ $availableStudents->count() == 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                        Seçilileri Sınıfa Ekle
                    </button>
                </div>
            </form>
        </div>

        <!-- Enrolled Students -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                <h2 class="text-lg font-medium text-slate-800 dark:text-white">Sınıftaki Öğrenciler</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Kapasite: {{ $classroom->capacity }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enrolledStudents->count() >= $classroom->capacity ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $enrolledStudents->count() }} Öğrenci
                    </span>
                </div>
            </div>
            
            <form action="{{ route('admin.classrooms.students.detach', $classroom->id) }}" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="p-3 border-b border-slate-200 dark:border-slate-700">
                    <input type="text" id="search-enrolled" placeholder="İsim ile ara..." class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                </div>
                
                <div class="flex-1 overflow-y-auto p-2">
                    @if($enrolledStudents->count() > 0)
                        <ul class="space-y-1" id="enrolled-list">
                            @foreach($enrolledStudents as $student)
                            <li>
                                <label class="flex items-center p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-600 transition-all">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-checkbox h-5 w-5 text-rose-600 rounded border-slate-300 focus:ring-rose-500 dark:border-slate-600 dark:bg-slate-700">
                                    <div class="ml-3 flex-1 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white student-name">{{ $student->user->name ?? 'Bilinmiyor' }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->student_number }}</p>
                                        </div>
                                    </div>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="h-full flex items-center justify-center text-slate-500 dark:text-slate-400 p-8 text-center">
                            <p class="text-sm">Sınıfa kayıtlı öğrenci bulunmuyor.</p>
                        </div>
                    @endif
                </div>
                
                <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors" {{ $enrolledStudents->count() == 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                        Seçilileri Sınıftan Çıkar
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    // Simple client-side search for the lists
    function setupSearch(inputId, listId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        
        if (!input || !list) return;
        
        input.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const items = list.querySelectorAll('li');
            
            items.forEach(item => {
                const name = item.querySelector('.student-name').textContent.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupSearch('search-available', 'available-list');
        setupSearch('search-enrolled', 'enrolled-list');
    });
</script>
@endsection
