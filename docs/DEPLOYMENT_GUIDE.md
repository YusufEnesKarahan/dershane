# Dershane ERP - Production Deployment Guide

Bu doküman, Dershane ERP SaaS uygulamasının üretim (VPS / Cloud Ubuntu 22.04 / 24.04 LTS) ortamında sıfırdan canlıya alınması için gereken adım adım adımları içerir.

---

## 1. Sunucu ve Yazılım Gereksinimleri
- **İşletim Sistemi:** Ubuntu 22.04 LTS / Ubuntu 24.04 LTS
- **PHP Sürümü:** PHP 8.2 veya 8.3
- **PHP Eklentileri:** `php-cli`, `php-fpm`, `php-mysql`, `php-mbstring`, `php-xml`, `php-curl`, `php-gd`, `php-zip`, `php-intl`, `php-bcmath`
- **Veritabanı:** MySQL 8.0+ veya MariaDB 10.6+
- **Web Sunucusu:** Nginx
- **Process Manager:** Supervisor
- **Node.js:** Node.js 18+ & NPM

---

## 2. Dizin İzinleri ve Hazırlık
```bash
sudo chown -R www-data:www-data /var/www/dershane
sudo chmod -R 775 /var/www/dershane/storage /var/www/dershane/bootstrap/cache
```

---

## 3. Nginx Sanal Sunucu (VHost) Yapılandırması
`/etc/nginx/sites-available/dershane` dosyası:

```nginx
server {
    listen 80;
    server_name dershane.example.com;
    root /var/www/dershane/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Supervisor Queue Worker Yapılandırması
1. `docs/supervisor-worker.conf` dosyasını `/etc/supervisor/conf.d/dershane-worker.conf` konumuna kopyalayın.
2. Supervisor servisini güncelleyin:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

---

## 5. Cron (Scheduler) Kurulumu
`crontab -e -u www-data` komutunu çalıştırarak aşağıdaki satırı ekleyin:

```cron
* * * * * cd /var/www/dershane && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. SSL Sertifikası (Certbot / Let's Encrypt)
```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d dershane.example.com
```

---

## 7. İlk Canlıya Alım & Deployment Betiği
Kök dizinde bulunan `deploy.sh` dosyasını çalıştırın:

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 8. Veritabanı Yedekleme ve Temizlik
- Manuel yedek almak için: `php artisan backup:database`
- Geçici dosyaları temizlemek için: `php artisan storage:clean-temp`
- Yedekler `storage/app/backups/` dizininde saklanır ve 7 günden eski olanlar otomatik silinir.
