# Sprint 10.9.1 — DOM / Browser UAT (Kullanıcı Kabul Testleri) Raporu

> **Doküman Tipi**: Kullanıcı Kabul Testi (UAT) ve Tarayıcı Denetim Raporu  
> **Hedef Sistem**: Dershane SaaS Platformu  
> **Roller**: Senior QA Engineer, Senior Manual Tester, SaaS Product Tester, Laravel Production Test Specialist  
> **Tamamlanma Tarihi**: 2026-08-05  
> **UAT Durumu**: **Başarılı (RC2 Hazır)**

---

## 1. Executive Summary

**Sprint 10.9.1** kapsamında platformun gerçek kullanıcı gözüyle tarayıcı üzerindeki davranışlarını simüle etmek ve doğrulamak amacıyla kapsamlı **DOM / Browser UAT (Kullanıcı Kabul Testleri)** yürütülmüştür. 
Yapılan manuel ve otomasyon simülasyonları sırasında tespit edilen kritik veri çakışmaları ve kurulum sihirbazı yönlendirme problemleri anında düzeltilmiş, regresyon testleri yeniden çalıştırılmış ve platformun tüm akışları kesintisiz çalışır duruma getirilmiştir.

---

## 2. Test Ortamı (Test Environment)

- **Sunucu**: Local Development Server (`php artisan serve` @ http://127.0.0.1:8000)
- **Veritabanı**: MySQL (Port 3306)
- **PHP Sürümü**: PHP 8.4.22
- **Tarayıcı Ortamı**: Chromium (Browser Subagent Simülasyonu)
- **Çevre Kısıtları**: Yerel sistem üzerinde Node.js/NPM kurulu olmadığından asset derlemesi varsayılan statik varlıklar ve Tailwind CDN/Vite hazır assetleri ile gerçekleştirilmiştir.

---

## 3. Test Edilen Roller (Tested Roles)

1. **Ziyaretçi / Yeni Müşteri (Guest)**: Kurulum sihirbazını başlatan, şube ve plan seçen rol.
2. **Super Admin**: Tüm sistemin lisanslama, tenant, aktivite loglarını izleyen platform yöneticisi.
3. **Branch Admin**: Şubesindeki öğrenci, öğretmen, ders, sınıf, ödev süreçlerini yöneten şube yöneticisi.
4. **Teacher (Öğretmen)**: Ders programı, kendi sınıfları ve ödev değerlendirme süreçlerine bakan portal kullanıcısı.
5. **Student (Öğrenci)**: Ödev görüntüleme ve teslim portal kullanıcısı.
6. **Parent (Veli)**: Çocuk takip ve devam bilgisi portali kullanıcısı.

---

## 4. Test Senaryoları (Test Scenarios)

1. **Yeni Dershane Kayıt Akışı**: `/onboarding` üzerinden 5 adımlı sihirbazın tamamlanması, demo verilerin yüklenmesi seçeneğinin UAT testinin yapılması.
2. **Dashboard Veri Doğruluğu**: Yeni şube yöneticisi olarak sisteme girildiğinde KPI istatistiklerinin (öğrenci, öğretmen sayısı) doğru gelmesi.
3. **Kısıtlı Rotalara Erişim**: Öğretmen ve veli rollerinin şube dışı veya admin sayfalarına gitmeye çalıştığında `403 Forbidden` almasının UAT simülasyonu.

---

## 5. Bulunan ve Düzeltilen Buglar (Fixed UAT Bugs)

### BUG-UAT-001: Kurulum Sihirbazı Branch Slug Çakışması (Integrity Constraint Violation)
- **Açıklama**: Onboarding sırasında daha önce veritabanında var olan bir şube ismi girildiğinde (örn: `'Kadikoy Merkez'`), `branches.branches_slug_unique` benzersizlik indeksi nedeniyle SQL 1062 hatası ile sihirbaz yarıda kesiliyordu.
- **Düzeltme**: `OnboardingWizardController` şube kayıt adımına benzersizlik doğrulaması (`unique:branches,name`) ve kullanıcı dostu Türkçe hata mesajı (`'Bu şube adı sistemde zaten kayıtlı.'`) eklendi.

### BUG-UAT-002: Plan Seeder ve Boş Veri Çökmesi (TypeError)
- **Açıklama**: Veritabanında hiçbir plan bulunmadığında onboarding wizard tamamlanma adımında `TypeError: createInitialLicense() must be of type Plan, null given` hatası veriyordu.
- **Düzeltme**: `OnboardingService::assignDefaultPlan` metoduna veritabanında plan bulunamadığında dinamik olarak varsayılan bir plan (`Standart Plan`) oluşturup süreci kesintisiz tamamlatan savunmacı fallback kodu eklendi.

### BUG-UAT-003: Kurulum Sonrası Dashboard Yerine Legacy Sihirbaza Yönlendirme
- **Açıklama**: Yeni onboarding sihirbazından başarıyla kaydolup demo veri yüklemeyen kurum yöneticisi, `CheckOnboardingStatus` middleware'inin veritabanında `SystemIdentity` ve aktif `AcademicTerm` araması ve bulamaması nedeniyle eski kurulum sayfasına yönlendiriliyordu. Eski sayfa ise `academic_terms` tablosunda olmayan `branch_id` kolonunu sorguladığı için 500 hatası fırlatıyordu.
- **Düzeltme**: 
  - `OnboardingWizardController` bitiş adımına, sisteme ilk kez kaydolan kiracılar için otomatik olarak varsayılan `SystemIdentity` ve aktif `AcademicTerm` oluşturma kodları eklendi.
  - Eski `OnboardingController` içerisindeki hatalı `branch_id` veritabanı sorgusu temizlenerek standart `is_active` kontrolüne çekildi.
  - Kiracının onboarding durumu tamamlandığı an eski yönlendirme tabloları (`OnboardingStep`, `OnboardingChecklist`) otomatik olarak `completed` durumuna getirilerek middleware geçişi sağlandı.

### BUG-UAT-004: Demo Seeder Sınıf ve Ders Kodu Çakışması (Duplicate Entry)
- **Açıklama**: Onboarding sırasında "Demo Veri Yükle" seçildiğinde, `DemoDataSeederService` tarafından oluşturulan `10-a` sınıf kodu ve `matematik` ders kodu/slugu global benzersizlik kısıtları nedeniyle ikinci kiracı kurulumunda çakışıyor ve 500 hatası veriyordu.
- **Düzeltme**: `DemoDataSeederService` içerisinde oluşturulan ders, sınıf kodları ve öğretmen e-posta adresleri, sonlarına şube ID'si eklenerek (örn: `10-a-6`, `matematik-6`, `ahmet.yilmaz.6@demo.com`) kiracı bazında benzersiz hale getirildi.

---

## 6. Regression Test Sonuçları (Regression Test Results)

UAT düzeltmeleri sonrası platformdaki tüm otomasyon testleri (yeni eklenen test dahil) başarıyla koşulmuştur:

```text
Tests:    221 passed (613 assertions)
Duration: 119.24s
Result:   PASSED (100%)
```

---

## 7. Responsive, UI/UX ve Güvenlik Bulguları

- **Responsive Davranış**: Mobil ekranlarda (320px) ve tabletlerde (768px) menülerin ve tabloların taşma yapmadığı, responsive grid yapısının düzgün yerleştiği görülmüştür.
- **Validation Hataları**: Formlardaki boş veya yanlış veri girişlerinde artık 500 çökme hatası yerine form üzerinde temiz ve kırmızı renkli Türkçe hata mesajları dönmektedir.
- **Branch Isolation**: Şube yöneticileri ve portal kullanıcılarının sadece kendi şubelerine ait verilere eriştiği, diğer şubelerin kod ve slug bazlı benzersiz kayıtlarına müdahale edemediği doğrulanmıştır.
