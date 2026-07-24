@extends('layouts.templates.landing')

@php
    $seo = [
        'title' => ucfirst(str_replace('-', ' ', $slug)) . ' Kursu Detayları',
        'description' => 'Eğitim programımız hakkında detaylı bilgi, müfredat ve kayıt avantajları.',
        'keywords' => str_replace('-', ', ', $slug) . ', kurs detayları, eğitim, müfredat'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10">
            <a href="{{ route('frontend.courses.index') }}" class="inline-flex items-center text-sm font-semibold text-primary mb-6 hover:text-white transition-colors">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tüm Kurslara Dön
            </a>
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4 capitalize">
                {{ str_replace('-', ' ', $slug) }}
            </h1>
            <p class="text-neutral-400 max-w-2xl text-lg">Öğrencilerimizi başarıya taşıyan kapsamlı ve hedefe yönelik müfredatımız.</p>
        </x-container>
    </x-section>

    <!-- COURSE DETAILS -->
    <x-section bg="white" py="16">
        <x-container>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-12">
                    <article class="prose prose-neutral max-w-none">
                        <h2 class="text-2xl font-bold font-display text-neutral">Program Hakkında</h2>
                        <p class="text-neutral/80 leading-relaxed">Bu eğitim programı, öğrencilerin akademik hedeflerine ulaşmaları için özel olarak tasarlanmıştır. Yüksek başarı oranımız, uzman kadromuz ve modern eğitim materyallerimizle geleceğinizi şekillendiriyoruz.</p>
                        
                        <h3 class="text-xl font-bold font-display text-neutral mt-8">Müfredat İçeriği</h3>
                        <ul class="space-y-4 mt-4">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-primary shrink-0 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-neutral/80">Kapsamlı Konu Anlatımları ve Soru Çözüm Stratejileri</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-primary shrink-0 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-neutral/80">Yeni Nesil Sorulara Uygun Deneme Sınavları (Her Hafta)</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-primary shrink-0 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-neutral/80">Birebir Mentorluk ve Veli Bilgilendirme Sistemi</span>
                            </li>
                        </ul>
                    </article>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-neutral-50 rounded-premium-2xl border border-neutral-100 p-6 shadow-premium-sm sticky top-8">
                        <h3 class="text-xl font-bold font-display text-neutral mb-4">Hemen Başvurun</h3>
                        <p class="text-sm text-neutral/70 mb-6">Erken kayıt avantajlarından ve özel indirimlerden yararlanmak için ön kayıt formunu doldurun.</p>
                        
                        <a href="{{ route('frontend.pre-register') }}" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-premium-sm text-white bg-primary hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Ön Kayıt Ol
                        </a>
                        
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <h4 class="text-sm font-bold text-neutral mb-3">Programa Dahil Olanlar</h4>
                            <ul class="space-y-2 text-sm text-neutral/70">
                                <li>✓ Tüm basılı yayınlar</li>
                                <li>✓ Sınırsız etüt hakkı</li>
                                <li>✓ Dijital kütüphane erişimi</li>
                                <li>✓ Psikolojik danışmanlık</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>
@endsection
