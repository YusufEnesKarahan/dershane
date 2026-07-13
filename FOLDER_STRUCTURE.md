# Folder Structure (Klasör Yapısı)

 hard hardening sprinti sonrasında projenin güncel klasör yapısı ve hiyerarşisi aşağıdaki şekildedir:

```text
├── app/
│   ├── Actions/                # Tek sorumluluğu olan iş kuralları (Create, Update, Delete, Restore, Archive)
│   ├── ViewModels/             # View katmanına veri taşıyan sınıflar (Frontend, Admin, Shared)
│   ├── Domains/                # İş odaklı alt domain'ler
│   │   ├── CMS/                # İçerik yönetim alanı
│   │   ├── CRM/                # Müşteri ve aday öğrenci alanı
│   │   ├── ERP/                # Finans, kurum işleri alanı
│   │   ├── Education/          # Dershane/Eğitim süreçleri alanı
│   │   ├── Core/               # Çekirdek iş mantığı
│   │   └── Shared/             # Paylaşılan ortak domain servisleri
│   │
│   ├── Core/                   # Çekirdek mimari yapısı
│   │   ├── Base/               # Ortak taban sınıflar (BaseService, BaseRepository, BaseAction vb.)
│   │   ├── DTO/                # Veri taşıma nesneleri (Pagination, Response, Filter, Search DTO'ları)
│   │   ├── Enums/              # Tip güvenli PHP Enums (Version, Feature, Status, User, Gender, Language, Currency)
│   │   ├── Exceptions/         # Özel hata tanımları (FeatureDisabled, ModuleNotFound, Theme, Validation)
│   │   ├── Helpers/            # Küresel fonksiyonlar (Helpers.php)
│   │   ├── Support/            # Özel Laravel koleksiyonları (Feature, Module, Menu, Theme Koleksiyonları)
│   │   └── Services/           # Çekirdek servisler (FeatureManager, ThemeManager, SettingsManager, BrandManager)
│   │
│   ├── Providers/              # Servis sağlayıcılar (Repository, Feature, Theme, Settings, Domain)
│   └── Http/                   # İstek ve Middleware katmanları
│
├── config/                     # Konfigürasyon dosyaları (Brand, Features, Settings, Theme, Modules, Permissions, Roles, Menus, Media)
│
├── routes/                     # Decoupled (Ayrıştırılmış) rota yönetimi
│   ├── web.php                 # Ana web giriş kapısı (Sadece alt dosyaları include eder)
│   ├── frontend.php            # Sürüm 1 Kurumsal site rotaları
│   ├── admin.php               # Sürüm 2 & Sürüm 3 yönetim ve ERP rotaları
│   ├── auth.php                # Kimlik doğrulama rotaları
│   └── api.php                 # API uç noktaları rotaları
│
└── docs/                       # Alt başlıklar altında dokümantasyon klasörü
```

## Dizinlerin Sorumlulukları
1. **`app/Core/Base/`**: Tüm alt sınıflar için taban mimariyi oluşturur. Projedeki tüm Action'lar `BaseAction`'ı, tüm DTO'lar `BaseDTO`'yu kalıtım alarak ortak kod tekrarını önler.
2. **`app/Core/Enums/`**: PHP 8.4'ün desteklediği tip güvenli backing enum'lar (`string`) burada tutulur. Bu sayede sihirli kelimeler (magic strings) yerine kontrollü sabitler kullanılır.
3. **`app/Core/DTO/`**: İstek verilerinin doğrulanıp/filtrelenip servisler arasında güvenle dolaşmasını sağlayan nesnelerdir.
4. **`routes/`**: Rota karmaşıklığını önlemek için rotalar web.php içinden çıkartılıp işlevlerine göre ayrı dosyalara taşınmıştır.
