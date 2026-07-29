# Sprint 7.8 — HQ Enterprise Alert & Notification Engine Report

**Tarih:** 2026-07-29  
**Modül:** HQ Central — Alert & Notification Engine  
**Durum:** ✅ Tamamlandı  

---

## 1. Genel Bakış

HQ Central platformuna Enterprise Alert & Notification Engine eklendi. Bu sistem, HQ üzerinde gerçekleşen kritik durumları otomatik algılayan, kural bazlı değerlendiren ve yöneticilere bildiren bir altyapı sunar.

### Kapsam
- Kural tabanlı alert sistemi (Rule-based Alert Engine)
- Event-driven alert tetikleme
- Interface tabanlı bildirim kanalları (Database, Mail)
- Cooldown mekanizması (spam önleme)
- Scheduler entegrasyonu (offline/license/backup kontrolleri)
- API endpoint (ERP → HQ alert raporlama)
- Dashboard widget ve yönetim UI
- RBAC entegrasyonu

---

## 2. Database Tasarımı

### 2.1 hq_alert_rules
Alert kurallarını tanımlayan tablo.

| Alan | Tip | Açıklama |
|------|-----|----------|
| id | bigint | Primary key |
| uuid | uuid | Unique identifier |
| name | string | Kural adı |
| category | string | Kategori (system, license, security, backup) |
| severity | string | info / warning / danger / critical |
| event_type | string | Tetiklenecek event tipi |
| condition | json | Koşul parametreleri |
| is_active | boolean | Kural aktif mi |
| cooldown_minutes | integer | Tekrar tetiklenme önleme süresi |
| created_by | bigint nullable | Kuralı oluşturan kullanıcı |

### 2.2 hq_alerts
Tetiklenen alertleri saklayan tablo.

| Alan | Tip | Açıklama |
|------|-----|----------|
| id | bigint | Primary key |
| uuid | uuid | Unique identifier |
| rule_id | bigint nullable | İlişkili kural |
| tenant_id | bigint nullable | İlişkili tenant |
| system_instance_id | bigint nullable | İlişkili sistem |
| title | string | Alert başlığı |
| message | text | Detay mesajı |
| severity | string | info / warning / danger / critical |
| status | string | open / acknowledged / resolved |
| triggered_at | timestamp | Tetiklenme zamanı |
| resolved_at | timestamp nullable | Çözülme zamanı |
| metadata | json nullable | Ek veri |

### 2.3 hq_notification_logs
Bildirim gönderim kayıtları.

| Alan | Tip | Açıklama |
|------|-----|----------|
| id | bigint | Primary key |
| alert_id | bigint | İlişkili alert |
| channel | string | database / mail / webhook |
| recipient | string nullable | Alıcı |
| status | string | pending / sent / failed |
| sent_at | timestamp nullable | Gönderim zamanı |
| error_message | text nullable | Hata mesajı |

---

## 3. Mimari

### 3.1 Service Layer

- **HQAlertService**: Alert CRUD, acknowledge/resolve, istatistik
- **HQAlertRuleEvaluator**: Kural değerlendirme, cooldown kontrolü, alert tetikleme
- **HQSchedulerService**: Saatlik kontroller (offline, license expiry, stuck backups)

### 3.2 Notification System

Interface tabanlı soyutlama:

```
NotificationChannelInterface
├── DatabaseNotificationChannel
└── MailNotificationChannel
```

Her alert tetiklendiğinde tüm konfigüre edilmiş kanallar üzerinden bildirim gönderilir.

### 3.3 Event-Driven Architecture

Yeni eventler:
- `SystemOfflineDetected` — Sistem çevrimdışı olduğunda
- `SecurityThreatDetected` — Güvenlik tehdidi algılandığında
- `BackupFailedDetected` — Backup başarısız olduğunda

Mevcut eventlere entegre:
- `LicenseChanged`
- `RemoteCommandExecuted`
- `UpdateCompleted`
- `ConfigurationChanged`
- `BackupCompleted`

Tüm eventler `EvaluateAlertRules` listener üzerinden kural motoruna yönlendirilir (Queue uyumlu).

### 3.4 Scheduler Entegrasyonu

`HQSchedulerService::runHourlyChecks()` metodu saatlik olarak:
1. **Offline Sistem Tespiti**: 15 dakikadır heartbeat göndermeyen sistemleri tespit eder
2. **License Kontrolü**: 30 gün içinde dolacak ve dolmuş lisansları kontrol eder
3. **Stuck Backup Tespiti**: 2+ saat pending kalan backup job'ları failed olarak işaretler

---

## 4. API

### POST `/api/hq/alerts/report`

