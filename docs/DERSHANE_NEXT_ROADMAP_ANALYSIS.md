# Dershane SaaS ERP - Architecture Analysis & Roadmap Continuation

## 1. Executive Summary
This document provides a comprehensive architectural and SaaS-readiness analysis of the Dershane SaaS ERP platform following the successful removal of the legacy HQ Central Management architecture. The project is currently a modular monolith built on Laravel 13.8 (PHP 8.3) and TailwindCSS 4.0. It possesses a solid structural foundation, with robust domain segregation and a clean database schema. However, as a true SaaS product, it currently lacks multi-tenant (branch) security isolation (Global Scopes), onboarding workflows, and comprehensive automated testing. This report outlines the technical debt and proposes a clear, commercially driven roadmap to transform the system into a market-ready product.

---

## 2. Current Architecture Status

### Analysis
- **Framework:** Laravel 13.8, PHP 8.3.
- **Frontend Stack:** TailwindCSS 4.0, Vite, Blade Templates, Vanilla JS.
- **Architectural Pattern:** Modular Monolith. The codebase is organized by business domains (e.g., Core, Institution, Student, Teacher, CRM, Finance) using a multi-layered approach (Controllers -> DTOs -> Actions/Services -> Repositories -> Models).
- **Domain Organization:** High quality. Domains are well separated.
- **Service Layer & Repositories:** Present and actively used (e.g., `ClassroomService`, `StudentService`), ensuring business logic is abstracted from controllers.
- **Action / DTO Structure:** Excellent implementation for handling complex write operations (`CreateUserAction`, `UpdateCourseDTO`), keeping controllers thin.
- **Event / Listener:** Actively used for decoupling processes like audit logging (`CreateAuditLog`).
- **Queue / Cache:** Queues are implemented for heavy tasks. Cache is utilized for configurations and performance indices.
- **Authentication:** Dual usage of Session Auth (for Portal/Admin) and Sanctum (for API).
- **Authorization:** Built-in Laravel Policies and RBAC tables (`roles`, `permissions`, `permission_role`).

### Strengths & Weaknesses
- **Güçlü Taraflar:** Domain Driven Design prensiplerine yakın, temiz Controller katmanı, güçlü DTO/Action izolasyonu. Modüler yapısı ile ekiplerin bağımsız çalışabilmesine olanak tanıyor.
- **Zayıf Taraflar:** Global Scope eksikliği. Multi-tenant (çoklu şube) güvenlik duvarı zayıf. Test kapsamı (özellikle uçtan uca iş akışları için) yetersiz.
- **Enterprise Seviyesine Uzak Noktalar:** Fatura entegrasyonları (E-Fatura), gelişmiş Subscription/Billing motoru eksik.
- **Gereksiz Karmaşıklıklar:** Bazı modüllerde (Örn: CRM & Lead) gereğinden fazla parça (Analytics Controller, Pipeline Controller) bulunuyor; MVP için daha basit tutulabilirdi.

---

## 3. Module Completion Matrix

| Modül | Alt Bileşen | Durum | Açıklama |
| :--- | :--- | :---: | :--- |
| **Core** | Kullanıcı, Rol, Permission, Ayarlar | ✅ Tamamlandı | Güçlü bir RBAC yapısı aktif. Ayarlar DTO ile yönetiliyor. |
| **Institution**| Kurum, Şube, Ayarlar, Kullanıcı İlişkisi | 🟡 Kısmen | Model seviyesinde (`Branch`) tamam. Ancak kurum yöneticisinin şube limitlerini, faturaları veya tenant ayarlarını yöneteceği ana arayüz (Onboarding & Global Settings) eksik. |
| **Student** | Kayıt, Profil, Veli İlişkisi, Transfer | ✅ Tamamlandı | Uçtan uca kayıt (`Registration`), kabul (`Admission`) ve transfer süreçleri tamamlanmış. |
| **Teacher** | Öğretmen, Maaş, Sözleşme, İzin, Performans | ✅ Tamamlandı | Çapraz modüllerle (`TeacherSalaryProfile`, `TeacherSchedule`) tam entegre çalışıyor. |
| **Academic** | Kurs, Sınıf, Ders Programı, Dönem, Tatiller | ✅ Tamamlandı | Modeller arası ilişkiler kusursuz, çatışma önleyici (Exception) yapılar düşünülmüş. |
| **Attendance** | Yoklama, Oturum, Raporlama | ✅ Tamamlandı | Hem öğrenci hem personel (`EmployeeAttendance`) için ayrıntılı tabloları mevcut. |
| **Assignment** | Ödev, Dosya, Eğitim İçeriği | ✅ Tamamlandı | Polimorfik döküman yönetimi (`Documentable`) ve versiyonlama aktif. |
| **Finance** | Fatura, Tahsilat, Taksit, İndirim, İade | ✅ Tamamlandı | `Invoice`, `Payment`, `PaymentPlan` alt yapısı çok güçlü ve detaylı kurgulanmış. |
| **Parent Portal**| Veli Giriş, Öğrenci, Bildirim, Takip | ✅ Tamamlandı | Veli erişim loglarına kadar (`ParentAccessLog`) düşünülmüş, izole edilmiş portal yapısı. |

