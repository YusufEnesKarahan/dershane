# Sprint 6.9: HQ Central Management Backend Foundation

## Genel Bakış
Dershane ERP sisteminin diğer SaaS instance'larını yönetebileceği merkezi bir "HQ Panel" gibi çalışabilmesi için Backend Foundation (Altyapı) tamamlanmıştır. Bu sprint ile ERP sistemi artık bir merkez olarak hizmet verebilir ve diğer instance'lardan kayıt (register), yaşam belirtisi (heartbeat), telemetri verisi (telemetry) ve komut sonuçlarını alabilir.

## Mimarisi

### Veritabanı ve Modeller
Sistem mimarisini desteklemek adına `HQ` önekiyle (veya `HQCentral` önekiyle) aşağıdaki izole tablolar oluşturulmuştur:
- **`hq_tenants` (HQTenant):** Müşteri / Tenant tanımını tutar.
- **`hq_system_instances` (HQSystemInstance):** Tenant'lara bağlı ERP instance'larının UUID, çevre türü, versiyon ve durum verilerini tutar.
- **`hq_api_connections` (HQApiConnection):** Bağlantı ve IP kayıtlarını denetim (audit) amaçlı saklar.
- **`hq_telemetry_records` (HQTelemetryRecord):** Uzaktan gelen sağlık, performans ve sistem izleme snapshot'larını kaydeder.
- **`hq_central_commands` (HQCentralCommand):** Merkezden uzak noktalara gidecek asenkron komutları (pending, sent, completed, failed) kuyruklar.
- **`hq_central_sync_logs` (HQCentralSyncLog):** Tüm iletişim (payload & response) loglarını arşivler.

### Servis Katmanı
Domain-Driven Design felsefesine uygun olarak `App\Domain\HQ\Services` içerisine şu servisler eklenmiştir:
- **`TenantService`**: Müşteri profili oluşturur.
- **`SystemRegistryService`**: Gelen ilk kayıt ve düzenli `heartbeat` sinyallerini işleyerek offline/online status güncellemesi yapar.
- **`HQCommandService`**: Instance başına bekleyen komutları çeker ve sonuç geldiğinde (success/fail) durumu günceller.
- **`HQTelemetryService`**: Payload'ları `hq_telemetry_records` tablosuna indirir.
- **`HQMonitoringService`**: Admin paneli için istatistikleri ve agregasyonları hesaplar (otomatik offline markalama dahil).
- **`HQCommunicationService`**: İletişim geçmişini yazar.

### API Katmanı
`routes/api.php` içerisine `/api/hq` prefix'i ile HQ Endpointleri oluşturuldu:
- `POST /api/hq/register`
- `POST /api/hq/heartbeat`
- `POST /api/hq/telemetry`
- `GET /api/hq/commands`
- `POST /api/hq/commands/{id}/result`

### Güvenlik
- Endpointler `App\Http\Middleware\VerifyHQApiSignature` middleware'i ile korunmaktadır.
- İstekler `X-HQ-Signature`, `X-HQ-Timestamp` ve `Authorization: Bearer` kontrolünden geçer.
- Replay Attack önlemi olarak zaman damgası 5 dakikadan eskiyse reddedilir.
- payload verisi HMAC SHA256 (Gizli Anahtar - Secret Key) ile imzalanıp doğrulanır.
- Asla Eval, Exec, dinamik dosya yükleme, shell komutu vb. kod çalıştırılmasına izin verilmez. Sadece API üzerinden haberleşilir.

### Admin Arayüzü
- `/admin/platform/hq-central` sayfasına HQ Central Overview ekranı eklendi.
- Dashboard üzerinde Bağlı Sistemler (Total, Online, Offline, Pending Commands) görünür hale getirildi.

## Testler ve Doğrulama
- `HQBackendTest.php` üzerinden tam teşekküllü senaryolar yazılmıştır:
  - Kayıt işlemleri (Registration).
  - Online duruma çekme (Heartbeat).
  - İmza geçersizlik senaryoları (401 Unauthorized Rejection).
  - Telemetry Payload ayrıştırma.
  - Komut listesini çekme ve result postlama işlemleri başarıyla test edilmiştir.
