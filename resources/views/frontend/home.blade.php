@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $seo = [
        'title' => 'Özel Eğitimde Türkiye\'nin Lider Sınav Hazırlık Merkezi',
        'description' => 'YKS, LGS ve Yabancı Dil hazırlığında birebir rehberlik, derece kadrosu ve yeni nesil butik sınıflarla başarıyı yakalayın. Erken kayıt dönemimiz başladı!',
        'keywords' => 'dershane, kurs merkezi, tyt ayt hazirlik, lgs kursu, butik dershane, premium egitim'
    ];

    $hero = $demo->getHeroContent();
    $stats = $demo->getStatistics();
    $features = $demo->getFeatures();
    $programs = $demo->getPrograms();
    $teachers = $demo->getTeachers();
    $stories = $demo->getSuccessStories();
    $testimonials = $demo->getTestimonials();
    $gallery = $demo->getGallery();
    $faqItems = $demo->getFaq();
    $blogs = $demo->getBlogs();
    $contact = $demo->getContact();
@endphp

@push('styles')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "{{ $contact['company_name'] }}",
  "image": "{{ asset(config('brand.logo.light')) }}",
  "@id": "{{ url('/') }}/#localbusiness",
  "url": "{{ url('/') }}",
  "telephone": "{{ $contact['phone'] }}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Caddebostan Mah. Bağdat Caddesi No:245/4",
    "addressLocality": "Kadıköy",
    "addressRegion": "İstanbul",
    "postalCode": "34728",
    "addressCountry": "TR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 40.9734,
    "longitude": 29.0654
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "08:30",
    "closes": "20:00"
  }
}
</script>

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Course",
  "name": "YKS Derece Hazırlık Programı",
  "description": "TYT ve AYT sınavlarına yönelik, butik sınıflarda yoğun soru çözümlü ve birebir rehberlik takipli hazırlık eğitimi.",
  "provider": {
    "@type": "Organization",
    "name": "{{ $contact['company_name'] }}",
    "sameAs": "{{ url('/') }}"
  }
}
</script>

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Ana Sayfa",
    "item": "{{ url('/') }}"
  }]
}
</script>
@endpush