---

## 4. Database Assessment

Veritabanında **60+ Migration** bulunmaktadır. HQ Central Management silinmiş olmasına rağmen tablo yapısı Dershane ERP için oldukça bütünlüklüdür.

- **Ana Tablolar & Foreign Keys:** Referans bütünlüğü (Referential Integrity) sıkı şekilde korunuyor. Çoğu foreign key `cascade` veya `restrict` mantığına uygun dizayn edilmiş.
- **Şube (Branch) İzolasyonu:** Ana iş tablolarının (`students`, `teachers`, `classrooms`, `leads`) tamamında `branch_id` kolonu bulunmaktadır.
- **Finans Tabloları:** `invoices`, `payments`, `payment_plans` ilişkileri muhasebe standartlarına uygun.
- **Index Yapıları:** Sprint 5.2.3'te eklenen performans indeksleri sayesinde tablolar büyük veriye karşı hazırlıklı (`2026_07_27_110000_add_performance_indexes...`).
- **Audit Yapısı:** Event/Listener mimarisi ile güçlü bir şekilde loglanıyor (Sistem metrikleri tablosu mevcut).
- **Problemli Kararlar:** 
  - *IDOR Riski:* Tablolarda `branch_id` mevcut ancak Laravel tarafında otomatik bir Global Scope (örn: `BranchScope`) uygulanmamış. 
  - *SaaS Ölçeği:* Aynı sunucuda binlerce Dershane (Institution) tutulacaksa `tenant_id` kullanımı gerekebilir. Şu anki yapı `branch_id` üzerinden Multi-Branch yapısına dayanıyor ancak Multi-Tenant (Institution) yalıtımı daha belirgin olmalı.

---

## 5. Security Assessment

Sistemin SaaS ölçeğinde satışa çıkmadan önce acilen çözülmesi gereken güvenlik sorunları:

- **IDOR / Cross-Tenant Data Leak (Kritik):** Modellerde `branch_id` için bir `GlobalScope` yok. Controller veya Service katmanında yanlışlıkla bir `where('branch_id')` filtresi unutulursa, A şubesindeki bir hoca B şubesindeki bir öğrencinin kaydını silebilir veya görebilir.
- **Authorization Açıkları:** Form Request sınıflarında (örn: `StoreUserRequest`) gelen `branch_id` sadece tabloda var mı (`exists:branches,id`) diye kontrol ediliyor. İşlemi yapan kullanıcının o `branch_id` üzerinde yetkisi olup olmadığı doğrulanmıyor.
- **Mass Assignment:** DTO yapısı kullanıldığı için Mass Assignment riski minimize edilmiştir, bu yönden sistem çok güvenlidir.
- **Sensitive Data:** Öğrenci kimlik bilgileri, veli telefonları ve ödeme detayları maskelenmeden veritabanında tutulmaktadır (Finansal veriler için Encryption cast önerilir).

---

## 6. Technical Debt

| Öncelik | Kategori | Sorun | Çözüm Önerisi |
| :--- | :--- | :--- | :--- |
| **P0 (Kritik)** | Security / Architecture | Tenant İzolasyonu Yokluğu | Modeller için `BranchScope` veya `TenantScope` oluşturulup tüm sorgularda otomatik uygulanması sağlanmalı. |
| **P0 (Kritik)** | Security / Auth | Request Validation Eksikliği | Tüm FormRequest sınıflarında `branch_id` için "user has access to this branch" doğrulaması (Custom Rule) eklenmeli. |
| **P1 (Önemli)** | Testing | Feature (Akış) Testi Eksikliği | Öğrenci kaydı, Fatura ödeme ve Yoklama alma gibi core business işlevleri için uçtan uca Feature testleri yazılmalı. |
| **P1 (Önemli)** | Onboarding | SaaS Kurum Kayıt Akışı | Sistem dışından yeni bir dershanenin üye olup kredi kartı ile abonelik başlatacağı self-service Onboarding mekanizması eksik. |
| **P2 (İyileştirme)** | UI/UX | Component Dokümantasyonu | Kapsamlı Admin View Component'lerinin (form, crud, table) kullanım şekilleri storybook veya markdown olarak belgelenmeli. |

