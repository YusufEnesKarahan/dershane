@props([
    'title' => 'Eğitim Kurumu Yönetim Sistemi',
    'subtitle' => 'Modern, hızlı ve ölçeklenebilir eğitim yönetim çözümleri.',
])

<div class="bg-indigo-700 text-white py-20 px-6 text-center">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">{{ $title }}</h1>
        <p class="text-xl text-indigo-100 mb-8">{{ $subtitle }}</p>
        <div class="flex justify-center space-x-4">
            {{ $slot }}
        </div>
    </div>
</div>
