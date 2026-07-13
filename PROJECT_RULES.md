# Project Rules (Proje Kuralları)

Bu dosya, projede yer alan tüm geliştiricilerin uyması gereken temel yazılım geliştirme kurallarını içerir. Yazılan her satır kod ticari bir SaaS ürününe yakışır kalitede olmalıdır.

## 1. Kodlama Standartları ve Kalite
- **Strict Types:** Tüm PHP dosyalarının en başında `declare(strict_types=1);` yer almalıdır.
- **PSR Standartları:** Kod yazımında PSR-12 (ve güncel PSR standartları) kuralları eksiksiz uygulanacaktır.
- **Statik Analiz:** PHPStan seviyesi minimum Seviye 8 olarak hedeflenmiştir. Yazılan kodlarda tip belirtimleri tam olmalıdır.
- **Kod Biçimlendirme:** Değişiklikler yapılmadan önce ve yapıldıktan sonra Laravel Pint (`vendor/bin/pint`) ile kod temizlenmelidir.

## 2. Mimari Kurallar
- **SOLID Prensipleri:** Sınıflar tek sorumluluk taşımalı (Single Responsibility), genişlemeye açık ancak değişime kapalı olmalıdır (Open-Closed).
- **YAGNI (You Aren't Gonna Need It):** Şu an ihtiyaç duyulmayan hiçbir ekstra özellik veya kod bloğu eklenmeyecektir.
- **KISS (Keep It Simple, Stupid):** Karmaşık çözümler yerine olabildiğince basit ve anlaşılır mantık tercih edilecektir.
- **DRY (Don't Repeat Yourself):** Tekrar eden kodlar uygun Helper, Trait, Service veya Domain sınıflarına taşınmalıdır.
- **Dependency Injection:** Bağımlılıklar construct üzerinden inject edilmeli, ad-hoc `new` anahtar kelimesi ile nesne oluşturmaktan kaçınılmalıdır.

## 3. İsimlendirme Kuralları
- **Dosya ve Sınıf İsimleri:** Sınıf adları PascalCase (`StudentRegistrationService`), interfaceler sonuna `Interface` veya `Contract` takısı almalı, traitler ise `Trait` veya `Has...` şeklinde isimlendirilmelidir.
- **Metot ve Değişken İsimleri:** camelCase (`getStudentDetails`, `$activeVersion`).
- **Veritabanı Tablo ve Sütunları:** snake_case (`student_records`, `first_name`).
- **Route İsimleri:** kebab-case ve modül önekli (`admin.students.store`).

## 4. Git ve Versiyonlama
- **Commit Mesajları:** Anlamlı ve açıklayıcı olmalıdır (örn: `feat(core): add feature flag middleware`).
- **Secret ve API Key:** Kesinlikle kod tabanına veya git geçmişine `.env` dosyası, API key veya hassas veri eklenemez. Tüm hassas veriler çevre değişkenlerinden (Environment Variables) çekilmelidir.
- **Branch Yönetimi:** Geliştirmeler doğrudan `main` branch'e yapılmamalı, `feature/` veya `fix/` branch'leri açılmalıdır.
