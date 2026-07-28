# Sprint 6.7: HQ Scheduler & Auto Sync Foundation

## Genel Bakış
Dershane ERP sisteminin ileride HQ Central Management Platform ile haberleşmesini tam otomatik hale getirmek için gerekli olan **Scheduler ve Görev Çalıştırma (Task Execution) Altyapısı** kurulmuştur. Sistem güvenliğini ve mevcut kararlılığı korumak adına tüm asenkron veya planlanmış görevler (scheduler) varsayılan olarak DEVRE DIŞI (`disabled`) bırakılmış olup, istenildiğinde yapılandırma (config) üzerinden aktifleştirilebilir durumdadır. Hiçbir tehlikeli veya dinamik kod çalıştırma işlemine izin verilmemiş, sadece izin verilen komutların (whitelist) güvenli bir şekilde çalıştırılması hedeflenmiştir.

## Yapılan Değişiklikler

### 1. Yapılandırma (Configuration)
- `config/hq.php` dosyasına `scheduler` dizisi eklendi.
- Varsayılan olarak `enabled => false` yapıldı. Interval ayarları (`telemetry_interval`, `heartbeat_interval`, `sync_interval`) eklendi.

### 2. Veritabanı ve Loglama
- `hq_scheduler_logs` tablosu oluşturuldu. Bu tablo, çalıştırılan her zamanlanmış görevin (task) başlangıç/bitiş zamanını, başarısını/hatasını ve işlem detaylarını kaydeder.
- `HQSchedulerLog` modeli eklenerek UUID desteği ve JSON cast işlemleri yapılandırıldı.

### 3. Servis Katmanı
- **`HQSchedulerService`** oluşturuldu:
  - `executeTask($taskName, callable $closure)`: Verilen iş mantığını çalıştırır, süresini ölçer, hataları yakalar ve veritabanına loglar.
  - `runTelemetry()`: Telemetri snapshot verisini toplayıp gönderir.
  - `runHeartbeat()`: HQ'ya basit bir ping gönderir.
  - `processSyncQueue()`: Bekleyen senkronizasyon kuyruğunu kontrol eder (Foundation aşamasında sadece mock olarak çalışmaktadır).

### 4. Console Commands & Zamanlanmış Görevler
- Üç yeni izole Artisan komutu oluşturuldu:
  - `hq:telemetry`
  - `hq:heartbeat`
  - `hq:sync`
- Her bir komut `config('hq.scheduler.enabled')` kontrolü yaparak, kapalı olması durumunda güvenle (0 kodla) sonlanır.
- `routes/console.php` üzerinden bu komutlar cron pattern'ine bağlanarak kaydedildi.

### 5. Yönetim Paneli (Admin UI) & Dashboard
- `HQSchedulerController` ve ilgili `index.blade.php` arayüzü eklendi (`/admin/platform/scheduler`). Sistem durumu, son kalp atışı ve hatalı görev sayıları gösteriliyor.
- `ExecutiveDashboardController` güncellenerek "HQ Automation Status" metrikleri yönetici panosu dashboard'una yerleştirildi.

### 6. Güvenlik Notları
- Sistem hiçbir şekilde dinamik komut (exec, shell_exec, system, eval vb.) çalıştırmaz.
- Tüm görevler Laravel'in güvenli `Command` altyapısında kapsüllenmiştir ve izinli (whitelist) metotları çalıştırır.

### 7. Testler
- `tests/Feature/HQSchedulerTest.php` eklendi.
  - Görevlerin varsayılan olarak engellendiği (disabled state) test edildi.
  - Başarılı görev ve hata veren görev senaryolarının veritabanına doğru şekilde kaydedildiği doğrulandı.
  - Yönetim paneli yetkilendirme (Super Admin erişimi) test edildi.
  - İzinsiz/tehlikeli komutların (`hq:exec` vs) mevcut olmadığı assert edildi.

## Sonuç
Dershane ERP, güvenliği ihlal etmeyen ve kontrol edilebilir (configurable) bir zamanlanmış görev altyapısına sahiptir. Bu Foundation, ERP'nin HQ ortamına veri gönderip alabilmesi için ihtiyaç duyduğu otomatik döngüyü sağlam temellere oturtmaktadır.
