# 🏛️ HQ Central Management Platform — Entegrasyon Dokümantasyonu

Bu doküman, **Dershane ERP** projesinin **HQ Central Management Platform** ile güvenli, dayanıklı ve production seviyesinde iletişimini detaylandırmaktadır.

---

## 🔒 1. Güvenlik ve HMAC Mimarisi

HQ ile istemci arasındaki tüm API trafiği **HMAC-SHA256** şifreleme ve Zaman Damgası (Timestamp) doğrulaması ile korunmaktadır.

### İstek Başlıkları (Request Headers):
* **`X-HQ-API-KEY`**: İstemciye üretilen gizli API Secret anahtarı (`hq_sk_...`).
* **`X-HQ-TIMESTAMP`**: İsteğin atıldığı anın Unix zaman damgası (`time()`).
* **`X-HQ-SIGNATURE`**: İsteğin HMAC-SHA256 imzası.

### İmza Üretim Formülü:
```php
$signature = hash_hmac('sha256', $requestBody . $timestamp, $apiSecret);
```

### Zaman Damgası Toleransı (Timestamp Tolerance):
* Sunucu ve istemci arasındaki zaman farkı **en fazla 5 dakika (300 saniye)** olabilir. 5 dakikadan eski veya gelecekteki istekler `401 Unauthorized (Expired Timestamp)` yanıtı alır.

---

## 🔄 2. İletişim Akışları (Workflows)

### A. Register Akışı (İlk Kayıt Handshake)
1. İstemci projede `php artisan hq:register` komutu çalıştırılır.
2. `HqService::register()` istemci UUID'si, site adı, sektörel türü (`dershane`), domain ve PHP/Laravel sürümlerini `POST /api/v1/hq/register` adresine iletir.
3. HQ Sunucusu benzersiz `api_secret` üretir ve veritabanına kaydeder.
4. İstemci gelen `HQ_SITE_UUID` ve `HQ_API_SECRET` değerlerini otomatik olarak `.env` dosyasına yazar.

### B. Sync Akışı (Canlı Senkronizasyon & Kalp Atışı)
1. `hq:sync` komutu Laravel Scheduler aracılığıyla her **15 dakikada bir** çalışır.
2. `HqService::sync()` metrik verilerini (PHP/Laravel sürümü, DB bağlantısı, disk kullanımı %, aktif kullanıcı) paketler.
3. HMAC imzası ile `POST /api/v1/hq/sync` adresine istek gönderir.
4. HQ Yanıtı:
   * **`license_status`**: Güncel lisans durumu (`active`/`inactive`). İstemci yerel cache'i günceller.
   * **`pending_commands`**: HQ tarafından atanan çalıştırılacak komutlar dizisi.

### C. Command & Callback Akışı (Uzaktan Komut Yürütme)
1. HQ'dan dönen komutlar (`clear_cache`, `maintenance`, `license_sync`) `HqService::executePendingCommands()` tarafından yürütülür.
2. İşlem tamamlandıktan hemen sonra `POST /api/v1/hq/command-result` endpoint'ine callback atılır:
   ```json
   {
     "command_id": 10,
     "status": "executed",
     "output": "Cache and config cleared successfully.",
     "executed_at": "2026-07-20T11:16:52+03:00"
   }
   ```
3. HQ Paneli uzaktan komutun durumunu otomatik olarak günceller.

---

## 🛡️ 3. Dayanıklılık, Retry & Exception Yönetimi

* **Sıfır Çökme İlkesi:** HQ sunucusu kapalı olduğunda, 500 hatası verdiğinde veya ağ kesildiğinde **Scheduler veya Queue asla çökmez**.
* **Retry (Yeniden Deneme) Mekanizması:** Başarısız HTTP istekleri `config/hq.php` içinde tanımlanan `retry_count` (varsayılan: 3) ve `retry_delay` (varsayılan: 200ms) ayarlarına göre otomatik tekrar denenir.
* **Timeout Ayarları:** `request_timeout` (10s) ve `connect_timeout` (5s) ile sunucu kilitlenmeleri önlenir.

---

## 📜 4. Loglama (`storage/logs/hq.log`)

HQ ile ilgili tüm işlemler özel `hq` log kanalına yazılır. Log kategorileri:

* `REGISTER`: Kayıt adımları ve başarı durumları.
* `SYNC`: Kalp atışı ve telemetri aktarımları.
* `COMMAND`: Çalıştırılan uzaktan komutlar ve sonuçları.
* `ERROR`: Bağlantı veya yürütme hataları.
* `WARNING`: Retry veya geçersiz kimlik bilgisi uyarıları.
* `INFO`: Lisans durumu güncellemeleri.

---

## ⚙️ 5. Konfigürasyon ve `.env` Ayarları

### `config/hq.php`
```php
return [
    'url' => env('HQ_URL', 'http://127.0.0.1:8000'),
    'site_uuid' => env('HQ_SITE_UUID', null),
    'api_secret' => env('HQ_API_SECRET', null),
    'site_type' => env('HQ_SITE_TYPE', 'dershane'),
    'site_name' => env('HQ_SITE_NAME', env('APP_NAME', 'Dershane ERP')),
    'request_timeout' => (int) env('HQ_REQUEST_TIMEOUT', 10),
    'connect_timeout' => (int) env('HQ_CONNECT_TIMEOUT', 5),
    'retry_count' => (int) env('HQ_RETRY_COUNT', 3),
    'retry_delay' => (int) env('HQ_RETRY_DELAY', 200),
    'sync_interval' => (int) env('HQ_SYNC_INTERVAL', 900),
    'timestamp_tolerance' => (int) env('HQ_TIMESTAMP_TOLERANCE', 300),
];
```

### `.env`
```env
HQ_URL=http://127.0.0.1:8000
HQ_SITE_UUID=550e8400-e29b-41d4-a716-446655440000
HQ_API_SECRET=hq_sk_...
HQ_SITE_TYPE=dershane
HQ_SITE_NAME="Dershane ERP"
```