@section('template-content')

    <!-- 1. HERO SECTION -->
    <x-section bg="dark" py="24" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/10">
        <!-- Background Grid Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff03_1px,transparent_1px),linear-gradient(to_bottom,#ffffff03_1px,transparent_1px)] bg-[size:32px_32px]" aria-hidden="true"></div>
        
        <x-container class="relative z-10 flex flex-col items-center text-center space-y-8">
            <!-- Trust Badge & Edition Badge -->
            <div class="flex items-center gap-3 flex-wrap justify-center select-none">
                <span class="inline-flex items-center gap-2 px-3.5 py-1 text-xs font-semibold text-primary bg-primary/10 border border-primary/20 rounded-full tracking-wide animate-pulse">
                    <svg class="h-4 w-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    {{ $hero['badge'] }}
                </span>
                
                <!-- Helper/Directive test badge rendering -->
                {!! edition_badge() !!}
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-7xl font-display font-extrabold tracking-tight leading-none text-white max-w-4xl">
                {!! preg_replace('/(Sınır Tanımayan)/', '<span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">$1</span>', $hero['title']) !!}
            </h1>

            <!-- Subheadline -->
            <p class="text-sm sm:text-lg text-neutral-300 font-sans max-w-2xl leading-relaxed">
                {{ $hero['subtitle'] }}
            </p>

            <!-- Call to Actions -->
            <div class="flex flex-wrap justify-center gap-4 pt-2">
                <x-button variant="primary" size="lg" onclick="window.location.href='/on-kayit'" class="shadow-premium-xl">Hemen Ön Kayıt Ol</x-button>
                <x-button variant="outline" size="lg" onclick="window.location.href='/kurslar'" class="text-white border-neutral-700 hover:bg-neutral-800/40">Eğitim Programları</x-button>
            </div>

            <!-- Success metrics directly below CTAs -->
            <div class="pt-8 border-t border-neutral-800 w-full max-w-3xl grid grid-cols-4 gap-4 text-left sm:text-center select-none text-neutral-400">
                @foreach ($stats as $label => $val)
                    <div>
                        <span class="block text-xl sm:text-2xl font-bold text-white tracking-tight">{{ $val }}</span>
                        <span class="text-[10px] sm:text-xs uppercase tracking-wider text-neutral-500 font-semibold">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>

    <!-- 2. WHY CHOOSE US Section -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Neden {{ config('brand.name') }}?" subtitle="Klasik eğitim metotlarının ötesine geçerek öğrencilerimizi hedeflerine ulaştıran ayrıcalıklarımız." />
            
            <x-feature-grid cols="3">
                @foreach ($features as $feat)
                    <x-card variant="feature" title="{{ $feat['title'] }}">
                        {{ $feat['description'] }}
                    </x-card>
                @endforeach
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- 3. EDUCATION PROGRAMS Section -->
    <x-section bg="gray" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Eğitim Programlarımız" subtitle="Sınav türlerine ve sınıf seviyelerine göre özelleştirilmiş, başarı garantili kurs paketleri." />
            
            <x-feature-grid cols="3">
                @foreach ($programs as $prog)
                    <x-card variant="course" title="{{ $prog['title'] }}" subtitle="{{ $prog['subtitle'] }}">
                        {{ $prog['description'] }}
                        <x-slot name="footer">
                            <span class="font-semibold text-primary">{{ $prog['footer_text'] }}</span>
                            <x-button size="sm" onclick="window.location.href='{{ $prog['link'] }}'">İncele</x-button>
                        </x-slot>
                    </x-card>
                @endforeach
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- 4. TEACHERS Section -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Uzman Eğitmen Kadromuz" subtitle="Öğrencilerimizi başarıya taşıyan, Türkiye derecelerine imza atmış seçkin öğretmenlerimiz." />
            
            <x-feature-grid cols="4">
                @foreach ($teachers as $t)
                    <x-card variant="team" title="{{ $t['name'] }}" subtitle="{{ $t['role'] }}">
                        {{ $t['bio'] }}
                    </x-card>
                @endforeach
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- 5. SUCCESS STORIES Section -->
    <x-section bg="gray" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Gurur Tablomuz" subtitle="Geçtiğimiz dönem hayallerindeki üniversitelere ve liselere yerleştirdiğimiz öğrencilerimizin başarıları." />
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($stories as $story)
                    <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm text-center space-y-3">
                        <span class="inline-flex px-3 py-1 bg-green-50 text-green-800 border border-green-200 text-xs font-semibold rounded-full select-none">{{ $story['badge'] }}</span>
                        <h4 class="text-base font-bold text-neutral">{{ $story['student'] }}</h4>
                        <p class="text-xs text-neutral/50">{{ $story['destination'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>

    <!-- 6. TESTIMONIALS Section -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Velilerimiz ve Öğrencilerimiz Ne Diyor?" subtitle="Bizi deneyimlemiş kişilerin ağzından başarı hikayelerimiz." />
            
            <x-feature-grid cols="3">
                @foreach ($testimonials as $t)
                    <x-card variant="info" title="{{ $t['author'] }}" subtitle="{{ $t['role'] }}">
                        {{ $t['quote'] }}
                    </x-card>
                @endforeach
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- 7. GALLERY PREVIEW Section -->
    <x-section bg="gray" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Eğitim Alanlarımız ve Yaşam Alanı" subtitle="Öğrencilerimizin konforlu ve odaklanmış bir şekilde çalışabileceği modern fiziki ortamımız." />
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($gallery as $g)
                    <div class="group relative rounded-premium-xl overflow-hidden shadow-premium-md aspect-video bg-neutral-200">
                        <div class="absolute inset-0 bg-neutral-950/40 opacity-80 z-10"></div>
                        <div class="absolute bottom-4 left-4 z-20 text-white">
                            <span class="text-xs font-semibold block uppercase tracking-wider">{{ $g['title'] }}</span>
                            <span class="text-[10px] text-white/70">{{ $g['subtitle'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>

    <!-- 8. FAQ Section -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Sıkça Sorulan Sorular" subtitle="Kayıt süreçleri, eğitim modellerimiz ve şubelerimiz hakkında merak edilenler." />
            
            <x-faq :items="$faqItems" />
        </x-container>
    </x-section>

    <!-- 9. RECENT BLOGS Section -->
    <x-section bg="gray" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Rehberlik Köşesi & Duyurular" subtitle="Sınav taktikleri, çalışma programları ve uzmanlarımızdan tavsiyeler." />
            
            <x-feature-grid cols="3">
                @foreach ($blogs as $b)
                    <x-card variant="blog" title="{{ $b['title'] }}" subtitle="{{ $b['meta'] }}">
                        {{ $b['snippet'] }}
                        <x-slot name="footer">
                            <span>{{ $b['read_time'] }}</span>
                            <x-button size="sm" variant="ghost" onclick="window.location.href='{{ $b['link'] }}'">Oku</x-button>
                        </x-slot>
                    </x-card>
                @endforeach
            </x-feature-grid>
        </x-container>
    </x-section>

    <!-- 10. CONTACT & MAP Section -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="İletişim & Şubelerimiz" subtitle="Bizi ziyaret etmek veya bilgi almak için aşağıdaki kanallardan ulaşabilirsiniz." />
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Address detail cards -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="p-6 bg-neutral-50 rounded-premium-xl border border-neutral-100 flex items-start gap-4">
                        <div class="h-10 w-10 bg-primary/10 text-primary flex items-center justify-center rounded-full shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div class="font-sans">
                            <h4 class="text-sm font-semibold text-neutral">Kadıköy Merkez Şubesi</h4>
                            <p class="text-xs text-neutral/60 mt-1">{{ $contact['address'] }}</p>
                        </div>
                    </div>

                    <div class="p-6 bg-neutral-50 rounded-premium-xl border border-neutral-100 flex items-start gap-4">
                        <div class="h-10 w-10 bg-primary/10 text-primary flex items-center justify-center rounded-full shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div class="font-sans">
                            <h4 class="text-sm font-semibold text-neutral">Telefon Numaralarımız</h4>
                            <p class="text-xs text-neutral/60 mt-1">Sabit: {{ $contact['phone'] }}</p>
                            @if(isset($contact['socials']['whatsapp']))
                                <p class="text-xs text-neutral/60">WhatsApp: {{ str_replace('https://wa.me/', '+', $contact['socials']['whatsapp']) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Google Maps Placeholder with premium styling -->
                <div class="lg:col-span-2 rounded-premium-2xl overflow-hidden border border-neutral-100 shadow-premium-md relative min-h-[300px] bg-neutral-100 flex items-center justify-center">
                    <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] bg-neutral-50 flex flex-col items-center justify-center p-6 text-center">
                        <div class="h-12 w-12 bg-primary/10 text-primary flex items-center justify-center rounded-full mb-3 shadow-premium-sm">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-neutral">İnteraktif Google Harita</h4>
                        <p class="text-xs text-neutral/50 max-w-sm mt-1 leading-normal">
                            Kadıköy şubemiz Caddebostan Kültür Merkezi\'ne 5 dakika yürüme mesafesindedir.
                        </p>
                        <x-button variant="outline" size="sm" class="mt-4" onclick="window.open('https://maps.google.com', '_blank')">Haritada Göster</x-button>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>

    <!-- 11. STRONG BOTTOM CTA -->
    <x-section bg="gray" py="24">
        <x-container>
            <x-cta title="Geleceğinizi Şansa Bırakmayın" subtitle="Sınırlı VIP butik sınıf kontenjanlarımız dolmadan yerinizi ayırtın. Online ön kaydınızı hemen tamamlayın, eğitim danışmanlarımız 24 saat içinde sizi bilgilendirsin.">
                <x-button variant="primary" size="lg" onclick="window.location.href='/on-kayit'" class="shadow-premium-xl">Hemen Ön Kayıt Ol</x-button>
                @if(isset($contact['socials']['whatsapp']))
                    <x-button variant="outline" size="lg" onclick="window.location.href='{{ $contact['socials']['whatsapp'] }}'" class="border-neutral-300 hover:bg-neutral-200/50">
                        <svg class="h-5 w-5 text-green-500 mr-2 shrink-0 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.458L0 24zm6.59-4.846c1.6.95 2.519 1.334 4.545 1.335 5.518.002 10.007-4.484 10.01-10.002.002-2.673-1.037-5.187-2.927-7.078-1.89-1.891-4.407-2.93-7.08-2.93-5.524 0-10.014 4.49-10.017 10.012-.001 2.01.537 3.018 1.49 4.63l-.979 3.573 3.758-.97z"/></svg>
                        WhatsApp Destek
                    </x-button>
                @endif
            </x-cta>
        </x-container>
    </x-section>

@endsection
