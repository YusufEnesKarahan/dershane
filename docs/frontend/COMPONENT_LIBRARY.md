# Component Library (Bileşen Kütüphanesi)

Bu kılavuz, `resources/views/components/` altında tanımlanan Blade bileşenlerinin kullanım biçimlerini kod örnekleriyle açıklar.

## 1. Button Bileşeni (`<x-button>`)
Butonlar farklı varyantlar, boyutlar ve yüklenme durumları ile kullanılabilir.

```html
<!-- Primary Button -->
<x-button variant="primary" size="md">Kaydet</x-button>

<!-- Outline Button with Loading state -->
<x-button variant="outline" size="md" :loading="true">Gönderiliyor</x-button>

<!-- Ghost Button (Disabled) -->
<x-button variant="ghost" size="sm" :disabled="true">Pasif</x-button>
```

---

## 2. Card Bileşeni (`<x-card>`)
Kartlar, veri gruplarını veya içerikleri organize etmek için kullanılır.

```html
<x-card variant="feature" title="Matematik Programı" subtitle="TYT-AYT Odaklı Hazırlık">
    Matematik derslerimizde yeni nesil sorulara ve problem çözme stratejilerine odaklanıyoruz.
    
    <x-slot name="footer">
        <span>Fiyat: 15.000 TL</span>
        <x-button size="sm">İncele</x-button>
    </x-slot>
</x-card>
```

---

## 3. Input Bileşeni (`<x-input>`)
Tek bir bileşenden metin alanı, şifre girişi, çok satırlı metin (`textarea`) ve açılır liste (`select`) yönetilir.

```html
<!-- Standart Text Input -->
<x-input name="student_name" label="Öğrenci Adı" placeholder="Ahmet Yılmaz" required="true" />

<!-- Textarea Input -->
<x-input name="notes" label="Notlar" type="textarea" placeholder="Öğrenci hakkında detaylar..." />

<!-- Select Input -->
<x-input name="branch" label="Şube Seçin" type="select">
    <option value="kadikoy">Kadıköy</option>
    <option value="besiktas">Beşiktaş</option>
</x-input>
```

---

## 4. Modal Bileşeni (`<x-modal>`)
AlpineJS ile tetiklenen modal bileşeni. Bir modali açmak için global `open-modal` veya kapatmak için `close-modal` event'i fırlatılır.

```html
<!-- Trigger Button -->
<button @click="$dispatch('open-modal', { name: 'student_modal' })" class="px-4 py-2 bg-indigo-600 text-white rounded">
    Öğrenci Ekle
</button>

<!-- Modal Definition -->
<x-modal name="student_modal" title="Yeni Öğrenci Kaydı" size="lg">
    <x-input name="first_name" label="Adı" />
    <x-input name="last_name" label="Soyadı" />
    
    <x-slot name="footer">
        <x-button variant="outline" @click="$dispatch('close-modal', { name: 'student_modal' })">İptal</x-button>
        <x-button variant="primary">Kaydet</x-button>
    </x-slot>
</x-modal>
```
