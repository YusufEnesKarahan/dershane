@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Tasarım Rehberi (Style Guide)',
        'description' => 'Sistem genelinde kullanılan tüm görsel arayüz elemanlarının, butonların, form girdilerinin ve modüllerin canlı gösterimi.',
        'robots' => 'noindex, nofollow'
    ];

    $breadcrumbs = [
        'Tasarım Rehberi' => route('frontend.style-guide')
    ];

    $faqItems = [
        'Tasarım Sistemi Hangi Grid Üzerine Kuruludur?' => 'Tasarım sistemi 4px grid yapısına dayalıdır. padding, margin ve konumlandırmalar 4\'ün katları olarak düzenlenmiştir.',
        'White-Label Tema Desteği Nasıl Çalışır?' => 'Tasarım sisteminde kullanılan renkler, marka config dosyasından beslenen CSS değişkenleri yardımıyla dinamik olarak ezilebilir.',
    ];

    $timelineSteps = [
        'Aşama 1: Tasarım Sistemi' => 'CSS Tokenları ve temel blade bileşenlerinin kodlanması.',
        'Aşama 2: Sayfa Şablonları' => 'Default, Listing, Detail, Landing ve Legal yerleşim şablonları.',
        'Aşama 3: Yayına Alma' => 'Vite varlıklarının optimize derlenmesi ve test edilmesi.',
    ];

    $statsItems = [
        'Toplam Modül' => '18+',
        'Tema Desteği' => '100%',
        'Yüklenme Hızı' => '0.4s',
        'Erişilebilirlik' => 'WCAG AA',
    ];
@endphp

