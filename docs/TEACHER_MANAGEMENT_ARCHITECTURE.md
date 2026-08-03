# Öğretmen Yönetimi (Teacher Management) Mimarisi

Bu doküman, sistemdeki **Öğretmen (Teacher)** modülünün mimari yapısını, kullanılan tasarım desenlerini ve bileşenler arası ilişkileri detaylandırmaktadır.

## Temel Kurallar ve Prensipler

1. **Controller İnce Kalacak (Fat Model/Service, Skinny Controller):** Controller sınıfları sadece HTTP Request/Response yönetiminden ve Authorization işlemlerinden sorumludur. Tüm iş mantığı (business logic) Servis katmanında (Service Layer) bulunur.
2. **Tenant İzolasyonu (Tenant Isolation):** Tüm sorgular, oluşturma ve listeleme işlemleri kesinlikle `branch_id` (şube) bazında filtrelenmelidir. Bir şube yöneticisi veya personeli başka bir şubedeki öğretmeni asla göremez veya düzenleyemez.
3. **Limit Kontrolleri:** Abonelik planlarında belirlenen öğretmen oluşturma limitleri (örneğin `max_teachers = 20`), öğretmen kayıt işleminden önce kontrol edilmelidir.
4. **Bağımsızlık:** Bu modül içerisinde maaş (salary), ödeme (payment), SMS veya veli (parent portal) sistemleri **yer almaz**. Öğretmen yönetimi saf olarak personelin (öğretmenin) temel bilgileri, branşı ve sisteme giriş yetkileri etrafında şekillenir.

## Mimari Bileşenler

### 1. Veritabanı (Modeller ve Tablolar)

- **`users` Tablosu:** Tüm sisteme giriş yapan kullanıcıların temel bilgileri (ad, soyad, e-posta, şifre, branch_id) burada tutulur.
- **`teachers` Tablosu:** Sadece öğretmenlere özel detaylar (uzmanlık, unvan, eğitim durumu, biyografi, deneyim yılı) burada yer alır.
- **İlişki:** `Teacher` modeli, `User` modeline `user_id` üzerinden `belongsTo` ile bağlıdır.
- **Silme Mantığı:** Öğretmenler silindiğinde (Soft Delete) hem `teachers` hem de `users` tablosundaki kayıt soft-delete olarak işaretlenir (Cascade Soft Delete benzeri bir davranış Service üzerinden manuel tetiklenir).

### 2. Controller Katmanı (TeacherController)

- `app/Http/Controllers/Admin/TeacherController.php`
- Form Request validation kuralları Controller içinde validate ile (veya FormRequest sınıfı ile) değerlendirilir.
- Tenant filtreleri Global Scope (`TenantScoped`) üzerinden otomatik yapılır, ancak `create` metotlarında aktif `branch_id` verilmelidir.
- Authorization işlemleri Laravel Policy'leri (`TeacherPolicy`) üzerinden Controller başında yapılır (`$this->authorize(...)`).

### 3. Service Katmanı (TeacherManagementService)

- `app/Domain/Teacher/Services/TeacherManagementService.php`
- İş mantığını barındırır.
- `createTeacher(array $data, int $branchId, int $createdBy): Teacher` metodunda:
  1. `User` modeli oluşturulur (Şifre default oluşturulur, `branch_id` atanır).
  2. Kullanıcıya 'Teacher' rolü atanır.
  3. `Teacher` tablosuna ilgili ek alanlar kaydedilir.
- `updateTeacher(Teacher $teacher, array $data): Teacher` metodunda:
  1. E-posta değişiyorsa unique kontrolü yapılır.
  2. `User` modeli güncellenir.
  3. `Teacher` modeli güncellenir.
- `deleteTeacher(Teacher $teacher): void` metodunda:
  1. `Teacher` silinir.
  2. İlgili `User` da silinir.

### 4. Limit Yönetimi (SubscriptionLimitService)

- `app/Domain/Tenant/Services/SubscriptionLimitService.php`
- `checkTeacherLimit(int $branchId): bool` fonksiyonu ile şubenin sahip olduğu abonelik (`Plan` tablosu `max_teachers` sütunu) kontrol edilir.
- `create` metodunda false dönerse kayıt engellenir ve HTTP redirect ile hata gösterilir.

### 5. Yetkilendirme (Policy ve Middleware)

- **`TeacherPolicy.php`**: `teachers.view`, `teachers.create`, `teachers.edit`, `teachers.delete` yetkilerini (permissions) kontrol eder.
- Ek olarak `view` izni, kullanıcının kendisi ise yetki kontrolünden bağımsız olarak `true` dönebilir (`$user->id === $teacher->user_id`).
- Tüm Controller route'ları `routes/admin.php` içinde yer alır ve middleware kontrollerinden geçer. `show` metodu sadece ilgili yetkiler veya kullanıcının kendisi olma durumunu dikkate almak için `teachers.view` middleware'i dışında tanımlanıp, Controller içinde policy ile denetlenir.

### 6. Arayüz (Blade Views & Components)

- TailwindCSS ve Blade component tabanlı tasarım.
- Tüm formlar ve tablolar modern (vibrant colors, clean inputs) tasarım sistemiyle oluşturulmuştur.
- Hata ve başarı mesajları `<x-alert>` bileşeni kullanılarak gösterilir.

## Güvenlik ve İzolasyon Testleri

Tenant izolasyonu ve limit kontrolleri `tests/Feature/TeacherManagementTest.php` üzerinden doğrulanmıştır:
- Başka bir şubedeki `Tenant Admin`'in, ilgili öğretmeni göremeyeceği ve düzenleyemeyeceği doğrulanmıştır.
- Öğretmenlerin kendilerini görüntüleyebildiği doğrulanmıştır.
- Limit aşıldığında sistemin kayıt işlemine izin vermediği doğrulanmıştır.
