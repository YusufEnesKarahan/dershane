# Role Based Access Control (RBAC) ve Tenant Authentication Mimarisi

Dershane SaaS ürününde kullanıcı yetkilendirmesi (authorization) ve veri izolasyonu (tenant authentication) belirli roller ve Scope (kapsam) mimarisi üzerinden işlemektedir.

## 1. Roller ve Yetkiler (Roles & Permissions)

Sistemde varsayılan olarak şu roller bulunur:

- **Super Admin**: Tüm sistemi yönetir. Tenant gözetmeksizin herhangi bir veriye erişebilir. İzolasyon kurallarından muaf tutulmuştur.
- **tenant_admin**: İlgili şubenin/kurumun yöneticisidir. Öğrenci, öğretmen, sınıf ve raporlara sadece kendi kurumu (`branch_id`) çerçevesinde erişebilir.
- **teacher**: Kurumda öğretmen rolündedir. Kendisine atanmış veya kurum içindeki genel kısıtlı verileri (sınıf bilgisi, öğrencileri, yoklama) görebilir.
- **staff**: Kurum personeli rolündedir. Öğrenci kayıt, temel CRUD işlemleri yapabilir. Kendi kurumu dışındaki verilere erişemez.

Bu roller veritabanında `RBACSeeder` tarafından varsayılan izinleriyle (students.view, classes.create vb.) oluşturulur. İzin kontrolü `AuthorizationService` üzerinden sağlanır.

## 2. Tenant İzolasyonu (Tenant Isolation)

Her müşterinin verisi (`Student`, `Teacher`, `Classroom`, vb.) `branch_id` ile birbirinden izole edilmiştir.
İzolasyon iki katmanlı olarak uygulanır:

1. **Global Query Scope (`TenantScoped` Trait)**
   Tüm tenant bazlı modeller (Student, vb.) `TenantScoped` özelliğini kullanır. Bu özellik arka planda `BranchScope` çağırarak Eloquent sorgularına otomatik olarak `where('branch_id', TenantContext::getActiveBranchId())` filtresini ekler. Bu sayede Tenant A kullanıcısı `Student::all()` dediğinde sadece Tenant A öğrencilerini görür.

2. **Policy Katmanı (Authorization Policies)**
   Kullanıcının direkt olarak ID üzerinden (örn: `/students/15`) başka bir kurumun verisine erişmeye çalışmasını engellemek için `StudentPolicy`, `TeacherPolicy`, `ClassroomPolicy` gibi sınıflar devreye girer. Bu policyler içerisinde:
   ```php
   if ($user->branch_id !== $model->branch_id && !$user->isAdministrator()) {
       return false;
   }
   ```
   kuralı ile sıkılaştırılmış bir güvenlik duvarı örülür.

## 3. Login Akışı ve Middleware Yönlendirmeleri (Authentication Flow)

Login akışında `LoginAction` kullanıcının aktifliğini kontrol eder:
- Eğer kullanıcı `PASSIVE` veya `SUSPENDED` durumdaysa sisteme alınmaz ve audit log düşülür.
- Login başarılı ise `EnsureActiveBranch` middleware'i aktif olur. Bu middleware giriş yapan kullanıcının `branch_id` değerini alıp `TenantContext` içerisine yerleştirir. Böylece o Request boyunca yapılacak tüm veritabanı sorguları bu tenant kimliği üzerinden izole edilir.

**Dashboard Yönlendirmesi:**
Login sonrası `LoginController` kullanıcının rolüne göre akıllı yönlendirme yapar:
- `Super Admin` -> `/admin/dashboard`
- `tenant_admin` -> `/dashboard` (Tenant Paneli)
- `teacher` -> `/teacher/dashboard` (Öğretmen Paneli)
- `staff` -> `/staff/dashboard` (Personel Paneli)

Bu ayrı dashboard yapıları ile karmaşıklığın önüne geçilmiş ve modüler bir UI oluşturulmuştur.
