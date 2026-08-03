# Tenant Admin Dashboard Mimarisi

Dershane SaaS uygulamasında, Tenant (Şube/Kurum) yöneticilerinin giriş yaptıklarında karşılaştıkları ekran (`/dashboard`), kurumun genel durumunu özetleyen en önemli arayüzdür. Bu arayüzün tasarımı ve veri yönetimi, sistemin performansını ve izolasyonunu koruyacak şekilde özel bir mimariyle oluşturulmuştur.

## 1. Controller ve Service Katmanı Ayrımı

Mimarinin temel prensibi "Thin Controller, Fat Service" (İnce Kontrolcü, Kalın Servis) mantığıdır.
- **`TenantDashboardController`**: Sadece request'i karşılar, aktif tenant bilgisini `TenantContext` üzerinden alır ve servisten gelen veriyi view'a aktarır. Herhangi bir veri hesaplaması veya iş kuralı (business logic) içermez.
- **`TenantDashboardService`**: Tüm iş mantığının ve veri çekme operasyonlarının bulunduğu yerdir. `getStatistics`, `getStudentGrowth`, `getAttendanceSummary` gibi özelleşmiş metodlar ile veritabanından ham verileri alıp, view'ın kullanabileceği formatta düzenler.

## 2. Veri Ön Belleğe Alma (Caching)

Dashboard'lar çok fazla veri toplayıp hesaplama (COUNT, SUM, AVG) yaptığı için, her sayfa yenilemesinde bu sorguların tekrar çalışması performansı düşürecektir.
- `TenantDashboardService` içindeki `getDashboardData()` metodu, `$branchId` bazlı bir cache key oluşturur (`dashboard_metrics_{branchId}`).
- Veriler **5 dakika** boyunca (Time-To-Live: TTL) ön bellekte tutulur.
- Yeni bir öğrenci kaydı eklendiğinde veya abonelik (subscription) değiştiğinde `clearCache()` metodu tetiklenerek bu önbellek temizlenebilir.

## 3. UI/UX ve Bileşenler

`resources/views/dashboard/index.blade.php` dosyası, mevcut tasarım sistemine uygun olarak oluşturulmuştur:
- **KPI Kartları**: Aktif öğrenci, öğretmen, sınıf ve bugünkü yoklama durumunu gösterir.
- **Grafikler**: Chart.js kullanılarak son 6 aylık kayıt büyümesi (Growth) ve son 30 günlük yoklama istatistikleri görselleştirilmiştir. Bu grafikler responsive grid yapısı içinde konumlandırılmıştır.
- **Hızlı İşlemler**: Öğrenci Ekle, Öğretmen Ekle, Sınıf Oluştur gibi butonlar yetki kontrolü (`@can`) ile birlikte sunulur. Kullanıcının yetkisi yoksa ilgili buton gösterilmez.
- **Aktivite Akışı (Recent Activities)**: `PlatformAuditLog` üzerinden kurumda son yapılan işlemler formatlanarak (ikon, renk, çeviri) kullanıcıya sunulur.
- **Subscription Widget**: Limit bilgileri ve plan detayları, mevcut widget altyapısı kullanılarak sunulur.

## 4. Tenant İzolasyonu ve Güvenlik

- Tüm `Student`, `Teacher`, `Classroom`, `AttendanceSession` modellerinde `TenantScoped` global scope'u aktiftir. DashboardService bu modelleri sorguladığında, arka planda otomatik olarak `WHERE branch_id = ?` şartı eklenir.
- Farklı bir tenant yöneticisi bu sayfaya girdiğinde kesinlikle sadece kendi kurumuna ait istatistikleri görür. Bu durum `TenantDashboardTest` feature testleri ile %100 kapsama alınarak kanıtlanmıştır.
