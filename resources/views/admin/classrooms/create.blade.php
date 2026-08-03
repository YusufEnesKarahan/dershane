@extends('layouts.admin')

@section('title', 'Yeni Sınıf Ekle')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Yeni Sınıf Ekle</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sisteme yeni bir sınıf ekleyin.</p>
        </div>
        <a href="{{ route('admin.classrooms.index') }}" class="btn-secondary">İptal ve Geri Dön</a>
    </div>

    @if($errors->any())
        <div class="mb-6">
            <x-alert type="danger">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6">
            <x-alert type="danger">
                {{ session('error') }}
            </x-alert>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <form action="{{ route('admin.classrooms.store') }}" method="POST">
            @csrf
            
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-medium text-slate-800 dark:text-white mb-4">Sınıf Bilgileri</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sınıf Adı <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sınıf Kodu</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Boş bırakılırsa otomatik üretilir"
                            class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="classroom_type_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sınıf Türü</label>
                        <select name="classroom_type_id" id="classroom_type_id" 
                            class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Seçiniz</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('classroom_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Öğrenci Kapasitesi <span class="text-rose-500">*</span></label>
                        <input type="number" min="1" name="capacity" id="capacity" value="{{ old('capacity', 30) }}" required
                            class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="color_code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Renk Kodu</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" name="color_code" id="color_code" value="{{ old('color_code', '#4F46E5') }}"
                                class="h-10 w-10 border-0 rounded p-0 bg-transparent">
                            <input type="text" value="{{ old('color_code', '#4F46E5') }}" readonly
                                class="form-input flex-1 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white bg-slate-50 dark:bg-slate-800">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label for="teacher_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sorumlu Öğretmen</label>
                        <select name="teacher_id" id="teacher_id" 
                            class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Atanmamış</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name ?? 'Bilinmiyor' }} {{ $teacher->title ? "({$teacher->title})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sınıfın ana rehber/sorumlu öğretmenini belirleyin.</p>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="form-checkbox h-5 w-5 text-primary-600 rounded border-slate-300 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-700">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Bu Sınıf Aktif mi?</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 dark:bg-slate-800/50 flex justify-end rounded-b-xl">
                <button type="submit" class="btn-primary">
                    Sınıfı Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('color_code').addEventListener('input', function(e) {
        this.nextElementSibling.value = e.target.value;
    });
</script>
@endsection