ERP instance'larının HQ'ya alert raporlaması için kullanılır.

**Request:**
```json
{
    "system_id": "sys-uuid",
    "type": "security",
    "message": "Multiple failed login attempts",
    "severity": "danger",
    "metadata": {"ip": "192.168.1.1"}
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Alert reported and evaluated successfully.",
    "timestamp": 1753797600
}
```

HMAC imza doğrulaması `VerifyHQApiSignature` middleware ile korunur.

---

## 5. RBAC & Permissions

### Yeni Gate Tanımları
- `hq.viewAlerts` — Alert listesini ve detayını görüntüleme
- `hq.manageAlerts` — Alert acknowledge/resolve işlemleri

### HQPolicy Güncellemeleri
- `viewAlerts()` — Super Admin veya `hq.viewAlerts` permission'ına sahip kullanıcılar
- `manageAlerts()` — Sadece Super Admin

---

## 6. UI

### 6.1 Alert Listesi (`/admin/platform/hq-central/alerts`)
- Severity ve status bazlı filtreleme
- İstatistik kartları (Open, Critical, Acknowledged, Resolved Today)
- Animasyonlu durum göstergeleri (pulse dot for open alerts)
- Paginated tablo

### 6.2 Alert Detay (`/admin/platform/hq-central/alerts/{id}`)
- Full alert bilgisi ve metadata
- Notification geçmişi
- Acknowledge/Resolve butonları
- Kaynak bilgileri (Tenant, System Instance)

### 6.3 Dashboard Widget
HQ Central ana dashboard'a Enterprise Alerts kartı eklendi.

---

## 7. Dosya Listesi

### Yeni Dosyalar
| Dosya | Açıklama |
|-------|----------|
| `database/migrations/2026_07_29_131631_create_hq_alerts_tables.php` | Migration |
| `app/Models/HQAlertRule.php` | Alert Rule model |
| `app/Models/HQAlert.php` | Alert model |
| `app/Models/HQNotificationLog.php` | Notification Log model |
| `app/Domain/HQ/Services/HQAlertService.php` | Alert service |
| `app/Domain/HQ/Services/HQAlertRuleEvaluator.php` | Rule evaluator |
| `app/Domain/HQ/Contracts/NotificationChannelInterface.php` | Interface |
| `app/Domain/HQ/Notifications/DatabaseNotificationChannel.php` | DB channel |
| `app/Domain/HQ/Notifications/MailNotificationChannel.php` | Mail channel |
| `app/Events/SystemOfflineDetected.php` | Event |
| `app/Events/SecurityThreatDetected.php` | Event |
| `app/Events/BackupFailedDetected.php` | Event |
| `app/Listeners/EvaluateAlertRules.php` | Listener |
| `app/Http/Controllers/Admin/HQAlertController.php` | Web controller |
| `app/Http/Controllers/Api/HQAlertApiController.php` | API controller |
| `resources/views/admin/hq/alerts/index.blade.php` | List view |
| `resources/views/admin/hq/alerts/show.blade.php` | Detail view |
| `tests/Feature/HQAlertTest.php` | Feature test |

### Güncellenen Dosyalar
| Dosya | Değişiklik |
|-------|-----------|
| `app/Providers/AppServiceProvider.php` | Alert event listeners + Gate tanımları |
| `app/Policies/HQPolicy.php` | viewAlerts, manageAlerts |
| `app/Domain/HQ/Services/HQMonitoringService.php` | Alert istatistikleri |
| `app/Domain/HQ/Services/HQSchedulerService.php` | Hourly checks |
| `routes/admin.php` | Alert routes |
| `routes/api.php` | `/api/hq/alerts/report` |
| `routes/console.php` | Scheduler integration |
| `resources/views/admin/hq/index.blade.php` | Dashboard widget |

---

## 8. Güvenlik

- ✅ `exec()`, `eval()`, `shell_exec()` kesinlikle kullanılmadı
- ✅ HMAC imza doğrulaması korundu
- ✅ RBAC permission sistemiyle entegre
- ✅ DB transaction ile veri tutarlılığı sağlandı
- ✅ Queue uyumlu tasarım (ShouldQueue listener)
- ✅ Cooldown mekanizması ile alert spam önleme

---

## 9. Sonraki Adımlar (Opsiyonel)

1. **Webhook Notification Channel** — Slack, Teams, Discord entegrasyonu
2. **Alert Rule Builder UI** — Admin panelden kural oluşturma
3. **Escalation Policy** — Belirli süre acknowledge edilmezse üst kademeye bildirim
4. **Alert Correlation** — İlişkili alertleri gruplama
5. **SMS Notification Channel** — Kritik alertler için SMS
