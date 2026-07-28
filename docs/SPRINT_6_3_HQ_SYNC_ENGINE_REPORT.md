# Sprint 6.3: HQ Synchronization Engine Foundation

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminde gerçekleşen önemli iş olaylarını (lisans değişikliği, şube oluşumu vb.) HQ tarafına bildirmek için kullanılacak lokal bir senkronizasyon (sync) kuyruk altyapısı oluşturulmuştur. Bu sistem asenkron çalışan gerçek bir job (queue/worker) değildir; yalnızca veritabanına olayları bir günce (log) şeklinde kaydeden hafif bir kayıt mekanizmasıdır.

## Yapılan Değişiklikler

### 1. Database & Models
- `hq_sync_queue` tablosu için migration oluşturuldu.
- `HQSyncEvent` modeli eklendi (`event_type`, `payload`, `status`, `retry_count`, `last_error`, `processed_at`).

### 2. Service Katmanı (HQSyncService)
- `queue()`: Verilen tip ve payload ile kuyruğa yeni bir "pending" (bekleyen) kayıt ekler.
- `queueLicenseChanged()`, `queueFeatureChanged()`, vb. kolaylaştırıcı (helper) metodlar eklendi, böylece ileride ERP modüllerinden tek satırla kayıt atılabilir.
- `retry()`: Başarısız olan bir kaydı yeniden işlenmek üzere "pending" durumuna çeker.
- `pending()`, `completed()`, `failed()`: Panelde gösterim için 5 dakikalık önbelleklemeli (cache) özet istatistik döndürür.
- `buildPayload()`: Verileri, HQ Panel'in anlayabileceği ortak bir standart formata dönüştürür.

### 3. Yönetim Paneli (Admin UI) & Routing
- `HQSyncController` oluşturuldu. 
- `resources/views/admin/platform/sync/index.blade.php` arayüzü ile lokal kuyruk durumunun özet widget'ları ve son 20 olay kaydı salt okunur (read-only) biçimde listelendi.
- Super Admin iznine tabi olacak şekilde `routes/admin.php` içerisine `/admin/platform/sync` rotası tanımlandı.

### 4. Executive Dashboard Entegrasyonu
- Ana Dashboard arayüzünde mevcut "HQ API" durumunun hemen yanına/altına "HQ Sync Queue" modülü dahil edildi. 

### 5. Kalite Güvencesi & Testler
- `tests/Feature/HQSyncTest.php` eklendi.
- Tüm senaryolar başarıyla test edildi:
  - Event oluşturma ve database yazma (`test_queue_event`).
  - Hata sayacı ve yeniden deneme mekanizması (`test_retry_counter`).
  - JSON payload üretimi (`test_payload_builder`).
  - Dashboard ve admin panel arayüzlerinin render testleri (`test_dashboard_widget`, `test_admin_sync_page`).

## Sonuç
Sistem, HQ ile gerçekleştirilecek haberleşmenin "kayıt ve kuyruk" (record/queue) katmanına lokal olarak kavuşmuştur. Sistemde otomatik bir worker, scheduler veya HTTP gönderimi yapılandırılmamıştır. 
Bir sonraki sprintte (6.4) HQ Panel ile imzalı ve gerçek HTTP haberleşmesi senkronizasyon motoruna entegre edilebilir.
