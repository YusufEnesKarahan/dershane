# Sprint 6.6: HQ Telemetry & Monitoring Foundation

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminin performans, sistem bilgisi, kullanım istatistikleri ve sağlık durumunu izlemek üzere "Telemetry & Monitoring Foundation" entegre edilmiştir. Hazırlanan telemetri yapısı tamamen sadece-okunur (read-only) mantığıyla çalışır ve toplanan verilerin HQ Panel'e güvenli bir şekilde, sadece manuel olarak gönderilmesine (snapshot) izin verir.

## Yapılan Değişiklikler

### 1. Database & Model
- `hq_telemetry_logs` tablosu oluşturuldu (`uuid`, `type`, `payload`, `status`, `generated_at`).
- `HQTelemetryLog` modeli oluşturularak UUID üretimi entegre edildi. Tablo tamamen toplanan metrik snapshot'larını denetim/history amaçlı saklamak için kullanılmaktadır.

### 2. Service Katmanı
- **`HQTelemetryService`** oluşturuldu. İçerdiği metotlar:
  - `collectHealth()`: Veritabanı ve önbellek bağlantılarını test ederek sağlığı doğrular.
  - `collectSystem()`: Laravel, PHP sürümleri, environment ve benzersiz System UUID değerlerini toplar.
  - `collectUsage()`: Toplam ve aktif kullanıcı/şube sayılarını, aktif SaaS feature sayısını toplar.
  - `collectPerformance()`: Anlık RAM ve Storage (Disk) tüketim oranlarını raporlar.
  - `createSnapshot()` & `storeSnapshot()`: Üstteki tüm metotları toplayıp JSON yapısında paketler ve veritabanına yedekler.
- **`HQHttpService`** güncellendi:
  - `sendTelemetry(array $payload)` metodu eklendi. Sistem, mevcut HMAC Signature ve HQ API Token sistemini kullanarak, güvenli HTTP isteğini HQ noktasına fırlatır.

### 3. Yönetim Paneli (Admin UI) & Dashboard
- `HQTelemetryController` oluşturuldu. `/admin/platform/telemetry` rotasına bağlandı.
- Arayüz (index.blade.php):
  - Güncel System Health, Database, Storage durum kartları.
  - Aktif kullanım sayıları ve son telemetri (Last Telemetry) zamanı kartları.
  - "Send Telemetry Snapshot" butonu eklendi (Otomatik gönderim yoktur, sadece butonla HQ'ya iletilir).
  - Geçmiş telemetri kayıtlarının listelendiği tablo.
- `ExecutiveDashboardController`: Dashboard'daki metrikleri zenginleştirerek "HQ Telemetry Status" bileşenini ekledi.

### 4. Güvenlik Notları
- Sistem hiçbir şekilde dinamik komut (exec, shell_exec, system, eval vb.) çalıştırmaz. 
- Veriler yalnızca standart PHP fonksiyonlarıyla (`memory_get_usage`, `disk_free_space`) ve Laravel'in standart cache/DB bağlantıları test edilerek okunur. Telemetri mekanizması tamamen pasif ve izoledir.
- Gönderimler, daha önce kurulan `SignatureService` (HMAC SHA256) kullanılarak güvence altına alınmıştır.

### 5. Testler
- `tests/Feature/HQTelemetryTest.php` geliştirildi.
  - Veri toplama algoritmaları, snapshot üretimi ve loglama test edildi.
  - Güvenlik yetkilendirmesi (sadece Super Admin erişimi) ve admin panel erişim limitleri doğrulandı.
  - HTTP mock ile ağ katmanı simüle edilerek giden veriler test edildi.

## Sonuç
Dershane ERP, güvenliği ihlal etmeyen, harici sistemlerin (HQ'nun) anlık durum sorgulaması yapabileceği güvenli, raporlanabilir bir Telemetry altyapısına sahiptir. Snapshot'lar manuel onay mekanizmasına bağlanarak production (canlı) sistemin güvenilirliği korunmuştur.
