# Sprint 6.1: HQ Central Panel Integration Foundation

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminin ileride HQ Central Management Panel tarafından yönetilebilmesi için temel entegrasyon altyapısı kurulmuştur. HQ ile gerçek bir API veya senkronizasyon (sync) bağı kurulmamış olup, tamamen sistem içi (local) kimlikleme ve durum özeti çıkarma mekanizmaları hazırlanmıştır. 

## Yapılan Değişiklikler

### 1. Database & Models
- `system_identity` tablosu için migration oluşturuldu.
- `SystemIdentity` modeli oluşturuldu ve yeni kayıt açıldığında otomatik olarak `uuid` ve `installation_uuid` oluşturacak şekilde `boot()` metodu yazıldı.

### 2. Service Katmanı
- `HQIntegrationService` implemente edildi.
  - `getInstanceInformation()`: `SystemIdentity` bilgisini kontrol edip yoksa oluşturur.
  - `getSystemVersion()`: `UpdateService` üzerinden güncel versiyonu çeker.
  - `getLicenseStatus()`: `LicenseService` üzerinden güncel lisans bilgisini çeker.
  - `getEnabledFeatures()`: `FeatureFlagService` üzerinden aktif olan modülleri listeler.
  - `getHealthSummary()`: Sistem sağlık durumu için statik local bir özet döner.

### 3. Yönetim Paneli
- `HQIntegrationController` eklendi.
- `resources/views/admin/platform/hq_integration/index.blade.php` oluşturuldu. Arayüz "Offline (Local)" bağlantı durumuyla beraber sistem UUID'lerini, lisansını ve sağlık durumunu sergileyecek modern, readonly (salt okunur) bir sayfa yapısına kavuşturuldu.
- Route'lar Super Admin iznine tabi olarak `routes/admin.php` içerisine eklendi.

### 4. Executive Dashboard Entegrasyonu
- `ExecutiveDashboardController` güncellenerek `HQIntegrationService` dahil edildi.
- Dashboard arayüzünde Sistem UUID'sini ve bağlantı durumunu gösteren yeni bir bilgi satırı eklendi.

### 5. Kalite Güvencesi & Testler
- `tests/Feature/HQIntegrationTest.php` oluşturuldu.
- Tüm senaryolar başarıyla test edildi:
  - Identity oluşumu, UUID validasyonu.
  - Servis veri çekme doğrulaması.
  - Admin sayfasının ve Dashboard widget'ının hatasız render edilmesi.

## Sonuç
Sistem gelecekteki HQ paneline API çağrılarıyla bağlanacak gerekli local kimliğe (identity) ve özet servislere kavuşmuştur. 
Bir sonraki sprintte gerçek HQ bağlantısı hazırlanabilir.
