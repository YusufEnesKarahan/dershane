@extends('layouts.templates.landing')

@php
    $seo = [
        'title' => ucfirst(str_replace('-', ' ', $slug)) . ' | Blog',
        'description' => 'Blog yazımızı okuyun ve eğitim dünyasındaki en güncel bilgilere ulaşın.',
        'keywords' => str_replace('-', ', ', $slug) . ', blog, makale, rehberlik'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10">
            <a href="{{ route('blogs.index') }}" class="inline-flex items-center text-sm font-semibold text-primary mb-6 hover:text-white transition-colors">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tüm Yazılara Dön
            </a>
            
            <div class="flex items-center space-x-4 text-sm text-neutral-400 mb-6">
                <span>12 Mart 2026</span>
                <span>•</span>
                <span class="bg-primary/20 text-primary px-3 py-1 rounded-full">Rehberlik Servisi</span>
                <span>•</span>
                <span>4 dk okuma</span>
            </div>

            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-6 capitalize leading-tight">
                {{ str_replace('-', ' ', $slug) }}
            </h1>

            <div class="flex items-center mt-8">
                <div class="h-12 w-12 rounded-full bg-neutral-800 flex items-center justify-center border-2 border-neutral-700">
                    <span class="text-white font-bold">R</span>
                </div>
                <div class="ml-4">
                    <p class="text-white font-semibold">Rehberlik Servisi</p>
                    <p class="text-neutral-400 text-sm">Uzman Danışman</p>
                </div>
            </div>
        </x-container>
    </x-section>

    <!-- BLOG CONTENT -->
    <x-section bg="white" py="16">
        <x-container>
            <div class="max-w-3xl mx-auto">
                <div class="aspect-[21/9] bg-neutral-100 rounded-premium-2xl mb-12 overflow-hidden shadow-premium-sm border border-neutral-100">
                    <img src="/assets/branding/og-image.jpg" alt="Blog Görseli" class="w-full h-full object-cover">
                </div>

                <article class="prose prose-neutral prose-lg max-w-none">
                    <p class="lead text-xl text-neutral/80 font-medium mb-8">Bu makalede öğrencilerin akademik başarısını artıracak temel stratejilere ve dikkat edilmesi gereken kritik noktalara değineceğiz.</p>
                    
                    <h2 class="text-2xl font-bold font-display text-neutral mt-10 mb-4">Düzenli Çalışmanın Önemi</h2>
                    <p class="text-neutral/80 leading-relaxed mb-6">Başarının temel anahtarı düzenli ve planlı çalışmaktan geçer. Zaman yönetimini doğru yapmak, öğrencinin verimliliğini doğrudan etkiler. Günlük, haftalık ve aylık çalışma programları oluşturmak bu sürecin ilk adımıdır.</p>

                    <h2 class="text-2xl font-bold font-display text-neutral mt-10 mb-4">Deneme Sınavlarının Rolü</h2>
                    <p class="text-neutral/80 leading-relaxed mb-6">Sadece konu çalışmak yeterli değildir. Düzenli olarak deneme sınavlarına girmek, öğrencinin sınav stresini yönetmesini ve eksik konularını tespit etmesini sağlar. Kurumumuzda her hafta uygulanan Türkiye geneli deneme sınavları tam olarak bu amaca hizmet etmektedir.</p>

                    <blockquote class="border-l-4 border-primary pl-6 py-2 my-8 bg-neutral-50 rounded-r-xl italic text-neutral/80">
                        "Eğitim bir varış noktası değil, sürekli devam eden bir yolculuktur."
                    </blockquote>

                    <h2 class="text-2xl font-bold font-display text-neutral mt-10 mb-4">Sonuç Olarak</h2>
                    <p class="text-neutral/80 leading-relaxed">Öğrenci, veli ve kurum işbirliği ile ulaşılamayacak hiçbir hedef yoktur. Rehberlik servisimiz, bu yolculukta öğrencilerimize her zaman destek olmaya hazırdır.</p>
                </article>

                <!-- Share & Tags -->
                <div class="mt-12 pt-8 border-t border-neutral-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-neutral">Etiketler:</span>
                        <a href="#" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-600 text-sm rounded-full transition-colors">Eğitim</a>
                        <a href="#" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-600 text-sm rounded-full transition-colors">Rehberlik</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-semibold text-neutral">Paylaş:</span>
                        <div class="flex gap-2">
                            <button class="h-8 w-8 rounded-full bg-neutral-100 hover:bg-primary hover:text-white text-neutral-500 flex items-center justify-center transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </button>
                            <button class="h-8 w-8 rounded-full bg-neutral-100 hover:bg-primary hover:text-white text-neutral-500 flex items-center justify-center transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>
@endsection
