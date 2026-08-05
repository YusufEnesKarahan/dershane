# Dershane SaaS Platformu — Canlıya Alım Kontrol Listesi (Production Deployment Checklist)

> **Doküman Tipi**: Canlı Ortam Kurulum ve Dağıtım Rehberi  
> **Hedef Sistem**: Dershane SaaS Platformu  
> **Sürüm**: v1.0.0 Stable  
> **Gerekli Yetkinlik**: Laravel DevOps Engineer / System Administrator  

---

## 1. Sunucu ve Sistem Gereksinimleri

### Sunucu Altyapısı
- **İşletim Sistemi**: Ubuntu Server 22.04 LTS veya 24.04 LTS önerilir.
- **Web Sunucusu**: Nginx (Tavsiye edilen) veya Apache.
- **PHP Sürümü**: PHP 8.4.x (PHP 8.4+ PHP CLI ve PHP FPM yüklü olmalıdır).
  - *Gerekli PHP Eklentileri*: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `gd`, `zip`, `redis` (opsiyonel).
- **Composer**: Composer v2.6+ kurulu olmalıdır.
- **Node.js & NPM**: Node.js v20+ ve NPM v10+ (Statik varlıkların derlenmesi için gereklidir).
- **Veritabanı Sunucusu**: MySQL 8.0+ veya MariaDB 10.6+.

---

## 2. Kurulum ve Dağıtım Adımları (Deployment Steps)

### Adım 1 — Kodun Sunucuya Çekilmesi ve İzinler
Projeyi `/var/www/html/dershane` dizinine klonlayın ve gerekli yazma izinlerini tanımlayın:
```bash
# Sahiplik atama
sudo chown -R www-data:www-data /var/www/html/dershane

# Dizin izinlerini ayarlama (storage ve bootstrap/cache yazılabilir olmalıdır)
sudo chmod -R 775 /var/www/html/dershane/storage
sudo chmod -R 775 /var/www/html/dershane/bootstrap/cache
```

### Adım 2 — Bağımlılıkların Yüklenmesi (Composer & NPM)
Dev ortamına ait paketleri hariç tutarak PHP bağımlılıklarını kurun ve optimize edin:
```bash
# Composer bağımlılıkları (Optimized Autoloader ile)
composer install --optimize-autoloader --no-dev

# NPM bağımlılıkları ve Asset derlemesi
npm install
npm run build
```

### Adım 3 — Environment (.env) Yapılandırması
`.env.example` dosyasını `.env` olarak kopyalayın ve düzenleyin:
```bash
cp .env.example .env
nano .env
```
*Aşağıdaki kritik production parametrelerini mutlaka güncelleyin:*
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://sizin-dershane-alan-adiniz.com`
- Veritabanı ve SMTP e-posta kimlik bilgilerini eksiksiz tanımlayın.
- `SESSION_DRIVER=database` ve `SESSION_ENCRYPT=true` olarak kilitlendiğinden emin olun.

### Adım 4 — Uygulama Anahtarı ve Veritabanı Kurulumu
```bash
# Uygulama şifreleme anahtarını oluştur
php artisan key:generate

# Veritabanı tablolarını oluştur (Canlı ortamda --force flag'i zorunludur)
php artisan migrate --force

# Platform temel ayarlarını ve planları tohumla
php artisan db:seed --force
```

### Adım 5 — Sembolik Bağlantı ve Önbellek
```bash
# Storage symlink oluşturma
php artisan storage:link

# Önbellek temizliği ve optimize etme (canlı ortam için)
php artisan optimize
```

---

## 3. Servislerin Konfigürasyonu (Background Services)

### 1) Laravel Task Scheduler (Zamanlanmış Görevler)
Lisans süreleri, devamsızlık uyarıları ve HQ senkronizasyonlarının çalışabilmesi için sunucu crontab'ına aşağıdaki satırı ekleyin:
```bash
# Crontab düzenleme modunu aç
crontab -e

# Aşağıdaki satırı ekleyin (www-data kullanıcısı olarak koşulmalıdır)
* * * * * cd /var/www/html/dershane && php artisan schedule:run >> /dev/null 2>&1
```

### 2) Laravel Queue Worker (Arka Plan İşleri)
E-posta bildirimleri ve rapor oluşturma gibi arka plan kuyruklarının işlenmesi için `Supervisor` servisini kurup yapılandırın.
Örnek `/etc/supervisor/conf.d/dershane-worker.conf` dosyası:
```ini
[program:dershane-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/dershane/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/html/dershane/storage/logs/worker.log
stopwaitsecs=3600
```
Supervisor servisini aktifleştirin:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dershane-worker:*
```
