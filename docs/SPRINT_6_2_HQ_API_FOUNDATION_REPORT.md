# Sprint 6.2: HQ Communication Layer Foundation

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminin gelecekte HQ Panel ile güvenli ve yetkilendirilmiş şekilde haberleşebilmesi amacıyla API Token temelli iletişim altyapısı (bearer token) ve veri paketleme şemaları oluşturulmuştur.

## Yapılan Değişiklikler

### 1. Database & Models
- `hq_api_tokens` tablosu oluşturuldu.
- `HqApiToken` modeli ve `token`, `name`, `last_used_at`, `expires_at`, `is_active` alanları eklendi.

### 2. Service Katmanı (HQApiService)
- `generateToken()`: Super Admin tarafından tetiklenebilecek 64 karakterli secure API token (`Str::random(64)`) üretir. Aktif olan eski tokenları iptal eder.
- `revokeToken()`: Token'ı deaktive eder.
- `validateToken()`: Gelen token'ın geçerliliğini ve süresini kontrol eder, `last_used_at` alanını günceller.
- `getActiveToken()`: Aktif olan token'ı getirir.
- `pingResponse()`: Temel bağlantı testi için array döner.
- `healthPayload()` / `systemPayload()`: `installation_uuid`, `system_uuid`, `version`, `license`, `features`, `active_users` ve `timestamp` verilerini içeren JSON paket şemalarını oluşturur.

### 3. Yönetim Paneli (Admin UI) & Routing
- `HQApiController` oluşturuldu. Token görüntüleme, yeniden oluşturma ve iptal etme işlevlerini üstlenir.
- `resources/views/admin/platform/api/index.blade.php` arayüzü eklendi.
- Route'lar Super Admin iznine tabi olarak `routes/admin.php` içerisine eklendi.

### 4. API Middleware (HQApiMiddleware)
- `HQApiMiddleware` oluşturuldu. İstekteki `Authorization: Bearer TOKEN` başlığını `HQApiService` aracılığıyla doğrular. Geçersiz isteklerde `401 Unauthorized` döner.

### 5. Executive Dashboard Entegrasyonu
- Dashboard üzerinde API Token'ın aktiflik ve son geçerlilik tarihini gösteren yeni bir durum alanı eklendi.

### 6. Kalite Güvencesi & Testler
- `tests/Feature/HQApiTest.php` eklendi.
- Tüm senaryolar başarıyla test edildi:
  - Token üretimi, token doğrulama/iptal etme.
  - Geçersiz ve süresi dolmuş tokenların reddedilmesi.
  - Payload içeriğinin doğruluğu.
  - Admin sayfası erişimi.
  - Middleware yetkilendirmesi.

## Sonuç
Sistem, HQ paneliyle gerçekleştirilecek olan yetkilendirilmiş API haberleşmesi (Bearer) ve telemetri/sağlık verisi paketleri için hazır hale getirilmiştir. 
Bir sonraki sprintte gerçek HQ senkronizasyonu hazırlanabilir.