---

## 7. Missing Features (For SaaS Readiness)

Gerçek müşterilere satılabilir (Commercial Ready) bir SaaS haline gelmek için şunlar eksiktir:
1. **Self-Service Onboarding:** Yeni kurumların (Dershanelerin) sisteme kayıt olup kendi şubelerini (Branch) oluşturabilecekleri Master Admin portalı.
2. **Subscription & Limits Manager:** Paket limitlerinin (örn: Maks 500 Öğrenci, 2 Şube) donanımsal olarak enforce (engelleme) edilmesi.
3. **E-Fatura Entegrasyonu:** Müşterilerin, velilere kestiği taksitleri direkt resmi e-faturaya (örn: Uyumsoft, Logo) dönüştürebilmesi.
4. **Payment Gateway (SaaS & Müşteri):** 
   - SaaS Sahibine (Bize) dershanelerin abonelik ödemesi yapması (Stripe/Iyzico).
   - Dershanelerin kendi velilerinden online taksit tahsilatı yapması için Sanal POS altyapısı.

---

## 8. Recommended Roadmap

Mevcut duruma göre ticari değer, MVP olgunluğu ve teknik borçları dengeleyen yeni geliştirme haritası:

1. **Sprint 6.0: Tenant Isolation & Security Hardening (Teknik Borç & Güvenlik)**
2. **Sprint 6.1: SaaS Onboarding & Subscription Limits (Satışa Hazırlık)**
3. **Sprint 6.2: Core Workflow Tests & Stabilization (Teknik Borç Ödemesi)**
4. **Sprint 6.3: Online Tahsilat & E-Fatura Foundation (Ticari Değer - Yüksek Pazar Talebi)**
5. **Sprint 6.4: Gelişmiş Veli Bildirim Merkezi & Mobil API (Kullanıcı Deneyimi)**

---

## 9. Next Sprint Detailed Plan

### **Sprint 6.0: Tenant Isolation & Security Hardening**

- **Amaç:** SaaS uygulamasının en kritik bileşeni olan "Veri İzolasyonunu" (Data Isolation) hiçbir hataya yer bırakmayacak şekilde framework (Laravel) seviyesinde garanti altına almak.
- **Neden Gerekli:** Mevcut durumda sorgular manuel `branch_id` filtresine dayanıyor. İnsan hatası (developer unutkanlığı) durumunda farklı şubelerin verileri sızabilir. Satışa çıkmadan önce bu IDOR riski (P0) kapatılmalıdır.

- **Teknik Yapılacaklar:**
  1. **Global Scope:** `BranchScope` adında bir global scope yazılacak ve ilgili tüm Modellere (Student, Teacher, Invoice vb.) otomatik uygulanacak. Kullanıcı sadece kendi şubesinin/şubelerinin verisini görebilecek.
  2. **Validation Rule:** Gelen requestlerde `branch_id` kontrolü için `UserCanAccessBranch` isimli custom validation rule yazılıp tüm form requestlerine entegre edilecek.
  3. **Middleware:** Kullanıcının aktif çalıştığı şubeyi (Active Branch) session'da tutan ve scope'a parametre geçen bir `ActiveBranchMiddleware` yazılacak.

- **Database Değişiklikleri:** Mevcut `branch_id` mimarisi korunacak, ekstra migration gerekmiyor.
- **Backend Değişiklikleri:** Sadece Modeller (`booted` metodları), Middleware ve Form Request katmanlarında değişiklik yapılacak. Controller ve Servislerdeki manuel `where('branch_id', ...)` filtreleri temizlenecek (Refactor).
- **Frontend Değişiklikleri:** Admin portalında, kullanıcının "Çalıştığı Şubeyi Değiştir" (Switch Branch) yapabileceği bir Topbar Dropdown bileşeni eklenecek.
- **Test Gereksinimleri:**
  - Farklı şubedeki öğrenci kimliği (ID) ile yapılan `GET`, `PUT`, `DELETE` isteklerinin HTTP 403 / 404 döndürdüğünü kanıtlayan Cross-Tenant Security Testleri.
- **Tahmini Tamamlanma Sırası:** Hemen Başlamalı (Priority: 1).
