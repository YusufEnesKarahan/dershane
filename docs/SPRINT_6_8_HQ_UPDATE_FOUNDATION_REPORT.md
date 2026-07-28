# Sprint 6.8: HQ Update Delivery Foundation

## Genel Bakış
Dershane ERP sisteminin HQ üzerinden güncellemeleri kontrol edebilmesi, versiyon takibi yapabilmesi ve güncelleme loglarını güvenli bir şekilde tutabilmesi için **Update Management Foundation** altyapısı geliştirilmiştir. Güvenlik prensipleri gereği sistem **hiçbir şekilde otomatik kod indirme, dosya değiştirme veya shell komutu (git pull, composer, vb.) çalıştırmaz**. Bu sprint sadece metadata takibi, veritabanı kayıtları ve API entegrasyonu katmanını içerir.

## Yapılan Değişiklikler

### 1. Yapılandırma (Configuration)
- `config/hq.php` içerisine `updates` dizisi eklendi.
- Varsayılan olarak kapalıdır (`enabled => false`).
- İzlenecek kanal (`channel => 'stable'`) ve kontrol aralığı (check_interval) eklendi.

### 2. Veritabanı ve Loglama
- `hq_updates`: HQ üzerinden bildirilen her bir versiyonun (versiyon numarası, paket linki, metadata vb.) tutulduğu tablodur.
- `hq_update_logs`: Güncelleme hareketlerinin (registered, installed, failed vb.) audit trail amaçlı tutulduğu log tablosudur.
- Her iki tablo da dış sistemlerle eşleşebilmesi için UUID destekler.

### 3. Servis Katmanı
- **`HQUpdateService`** oluşturuldu.
- `checkAvailable()`: HMAC korumalı HTTP katmanı üzerinden (HQHttpService) merkez sunucuya bağlanıp uygun update paketlerini sorgular.
- Mevcut versiyon bilgisi geçici olarak statik (`config('app.version')`) olarak tutulmaktadır.
- Güncellemeler `registerUpdate()` metoduyla veritabanına eklenir. Asla indirilmez veya çalıştırılmaz.

### 4. Console Commands
- Yeni izole komut: `hq:update-check`
- Yalnızca `config('hq.updates.enabled')` true ise çalışır. Servis üzerinden kontrol edip konsola bilgi mesajı basar. Herhangi bir exec/shell_exec barındırmaz.

### 5. HTTP Entegrasyonu
- `HQHttpService` içerisine `checkUpdates` metodu eklendi. Payload'ında sistemin UUID'si, versiyonu ve takip edilen update kanalı yer alır.

### 6. Yönetim Paneli (Admin UI) & Dashboard
- `/admin/platform/updates` rotasıyla yayın yapan `HQUpdateController` ve `index.blade.php` oluşturuldu. 
- Arayüzde mevcut sürüm, mevcut en güncel sürüm, update yapılandırma bilgileri ve geçmiş update logları tablo halinde yer alır. Manuel sürüm kurma taklidi (mock mark installed) butonu vardır.
- Ana dashboard (Executive Dashboard) sayfasına HQ Update Status kartı eklendi.

### 7. Güvenlik (Security Review)
- Kesinlikle dosya indirme/yazma/okuma kodları eklenmedi.
- Shell komutu (git, composer, npm, vb.) eklenmedi.
- Sadece `hq_updates` veritabanı manipülasyonu yapıldı.

### 8. Testler
- `tests/Feature/HQUpdateTest.php` geliştirildi.
- Config devre dışı olduğunda (default) sistemin engellendiği (blocked) kanıtlandı.
- HTTP Mocking yöntemi ile sahte (fake) HQ endpointlerinden dönen metadata verisinin veritabanına kayıt süreci (register) test edildi.
- Yönetim paneli arayüz testleri ve Super Admin yetki kontrolü doğrulandı.

## Sonuç
Update metadata altyapısı güvenli, izole ve pasif modda çalışır durumda sisteme entegre edilmiştir. Gerçek paket yükleme işlemleri ileriki fazlarda ayrıca kurgulanacaktır.
