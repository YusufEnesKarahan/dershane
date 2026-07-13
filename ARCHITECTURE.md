# Architecture Decisions (Mimari Kararlar)

Bu belge, Dershane / Etüt Merkezi / Eğitim Kurumu Yönetim Sistemi SaaS projesinde alınan mimari kararları ve gerekçelerini açıklar.

## 1. Single Codebase, Multi-Package (Tek Kod Tabanı, Üç Farklı Paket)
Proje tek bir kod tabanında geliştirilecek ancak lisans/paket durumuna göre 3 farklı versiyon olarak satılacaktır:
- **Version 1 (Kurumsal Web Sitesi)**
- **Version 2 (Kurumsal + Yönetim)**
- **Version 3 (Full Eğitim ERP)**

### Mimari Çözüm: Feature Flags & Middleware
Paket sınırları ve erişim yetkileri, `config/features.php` üzerinden yönetilen dinamik Feature Flag'ler ve bunlara bağlı çalışan Middleware katmanı (`FeatureFlagMiddleware`) aracılığıyla kontrol edilir. İş kuralları kod içinde doğrudan ayrılmak yerine, özellik bazlı flag kontrolleri ile sınırlandırılır.

## 2. Feature-First & Action-Domain Yaklaşımı
Geleneksel MVC yapısı büyüdükçe Controller ve Model sınıflarının aşırı büyümesine (Fat Controllers/Models) neden olur. Bunu engellemek için:
- **Actions:** Her bir iş kuralı tek sorumluluğa sahip `Action` sınıflarına bölünmüştür (örneğin: `RegisterStudentAction`). Controller'lar yalnızca istekleri alır ve ilgili Action'ı tetikler.
- **Domains:** İş kuralları veritabanından bağımsız olarak Domain servislerinde çözülür.
- **ViewModels:** View katmanına gönderilecek veriler Controller'da hazırlanmak yerine, ilgili `ViewModel` sınıfları içinde organize edilir. Bu sayede View'lar sade kalır ve test edilebilirlik artar.

## 3. Veri Erişim Katmanı (Repository Pattern)
Veritabanı erişimini soyutlamak ve sorguların tekrarını önlemek için `Repository` yapısı kullanılacaktır.
- Veritabanı değişimlerinde (SQLite -> MySQL) veya test yazımında Mock nesnelerin kolay kullanımı için arayüzler (`Contracts`) tanımlanacaktır.
- ORM olarak Laravel Eloquent kullanılacak, ancak sorgular doğrudan Controller içinde değil, Repositories içinde yazılacaktır.

## 4. White-Label & Theming Hazırlığı
Gelecekte farklı kurumlara kendi marka ve renkleriyle (White-Label) satılabilmesi için:
- Renkler, logolar, fontlar ve arayüz düzenleri `config/brand.php` dosyasında yapılandırılmıştır.
- Blade şablonlarında inline renk kodları yerine CSS değişkenleri (CSS Variables) ve Tailwind CSS config eşleşmesi kullanılacaktır.

## 5. Çevre ve Veritabanı Ayrımı
- **Geliştirme Ortamı (Development):** Kolaylık ve hız için dosya tabanlı SQLite veritabanı kullanılır.
- **Canlı Ortam (Production / Staging):** Yüksek performans ve ölçeklenebilirlik için ilişkisel MySQL veritabanı kullanılır.
- Çevreye özgü ayarlar `.env` dosyası üzerinden kontrol edilir.
