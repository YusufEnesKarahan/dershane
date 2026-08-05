# 🏫 Dershane SaaS Platformu (v1.0.0 Stable Release)

Dershane SaaS Platformu, eğitim kurumlarının (dershaneler, kurslar, etüt merkezleri) tüm şube, öğrenci, öğretmen, veli, ders, ödev ve sınav süreçlerini uçtan uca yönetebilecekleri, bulut tabanlı ve çok kiracılı (multi-tenant) modern bir Dershane ERP / SaaS otomasyon sistemidir.

---

## 🚀 Temel Özellikler (Key Features)

- **Çok Kiracılı SaaS Mimarisi (Multi-Tenant Architecture)**: Her dershane kendi veri alanında izole edilmiştir. Tek kod tabanı üzerinden yüzlerce farklı dershane (tenant) ve şube (branch) güvenli şekilde barındırılır.
- **Şube ve İzolasyon Yönetimi (Branch Isolation)**: Kiracılar kendi şubelerini oluşturabilir; şube yöneticileri, öğretmenler ve öğrenciler sadece yetkili oldukları şube verilerine erişebilir.
- **Öğrenci Yaşam Döngüsü**: Kayıt, sınıfa atama, veli ilişkilendirme, şube transferi ve devam takip süreçleri.
- **Öğretmen Portali**: Ders programı takibi, sınıf yönetimi, ödev tanımlama ve sınav sonucu değerlendirme süreçleri.
- **Veli ve Öğrenci Portali**: Öğrencilerin ödev teslimi ve sınav sonuç karnelerini (doğru, yanlış, net ve puan analiziyle) izleyebildiği, velilerin ise çocuklarının devam durumlarını takip edebildiği özel portaller.
- **Ödev ve Sınav Yönetimi**: Konu bazlı ödev dağıtımı, öğrenci teslimleri, sınav yönetimi ve detaylı sınav analiz kartları.
- **Akademik Takvim & Program**: Ders saatleri, sınıfların haftalık programı, resmi tatil kısıtları ve çakışma kontrolleri.
- **Manuel Lisanslama ve Plan Kısıtları**: Paket bazlı limitasyonlar (maksimum öğrenci, öğretmen, sınıf limitleri) ve lisans bitiş süresi kontrolleri (Çevrimiçi ödeme entegrasyonu içermeyen %100 güvenli manuel lisanslama).
- **Rol ve Yetkilendirme (RBAC)**: Super Admin, Branch Admin, Teacher, Student ve Parent rolleri için Spatie entegrasyonuyla yönetilen ince taneli yetki matrisi.
- **Gelişmiş Dashboard Analitiği**: Şube yöneticileri ve platform yöneticileri için anlık finans, ders, ödev ve başarı grafikleri.

---

## 💻 Teknolojiler (Technology Stack)

- **Backend**: Laravel 13 & PHP 8.4
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Frontend**: Blade Templates, Tailwind CSS
- **Authentication**: Strict HTTPOnly Secure Session Cookies, CSRF, Rate Limiting
- **Testing**: PHPUnit (221+ Automated Tests, 100% Pass)

---

## ⚙️ Kurulum ve Çalıştırma (Installation & Running)

### 1) Gereksinimler (Requirements)
- PHP 8.4+
- Composer v2+
- Node.js v20+ & NPM v10+
- MySQL 8.0+

### 2) Kurulum Adımları (Installation)
Projeyi klonlayıp dizine geçin ve bağımlılıkları kurun:
```bash
composer install
npm install && npm run build
```

### 3) Environment Yapılandırması (.env)
`.env.example` dosyasını kopyalayarak `.env` oluşturun ve veritabanı ile mail sunucu bağlantılarınızı girin:
```bash
cp .env.example .env
php artisan key:generate
```

### 4) Migration ve Seeder İşlemleri
Veritabanı şemasını oluşturmak ve temel rolleri, platform ayarlarını yüklemek için aşağıdaki komutu çalıştırın:
```bash
php artisan migrate --seed
```

### 5) Deployment ve Önbellek (Production Optimization)
Canlı ortamda en yüksek performans için konfigürasyon, rota ve görünümleri önbelleğe alın:
```bash
php artisan optimize
```

---

## 🕒 Arka Plan İşleri ve Planlanmış Görevler (Scheduler & Queue)

### 1) Task Scheduler (Cron)
Zamanlanmış görevlerin (lisans süresi, telemetri, otomatik e-posta bildirimleri) çalışması için sunucuya aşağıdaki Cron girişini yapın:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2) Queue Worker
Arka plan e-posta ve raporlama kuyruklarının işlenmesi için queue worker'ı başlatın (Production'da Supervisor önerilir):
```bash
php artisan queue:work
```

---

## 📄 Lisans (License)
Bu yazılım Dershane SaaS Platformu telif hakları ile korunmaktadır. Detaylar için [LICENSE.md](file:///c:/Users/Yusuf%20Enes%20Karahan/Desktop/Scripts/dershane/LICENSE.md) dosyasına göz atabilirsiniz.
