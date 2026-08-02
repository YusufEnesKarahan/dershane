# Sprint 7.1 — SaaS Monetization & License Management Raporu

## 📌 Hedef ve Kapsam
Mevcut basit (active/inactive) lisans sisteminin ticari bir SaaS (Software as a Service) platformu standartlarına taşınması sağlanmıştır. Yeni bir framework veya paket eklenmeden, var olan `Tenant`, `Branch`, `License` altyapısı genişletilerek plan bazlı limit kontrolü ve dinamik 14 günlük deneme (Trial) sürümü süreçleri entegre edilmiştir.

## 🛠 Yapılan Değişiklikler

### 1. SaaS Plan Sistemi
- **Tablo ve Model:** `plans` tablosu eklendi. `App\Models\Plan` modeli oluşturuldu ve `limits` kolonu JSON (array) olarak cast edildi.
- **Seeder:** `PlanSeeder` sınıfı eklenerek sisteme standart olarak 3 temel plan eklendi:
  - **Starter**: 500 Öğrenci, 5 Kullanıcı, 1 Şube
  - **Professional**: 2000 Öğrenci, 20 Kullanıcı, 3 Şube
  - **Enterprise**: 10000 Öğrenci, 100 Kullanıcı, 10 Şube

### 2. License Geliştirmesi
- `licenses` tablosuna `plan_id` (foreign key), `starts_at` ve `trial_ends_at` alanları eklendi. Geriye dönük uyumluluğun bozulmaması adına `plan_id` alanı geçici olarak `nullable` yapıldı.
- `License` modelinde `isActive()` ve `isExpired()` fonksiyonları güncellenerek `trial`, `suspended` ve `cancelled` durumları eklendi.

### 3. Feature Limit Kontrol Altyapısı (`LicenseLimitService`)
- Sınıfın içerisinde `getLimit` metodu geliştirilerek, aktif lisansın `Plan`'ına (limits json) öncelik verilmesi, plan yoksa eski sisteme uygun `metadata` içinden okuması sağlandı.
- Merkezi limit kontrolü (Controller haricinde) için şu metotlar eklendi:
  - `canCreateStudent()`: Öğrenci sayısını global (Scope olmadan) sayarak kontrol eder.
  - `canCreateUser()`: Sistemdeki toplam kullanıcı sayısını kontrol eder.
  - `canCreateBranch()`: Şube sayısını sınırlandırır.

### 4. 14 Günlük Trial Sistemi (Onboarding)
- `InstallService` içerisindeki ilk kurulum akışı (onboarding) güncellendi. Artık yeni kurulumlar otomatik olarak 1 yıllık Enterprise yerine **Starter** planıyla **14 günlük Trial** lisansına atanıyor.

### 5. Admin Panel Güncellemesi
- `admin/platform/licenses/index.blade.php` ekranında UI karmaşıklaştırılmadan mevcut `x-admin` bileşenleri kullanıldı.
- Kullanıcıya aktif plan adı, başlangıç ve bitiş tarihi, **kalan gün sayısı (Trial veya Normal)** ve planın getirdiği modül limitleri (Öğrenci, Kullanıcı, Şube) görselleştirildi.

## 🗄 Migration Listesi
- `database/migrations/2026_08_02_151810_create_plans_table.php`
- `database/migrations/2026_08_02_151816_add_saas_fields_to_licenses_table.php`

## ✅ Test Sonuçları
SaaS lisans yönetimini kapsayan `SaaSLicenseTest.php` eklendi (Plan oluşturma, Trial geçerliliği, Öğrenci limit kontrolü test edildi). 
Etkilenen eski test (SaaSInstallerTest) `trial` sistemine göre uyarlandı.

```text
php artisan test
42 tests, 112 assertions, 0 failures, 0 errors.
Duration: ~5.9s (Passed)
```

## 🏗 Mevcut Mimariye Etkisi
Domain Driven yapı korundu ve eski `LicenseController`'a herhangi bir iş (business) kodu eklenmedi, tüm akış Service (`LicenseLimitService`, `InstallService`) katmanlarında halledildi. Eski veritabanı kayıtlarının bozulmaması için geri uyumlu `fallback` kontrolleri entegre edilmiştir. Mevcut hiçbir modül (CRM, HR) kırılmamıştır.
