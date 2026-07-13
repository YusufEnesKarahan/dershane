# Folder Structure (Klasör Yapısı)

Proje, gelecekte büyüyebilecek, karmaşıklığı azaltacak ve ekip çalışmasına uygun **Feature-First** ve **Domain-Driven** odaklı bir klasör yapısını benimsemektedir.

```text
├── app/
│   ├── Core/                   # Ortak ve paylaşılan çekirdek bileşenler
│   │   ├── Contracts/          # Ortak arayüzler (Interfaces)
│   │   ├── Services/           # Genel ve entegrasyon servisleri
│   │   ├── Traits/             # Yeniden kullanılabilir PHP Trait'leri
│   │   ├── Helpers/            # Yardımcı sınıflar ve fonksiyonlar
│   │   ├── Enums/              # Sabit değer grupları (Enums)
│   │   ├── Exceptions/         # Özel hata sınıfları (Custom Exceptions)
│   │   ├── DTO/                # Veri Transfer Nesneleri (Data Transfer Objects)
│   │   └── Support/            # Yardımcı diğer destek sınıfları
│   │
│   ├── Features/               # Uygulamanın uç noktaları ve özellik odaklı iş mantığı (Controllers vb.)
│   ├── Domains/                # İş kuralları ve veritabanı dışı iş mantığı alanları
│   ├── Actions/                # Tek bir işi gerçekleştiren sınıflar (Single Action Controllers / Services)
│   ├── ViewModels/             # Görünüm katmanına veri hazırlayan ara sınıflar
│   ├── Policies/               # Yetkilendirme sınıfları
│   └── Repositories/           # Veri erişim soyutlama katmanı (Data Access Layer)
│
├── config/                     # Konfigürasyon dosyaları (Brand, Features, Settings vb.)
│
├── docs/                       # Proje teknik ve mimari dokümantasyonu
│   ├── architecture/           # Genel mimari kararları
│   ├── frontend/               # Arayüz mimarisi ve standartları
│   ├── backend/                # Arka plan servisleri ve API'ler
│   ├── database/               # Veri modelleri, indeksleme ve veritabanı kuralları
│   ├── deployment/             # CI/CD, sunucu kurulum ve canlıya alma
│   ├── coding/                 # Kod yazım kuralları detayları
│   ├── branding/               # White label ve kurumsal kimlik yönetimi
│   ├── features/               # Modüller ve yetenek listeleri
│   ├── modules/                # Büyük ölçekli modüler yapı tasarımları
│   └── versioning/             # Paket versiyon stratejileri (V1, V2, V3)
│
├── resources/                  # Frontend kaynakları
│   ├── views/                  # Blade şablonları
│   │   ├── frontend/           # Kurumsal web sitesi sayfaları (V1)
│   │   ├── admin/              # Yönetici paneli sayfaları (V2 & V3)
│   │   ├── components/         # Yeniden kullanılabilir Blade bileşenleri
│   │   ├── layouts/            # Ana sayfa şablonları (frontend, admin, auth, guest)
│   │   └── errors/             # Hata sayfaları (404, 500 vb.)
│   ├── css/                    # Tailwind CSS dosyaları
│   └── js/                     # JavaScript ve AlpineJS kodları
│
└── routes/                     # Web ve API yönlendirme tanımları
```

## Yapısal Prensipler
1. **Dikey Bölümleme (Vertical Slicing):** Bir özellik eklenirken tüm katmanları (`Action`, `Repository`, `ViewModel`, `View`) birbirine sıkı sıkıya bağlı olmadan ilgili feature klasörü veya katmanı bazında yönetilir.
2. **Core Bağımsızlığı:** `Core` klasöründeki kodlar spesifik iş kurallarından arındırılmış olmalıdır. İş kuralları `Domains` ve `Features` altında çözülür.
3. **Whitelabel Hazırlığı:** `resources/views/layouts` ve `config/brand.php` dosyaları, gelecekteki çoklu marka (White-Label) ve tema özelleştirmelerine uyumlu şekilde yapılandırılmıştır.
