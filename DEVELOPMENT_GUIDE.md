# Development Guide (Geliştirme Kılavuzu)

Bu kılavuz, projenin yerel ortamda kurulması, çalıştırılması ve geliştirme süreçlerinin standartlarını tanımlar.

## Yerel Kurulum Adımları

1. **Gereksinimler:**
   - PHP 8.4 veya üzeri
   - Composer 2.x
   - Node.js 20.x + NPM
   - SQLite3 (Yerel geliştirme veritabanı için)

2. **Projeyi Klonlama ve Bağımlılıkların Yüklenmesi:**
   ```bash
   composer install
   npm install
   ```

3. **Çevre Değişkenlerinin Ayarlanması:**
   `.env.example` dosyasını `.env` olarak kopyalayın ve gerekli alanları güncelleyin:
   ```bash
   copy .env.example .env
   ```

4. **Uygulama Anahtarının Oluşturulması:**
   ```bash
   php artisan key:generate
   ```

5. **Veritabanı Oluşturma ve Göçlerin Çalıştırılması:**
   Yerel geliştirme ortamında SQLite kullanılacaktır:
   ```bash
   # SQLite veritabanı dosyasını oluşturun (oluşturulmamışsa)
   touch database/database.sqlite
   
   # Migrasyonları çalıştırın
   php artisan migrate
   ```

6. **Frontend Geliştirme Sunucusunun Çalıştırılması:**
   ```bash
   npm run dev
   ```

## Geliştirme Standartları ve Komutlar

### Kod Biçimlendirme (Laravel Pint)
Kodunuzu commit etmeden önce biçimlendirmek için Pint komutunu çalıştırın:
```bash
vendor/bin/pint
```

### Statik Analiz (PHPStan)
Kod kalitesini ve tipleri doğrulamak için:
```bash
vendor/bin/phpstan analyse
```

### Yeni Bir Özellik Ekleme Adımları
Projeye yeni bir özellik (feature) eklenirken **Feature-First** mantığıyla hareket edilmelidir:
1. `app/Features/` altında özelliğe ait ilgili yapılar (Controller, Actions, DTO) oluşturulur.
2. İş mantığı `app/Domains/` veya `app/Core/Services/` katmanında çözülür.
3. Model ve Veritabanı işlemleri `app/Repositories/` üzerinden yönetilir.
4. Gerekli yetkilendirmeler `app/Policies/` dosyalarında tanımlanır.
