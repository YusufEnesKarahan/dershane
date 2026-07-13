@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $about = $demo->getAbout();
    $contact = $demo->getContact();

    $seo = [
        'title' => 'Hakkımızda | Kurumsal Hikayemiz',
        'description' => $about['mission'],
        'keywords' => 'hakkımızda, vizyon, misyon, başarılarımız, tarihçe'
    ];
@endphp

@push('styles')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Ana Sayfa",
      "item": "{{ url('/') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Kurumsal",
      "item": "{{ url('/kurumsal/hakkimizda') }}"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Hakkımızda",
      "item": "{{ url('/kurumsal/hakkimizda') }}"
    }
  ]
}
</script>
@endpush

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Hakkımızda</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">{{ $about['story'] }}</p>
        </x-container>
    </x-section>

    <!-- MISSION & VISION -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="p-8 bg-neutral-50 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
                    <div class="h-12 w-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl mb-6">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-neutral mb-3">Misyonumuz</h3>
                    <p class="text-sm text-neutral/70 leading-relaxed">{{ $about['mission'] }}</p>
                </div>
                <div class="p-8 bg-neutral-50 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
                    <div class="h-12 w-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl mb-6">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-neutral mb-3">Vizyonumuz</h3>
                    <p class="text-sm text-neutral/70 leading-relaxed">{{ $about['vision'] }}</p>
                </div>
            </div>
        </x-container>
    </x-section>

    <!-- VALUES -->
    <x-section bg="gray" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Değerlerimiz" subtitle="Eğitim anlayışımızın temelini oluşturan değişmez ilkelerimiz." />
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($about['values'] as $value)
                    <div class="text-center p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm">
                        <span class="font-semibold text-neutral">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>

    <!-- TIMELINE -->
    <x-section bg="white" py="16" class="border-b border-neutral-100">
        <x-container>
            <x-section-header title="Tarihçemiz" subtitle="Kuruluşumuzdan günümüze başarı dolu yolculuğumuz." />
            <div class="max-w-3xl mx-auto space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-neutral-200 before:to-transparent">
                @foreach ($about['timeline'] as $item)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-primary text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-premium-xl bg-white border border-neutral-100 shadow-premium-sm">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-neutral">{{ $item['title'] }}</h4>
                                <span class="text-xs font-semibold text-primary px-2 py-1 bg-primary/10 rounded-full">{{ $item['year'] }}</span>
                            </div>
                            <p class="text-sm text-neutral/70">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>

@endsection
