@extends('layouts.templates.landing')

@php
    $seo = [
        'title' => 'Ana Sayfa',
        'description' => 'Eğitim kurumları için yeni nesil SaaS dershane ve etüt merkezi yönetim sistemi.',
    ];
@endphp

@section('template-content')
    <!-- Hero Banner -->
    <x-hero title="Geleceğin Eğitim Platformu" subtitle="Dershaneler, etüt merkezleri ve dil kursları için özel olarak tasarlanmış premium yönetim ve otomasyon ERP altyapısı.">
        <x-button variant="primary" size="lg" onclick="window.location.href='/on-kayit'">Hemen Ön Kayıt Ol</x-button>
        <x-button variant="outline" size="lg" onclick="window.location.href='/style-guide'">Arayüz Tasarım Rehberi</x-button>
    </x-hero>

    <!-- Features Section -->
    <x-section bg="gray" py="16">
        <x-container>
            <x-section-header title="Eğitim Kurumunuz İçin Her Şey Tek Bir Çatı Altında" subtitle="SOLID prensipleriyle kodlanmış, modüler ve yüksek performanslı kurumsal çözüm." />
            
            <x-feature-grid cols="3">
                <x-card variant="feature" title="Tek Kod Tabanı (Multi-Tenant)" subtitle="SaaS Mimarisi">
                    Tek bir kod tabanı üzerinden kurumunuzun tüm şube ve lisans seviyelerini kolayca yönetin.
                </x-card>
                <x-card variant="feature" title="Esnek Fiyatlandırma" subtitle="Sürüm Yönetimi">
                    Sistem 3 farklı lisans paketiyle (V1 Web Sitesi, V2 Yönetim Paneli, V3 Full ERP) esnek satış modellerini destekler.
                </x-card>
                <x-card variant="feature" title="Premium Tasarım" subtitle="UI/UX Standartları">
                    En son Tailwind CSS ve modern arayüz standartlarıyla donatılmış, White-Label uyumlu kurumsal arayüz.
                </x-card>
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- Stats Banner -->
    <x-section bg="white" py="12">
        <x-container>
            <x-stats :items="[
                'Aktif Modül' => '18+',
                'Yüklenme Skoru' => '99/100',
                'Erişilebilirlik' => 'WCAG AA',
                'Şube Desteği' => 'Sınırsız'
            ]" />
        </x-container>
    </x-section>

    <!-- Bottom CTA -->
    <x-section bg="gray" py="16">
        <x-container>
            <x-cta title="Kurumunuzu Bugün Dijitalleştirin" subtitle="Hemen ön kayıt formumuzu doldurarak erken kayıt avantajlarından faydalanın ve sistemi ilk deneyimleyenlerden olun.">
                <x-button variant="primary" onclick="window.location.href='/on-kayit'">Ön Kayıt Başvurusu Yap</x-button>
                <x-button variant="outline" onclick="window.location.href='/kurumsal/hakkimizda'">Detaylı Bilgi Al</x-button>
            </x-cta>
        </x-container>
    </x-section>
@endsection