@section('content')
    <x-page-header title="Tasarım Rehberi (Style Guide)" :breadcrumbs="$breadcrumbs" />

    <x-container class="py-12 space-y-16">
        
        <!-- Section 1: Buttons -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="1. Buton Tasarımları (x-button)" subtitle="Farklı varyantlar, boyutlar ve yükleme/pasif durumlarının listesi" align="left" />
            
            <div class="space-y-6">
                <!-- Variants -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Varyantlar (V1/V2/V3)</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <x-button variant="primary">Primary (Birincil)</x-button>
                        <x-button variant="secondary">Secondary</x-button>
                        <x-button variant="outline">Outline (Çerçeveli)</x-button>
                        <x-button variant="ghost">Ghost (Hayalet)</x-button>
                        <x-button variant="success">Success</x-button>
                        <x-button variant="danger">Danger (Tehlike)</x-button>
                        <x-button variant="link">Link Buton</x-button>
                    </div>
                </div>

                <!-- Sizes -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Boyutlar (Sizes)</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <x-button variant="primary" size="sm">Small (sm)</x-button>
                        <x-button variant="primary" size="md">Medium (md)</x-button>
                        <x-button variant="primary" size="lg">Large (lg)</x-button>
                    </div>
                </div>

                <!-- States -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Durumlar (States)</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <x-button variant="primary" :disabled="true">Pasif (Disabled)</x-button>
                        <x-button variant="primary" :loading="true">Yükleniyor (Loading)</x-button>
                    </div>
                </div>
            </div>
        </x-section>

        <!-- Section 2: Form Elements -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="2. Form Elemanları (x-input, x-checkbox)" subtitle="Metin girdileri, seçim kutuları ve doğrulama durumları" align="left" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Text Inputs -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Metin Alanları</h4>
                    <x-input name="sample_text" label="Adı Soyadı" placeholder="Ahmet Yılmaz" hint="Kullanıcının tam adını giriniz." />
                    <x-input name="sample_email" label="E-posta Adresi" type="email" value="deneme@site.com" :required="true" />
                    <x-input name="sample_error" label="Telefon Numarası" placeholder="5xx xxx xx xx" error="Geçersiz telefon numarası formatı." />
                </div>

                <!-- Textarea & Select -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Seçim ve Açıklama</h4>
                    <x-input name="sample_select" label="Eğitim Şubesi" type="select">
                        <option value="kadikoy">Kadıköy Merkez Şubesi</option>
                        <option value="besiktas">Beşiktaş Şubesi</option>
                    </x-input>
                    <x-input name="sample_textarea" label="Ön Kayıt Notu" type="textarea" placeholder="Eklemek istediğiniz notlar..." />
                </div>
            </div>

            <!-- Checkboxes, Radios and Toggle switches -->
            <div class="mt-8 pt-8 border-t border-neutral-100 space-y-4">
                <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Çoklu Seçim, Tekli Seçim ve Switch</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-checkbox name="check_1" label="KVKK Metnini onaylıyorum" hint="Yasal başvuru için onay verilmelidir." :checked="true" />
                    <x-checkbox name="radio_1" type="radio" label="Erkek Öğrenci" />
                    <x-checkbox name="switch_1" type="switch" label="Aktiflik Durumu" hint="Kullanıcı durumunu aktif etmek için açın." :checked="true" />
                </div>
            </div>
        </x-section>

        <!-- Section 3: Feedback & Status -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="3. Bildirim ve Uyarılar (x-alert, x-badge)" subtitle="Durum bildirimleri ve rozet stilleri" align="left" />
            
            <div class="space-y-6">
                <!-- Alerts -->
                <div class="space-y-3">
                    <x-alert type="success" :dismissible="true">İşleminiz başarıyla tamamlandı. Ön kayıt talebiniz alındı.</x-alert>
                    <x-alert type="info">Yeni eğitim dönemi erken kayıt başvuruları başlamıştır.</x-alert>
                    <x-alert type="warning">Sistem bakımı bu gece saat 02:00\'de yapılacaktır.</x-alert>
                    <x-alert type="danger" :dismissible="true">Ön kayıt gönderilirken bir sunucu hatası oluştu.</x-alert>
                </div>

                <!-- Badges -->
                <div class="space-y-2 pt-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Rozetler (Badges)</h4>
                    <div class="flex flex-wrap gap-2">
                        <x-badge color="primary">Primary</x-badge>
                        <x-badge color="secondary">Secondary</x-badge>
                        <x-badge color="gray">Gray</x-badge>
                        <x-badge color="green">Aktif (Green)</x-badge>
                        <x-badge color="yellow">Beklemede (Yellow)</x-badge>
                        <x-badge color="red">İptal Edildi (Red)</x-badge>
                        <x-badge color="blue">Bilgi (Blue)</x-badge>
                    </div>
                </div>
            </div>
        </x-section>

        <!-- Section 4: Interactives -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="4. Etkileşimli Elemanlar (AlpineJS)" subtitle="Modal pencereleri, açılır listeler ve akordeon yapılar" align="left" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Dropdown & Modal triggers -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Dropdown ve Modal Pencereleri</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Dropdown -->
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <x-button variant="outline">İşlemler Menüsü [v]</x-button>
                            </x-slot>
                            <x-slot name="content">
                                <a href="#" class="block px-4 py-2 text-xs text-neutral hover:bg-neutral-50 transition duration-150">Profili Düzenle</a>
                                <a href="#" class="block px-4 py-2 text-xs text-neutral hover:bg-neutral-50 transition duration-150">Şube Değiştir</a>
                                <div class="border-t border-neutral-100 my-1"></div>
                                <a href="#" class="block px-4 py-2 text-xs text-danger hover:bg-red-50 transition duration-150">Kaydı Sil</a>
                            </x-slot>
                        </x-dropdown>

                        <!-- Modal trigger -->
                        <x-button variant="primary" @click="$dispatch('open-modal', { name: 'demo_modal' })">Modali Aç</x-button>
                    </div>

                    <!-- Modal window -->
                    <x-modal name="demo_modal" title="Örnek Detay Penceresi" size="md">
                        <p class="text-xs text-neutral/70 leading-relaxed mb-4">
                            Bu modal penceresi AlpineJS ile tetiklenir, ESC tuşu veya dışarıya tıklama ile otomatik olarak kapanır. WCAG uyumluluğu için odak yönetimi barındırır.
                        </p>
                        <x-input name="modal_input" label="Hızlı Kayıt Girişi" placeholder="Veri girin..." />

                        <x-slot name="footer">
                            <x-button variant="outline" @click="$dispatch('close-modal', { name: 'demo_modal' })">Kapat</x-button>
                            <x-button variant="primary">Kaydet</x-button>
                        </x-slot>
                    </x-modal>
                </div>

                <!-- FAQ Accordion -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Akordeon SSS (x-faq)</h4>
                    <x-faq :items="$faqItems" />
                </div>
            </div>
        </x-section>

        <!-- Section 5: Layout Elements -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="5. Düzen ve Gösterim Elemanları" subtitle="Sayfa iskeletleri, kronolojik akışlar ve istatistik kartları" align="left" />
            
            <div class="space-y-8">
                <!-- Stats -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">İstatistik Göstergeleri (x-stats)</h4>
                    <x-stats :items="$statsItems" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-neutral-100">
                    <!-- Timeline -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Kronolojik Yol Haritası (x-timeline)</h4>
                        <x-timeline :steps="$timelineSteps" />
                    </div>

                    <!-- Empty state -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Boş Liste Görünümü (x-empty-state)</h4>
                        <x-empty-state title="Aranan Ders Bulunamadı" subtitle="Arama kriterlerinizi değiştirerek tekrar deneyebilirsiniz.">
                            <x-slot name="actions">
                                <x-button variant="outline" size="sm">Filtreleri Temizle</x-button>
                            </x-slot>
                        </x-empty-state>
                    </div>
                </div>
            </div>
        </x-section>

        <!-- Section 6: Loaders -->
        <x-section bg="white" py="12" class="border border-neutral-100 rounded-premium-2xl p-8 shadow-premium-sm">
            <x-section-header title="6. Yükleme Animasyonları (Loaders)" subtitle="Yüklenme durumlarındaki animasyonlar ve skeleton yapıları" align="left" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Spinners -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">Dönen Yükleyiciler (x-spinner)</h4>
                    <div class="flex gap-4 items-center">
                        <x-spinner size="sm" />
                        <x-spinner size="md" color="secondary" />
                        <x-spinner size="lg" />
                    </div>
                </div>

                <!-- Skeletons -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-neutral/50 uppercase tracking-wider">İskelet Yükleyiciler (x-skeleton)</h4>
                    <div class="space-y-3">
                        <x-skeleton type="text" />
                        <div class="flex items-center gap-3">
                            <x-skeleton type="avatar" />
                            <x-skeleton type="text" class="w-1/2" />
                        </div>
                        <x-skeleton type="card" class="h-20" />
                    </div>
                </div>
            </div>
        </x-section>

    </x-container>
@endsection
