# Sprint 6.5: HQ Remote Command Foundation

## Genel Bakış
Bu sprint kapsamında, Dershane ERP sisteminin gelecekte HQ Central Management Panel tarafından güvenli ve kontrollü bir biçimde yönetilebilmesi için "Remote Command Foundation" (Uzaktan Komut Altyapısı) kurulmuştur. Tüm komutlar, güvenlik gereği manuel bir onay döngüsünden geçerek Super Admin tarafından çalıştırılacak şekilde yapılandırılmıştır.

## Yapılan Değişiklikler

### 1. Database & Model
- `hq_commands` tablosu eklendi (`command_uuid`, `command_type`, `payload`, `status`, `result`, `requested_at`, `executed_at`).
- `HQCommand` modeli oluşturuldu ve yardımcı metodlarla (isPending, vb.) güçlendirildi.

### 2. Service Katmanı
- **`HQCommandService`**: Komutların oluşturulması, onaylanması, reddedilmesi, yürütülmesi (executor'a gönderilmesi) ve metrik toplanması görevlerini üstlenmektedir.
- **`HQCommandExecutor`**: Yalnızca önceden tanımlanmış beyaz listeli (whitelisted) komutları çalıştırır. (Hiçbir dinamik PHP kodu veya terminal `exec` çalıştırılmaz). 
  - Desteklenen komutlar: `health_check`, `system_info`, `cache_clear`, `version_check`.
- **`HQHttpService`**: Mevcut yapıya `sendCommandResult()` metodu eklenerek, manuel tetiklenen komut sonucunun HQ'ya raporlanması sağlandı.

### 3. Güvenlik Katmanı (Middleware)
- **`HQCommandMiddleware`**: HQ üzerinden gelebilecek payload'ı karşılamak üzere oluşturuldu. Hem Bearer token hem de `SignatureService` kullanılarak HMAC SHA256 imza doğrulamasını yürütür. Hatalı durumlarda 401 veya 403 yanıtı döner.

### 4. Yönetim Paneli (Admin UI) & Dashboard
- `HQCommandController` oluşturuldu. `/admin/platform/commands` rotasında Super Admin'ler için komutları yönetme arayüzü inşa edildi (Approve, Reject, Execute, Result).
- Executive Dashboard üzerindeki metrikler arasına "HQ Command Status" eklendi (Bekleyenler, Başarısızlar, Son Çalıştırma Zamanı).

### 5. Testler
- `tests/Feature/HQCommandTest.php` geliştirildi.
  - Komut kayıt, onay, reddetme süreçleri.
  - Sadece tanımlı (whitelisted) komutların çalıştırılması (cache clear ve health check testleri).
  - Güvenlik duvarının (HQCommandMiddleware) hatalı imza veya eksik token testleri.
- Tüm senaryolar `Http::fake()` ile mock edilerek dış iletişim engellendi.

## Sonuç
Dershane ERP, güvenliği ihlal etmeden (Remote Code Execution veya kontrolsüz iş kuyrukları barındırmadan) güvenli, imzalı ve manuel yönetim garantisi sunan bir Remote Command yapısına kavuşmuştur. Sistem şu an Production (Canlı) ortamına entegre edilmeye tüm testlerinden geçerek hazırdır.
