@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $faqs = $demo->getFaq();

    $seo = [
        'title' => 'Sıkça Sorulan Sorular | SSS',
        'description' => 'Eğitim sistemimiz, sınıflarımız ve kayıt süreçlerimiz hakkında merak edilen tüm soruların cevapları.',
        'keywords' => 'sss, sıkça sorulan sorular, eğitim, kayıt, sınıflar'
    ];
@endphp

@push('styles')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $question => $answer)
    {
      "@type": "Question",
      "name": "{{ $question }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $answer }}"
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Sıkça Sorulan Sorular</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Merak ettiğiniz soruların cevaplarını burada bulabilirsiniz.</p>
        </x-container>
    </x-section>

    <!-- FAQ LIST -->
    <x-section bg="white" py="16">
        <x-container>
            <div class="max-w-3xl mx-auto space-y-4">
                @foreach ($faqs as $question => $answer)
                    <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm group hover:border-primary/20 transition-colors">
                        <h3 class="text-lg font-bold text-neutral mb-2">{{ $question }}</h3>
                        <p class="text-sm text-neutral/70 leading-relaxed">{{ $answer }}</p>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
