# Sprint 6.4: HQ Secure Communication (HTTP Sync v1)

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminin HQ Central Platform ile **ilk gerçek, manuel ve güvenli HTTP haberleşme** altyapısı kurulmuştur. İletişim süreçleri tamamen manuel tetiklemeli (buton bazlı) olup, herhangi bir asenkron kuyruk veya zamanlanmış (cron) görev kullanılmamıştır. 

## Yapılan Değişiklikler

### 1. Database & Models
- `hq_sync_logs` tablosu için migration oluşturuldu.
- `HQSyncLog` modeli eklendi (`event_type`, `request_url`, `request_method`, `request_payload`, `response_status`, `response_payload`, `duration_ms`, `success`, `created_at`). Bu tablo, HQ Panel'e yapılan her isteğin giriş/çıkışlarını denetlemek amacıyla audit (denetim) logu olarak çalışır.

### 2. Yapılandırma (Config)
- `config/hq.php` yapılandırma dosyası oluşturuldu/üzerine yazıldı (`base_url`, `timeout`, `enabled`, `verify_ssl`). Çevre değişkenleri (.env) üzerinden kontrol edilebilir yapıda bırakıldı.

### 3. Service Katmanı
- **`SignatureService`**: HMAC SHA256 kullanarak `X-Signature` üretimi yapan hafif bir servis.
- **`HQHttpService`**: Laravel `Http` istemcisini (client) sarmalayan (wrapper) ve tüm HQ isteklerini merkezi bir yapıdan (`send` metodu) yürüten temel iletişim katmanı. 
  - Timeout, retry ve header (`X-System-UUID`, `X-Installation-UUID`, `X-System-Version`, `X-License`, `Authorization Bearer`, `X-Signature`) eklemeleri otomatik yapılır.
  - İletişim hataları yakalanarak (catch) `HQSyncLog` tablosuna başarılı/başarısız tüm denemeler kaydedilir.
  - Ping, Health, Register ve Manual Sync için proxy metodlar içerir.

### 4. Yönetim Paneli (Admin UI) & Routing
- `HQCommunicationController` oluşturuldu. 
- `resources/views/admin/platform/communication/index.blade.php` arayüzü ile:
  - HQ bağlantı durum özeti (Son başarılı Ping süresi baz alınarak) gösterildi.
  - Manuel Ping, Health Gönderimi, Register ve Sync aksiyonları butonlara bağlandı.
  - Tablo halinde son 20 HTTP iletişim logu listelendi.
- `routes/admin.php` içerisinde `/admin/platform/communication` rotaları `Super Admin` yetkisine bağlandı.

### 5. Executive Dashboard Entegrasyonu
- Dashboard üzerindeki metrikler genişletilerek **"HQ Connection"** kartı eklendi. (Aktif bağlantı durumu, Son Ping saati, Son Sync saati).

### 6. Kalite Güvencesi & Testler
- `tests/Feature/HQCommunicationTest.php` eklendi.
- `Http::fake()` kullanılarak dış ağ bağlantısı yapılmaksızın tüm senaryolar (Başarılı Ping, Sağlık Bildirimi, Manuel Senkronizasyon, Başarısız istek (500) loglanması, İmza (HMAC) üretimi) test edildi.

## Sonuç
Sistem, HQ ile veri paylaşabilmek için hazır bir HTTP istemci katmanına kavuşmuştur. Sistemde otomatik bir senkronizasyon (auto-sync) tanımlanmamıştır; tüm eylemler şimdilik platform yönetimi altındaki manuel butonlara bağlıdır. Bu sayede üretim (production) aşamasında kontrollü iletişim sağlanabilecektir.
