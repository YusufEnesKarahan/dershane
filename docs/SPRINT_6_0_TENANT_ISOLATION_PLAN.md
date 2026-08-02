# Sprint 6.0 — Tenant Isolation & Security Implementation Plan

## 1. Current Security Status
Şu anda Dershane SaaS ERP projesi modüler yapıda ve RBAC ile yetkilendirilmiş olsa da, Multi-Tenant (Çoklu-Kiracı/Şube) izolasyonu Framework (Laravel) seviyesinde koruma altında **değildir**.
Bir şube yetkilisi, ID (Primary Key) numarasını tahmin ettiği farklı bir şubenin öğrenci kaydını (örn: `Student::find(120)`) eğer controller'da manuel `where` filtresi unutulduysa görebilir ve düzenleyebilir. Bu durum SaaS mimarisinde **P0 Seviye Zafiyet (Cross-Tenant Data Leak / IDOR)** oluşturur.

## 2. Vulnerability Analysis
- **Missing Global Scopes:** `branch_id` kolonu olan 15+ ana iş tablosunda hiçbir Laravel Global Scope uygulanmamıştır.
- **Validation Risk:** `StoreUserRequest` gibi dosyalarda gönderilen `branch_id` sadece `exists:branches,id` ile kontrol ediliyor. Hedef şubenin, işlem yapan kullanıcının yetki alanında olup olmadığı denetlenmiyor.
- **Relationship Blindspots:** `Invoice`, `Payment` ve `Attendance` gibi milyarlarca satıra ulaşabilecek tablolar direkt `branch_id` taşımıyor; sadece ilişki (`student_id`) üzerinden izolasyona sahipler ki bu da hem global scope yazmayı karmaşıklaştırır hem de performansı düşürür.

## 3. Proposed Architecture
- **Active Context:** Kullanıcı sisteme girdiğinde bir Session (Web) veya Header (API) üzerinden `active_branch_id` set edilecek.
- **Global Scope (BranchScope):** `TenantScoped` isimli bir trait oluşturulacak. Bu trait eklenen her model (`Student`, `Teacher` vb.) otomatik olarak `BranchScope` tarafından `where('branch_id', context())` filtresine tabi tutulacak.
- **Super Admin Bypass:** Super Admin rolündeki kullanıcılar veya arka plandaki sistem işleri (`withoutGlobalScope`) ile bu sınırı aşabilecek.

## 4. Required Files To Create
1. `app/Core/Scopes/BranchScope.php`
2. `app/Core/Traits/TenantScoped.php`
3. `app/Http/Middleware/EnsureActiveBranch.php`
4. `app/Rules/UserCanAccessBranch.php`
5. `tests/Feature/Tenant/TenantIsolationTest.php`

## 5. Required Files To Modify
- `app/Models/Student.php`, `Teacher.php`, `Classroom.php`, `User.php` vb. (İçerisine `use TenantScoped;` eklenecek).
- `app/Http/Requests/*` (Tüm FormRequest dosyalarındaki `exists:branches,id` kuralı yeni kural ile değişecek).
- `app/Http/Kernel.php` veya `bootstrap/app.php` (Middleware kaydı).

## 6. Migration Requirement
Evet, bir optimizasyon ve güvenlik migration'ı gerekiyor:
- `add_branch_id_to_finance_and_attendance_tables`: `invoices`, `payments`, `attendance_sessions`, `student_attendances`, `assignments` tablolarına `branch_id` kolonu eklenip indexlenecek. Aksi halde Join'li scope'lar milyonlarca satırda DB'yi kilitler.

## 7. Implementation Steps
1. **Migration:** Finans ve Yoklama tablolarına `branch_id` ekleme.
2. **Context Layer:** `EnsureActiveBranch` Middleware'i ile Session/Header kontrolünün başlatılması.
3. **Scope Layer:** `BranchScope` ve `TenantScoped` traitinin yazılması. Modellerin güncellenmesi.
4. **Validation Layer:** `UserCanAccessBranch` kuralının yazılması ve Requestlerin refactor edilmesi.
5. **Controller Cleanup:** Controller'larda önceden yazılmış manuel `where('branch_id', ...)` ve `whereHas` sorgularının temizlenmesi.
6. **Queue Handling:** Job payload'larına `active_branch_id` inject edecek Serializer mimarisinin kurulması.

## 8. Testing Strategy
- **Isolation Tests:** Kullanıcı A (Branch 1), Öğrenci B'yi (Branch 2) görüntülemeyi (GET), silmeyi (DELETE) veya güncellemeyi (PUT) dener -> Beklenen 404 Not Found (Scope devrede olduğu için model bulunamayacaktır).
- **Validation Tests:** Yetkisi olmayan şube ID ile POST isteği gönderildiğinde 422 Validation Error alınması.
- **Queue Tests:** Asenkron bir Job içindeki sorgunun da şube sınırları içinde kaldığının doğrulanması.

## 9. Rollback Strategy
- Eklenen `branch_id` migration'u `down` metodu ile geri alınabilir.
- `TenantScoped` Trait'i modellerden silindiğinde sistem saniyeler içerisinde eski davranışına geri döner (Decoupled Design).

## 10. Risk Analysis
- **Kritik Risk:** Queue (Kuyruk) işleri eğer scope context'ini kaybederse (Session olmadığı için) `active_branch_id` bulamaz ve ya tüm verileri çeker ya da hata fırlatır.
- **Çözüm:** Laravel'in `Job` serialize sürecine müdahale edip, Job dispatch edildiği andaki `active_branch_id`'yi Job constructor'ında taşıyacağız ve worker başladığında context'i manuel set edeceğiz.
