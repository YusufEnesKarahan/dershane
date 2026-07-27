# Sprint 5.2 - Performance & Database Optimization Report

## 1. N+1 Query & Eager Loading Analizi
- **Controller'larda `all()` Kullanımı:** Birçok Controller'da (örn. `StudentController`, `AssignmentController`, `EmployeeController`, `AssignmentSubmissionController`) form ve dropdown verileri için `Branch::all()`, `Classroom::all()`, `Course::all()`, `Student::all()` çağrılmaktadır. Bu durum, veritabanındaki tüm satırları memory'ye yükleyerek ciddi bir performans ve bellek sızıntısına (memory leak) neden olur. `select('id', 'name')->get()` kullanılmalı veya bu dropdown verileri cache'lenmelidir.
- **Döngü İçi Sorgular (N+1):** `TeacherAttendanceController` içerisinde `foreach` döngüsünde `AttendanceStatus::where('code', ...)->first()` ve `Attendance::updateOrCreate()` çağrılmaktadır. 30 kişilik bir sınıfta bu kod 60 adet veritabanı sorgusu tetikler. Toplu (bulk) upsert işlemleri ve önceden memory'e alınmış status mapping kullanılmalıdır.
- **Eksik Eager Loading:** `AttendanceSessionRepository` paginate metodunda `withCount('attendances')` kullanılmadığı için `admin.attendances.index` Blade dosyasındaki `$session->attendances->count()` çağrısı her satır için N+1 sorgusu tetiklemektedir.

## 2. Repository Analizi
- Repository'lerde genellikle eager loading doğru kullanılmış olsa da (`with(['classroom', 'course'])`), ilişkisel verilerin `count` ihtiyacı olan yerlerde `withCount()` kullanılmadığı tespit edilmiştir.
- `SystemJobController` gibi sayfalarda dashboard istatistikleri için tekrarlı `count()` sorguları doğrudan veritabanına atılmaktadır.

## 3. Pagination Analizi
- Sistemdeki tüm repository'lerde istisnasız `$query->paginate($perPage)` (LengthAwarePaginator) kullanılmıştır. 
- Bu durum, her sayfalama işleminde tüm tablonun `COUNT(*)` sorgusunun çalıştırılmasına neden olur. Özellikle `JobHistory`, `AutomationLog`, `Attendance` ve `Media` gibi çok hızlı büyüyen log/arşiv tablolarında bu durum ciddi bir darboğazdır. Bu tablolar için `simplePaginate()` veya `cursorPaginate()` kullanılmalıdır.

## 4. Database Index Analizi
Mevcut migration'lar incelendiğinde aşağıdaki alanlarda indeks (index) eksikliği tespit edilmiştir:
- Sıkça filtrelenen `status` kolonları (örn. `students.status`, `registrations.status`, `pages.status`).
- Arama yapılan metin alanları için (örn. `first_name`, `last_name`) index eksikliği (B-tree yerine amaca uygun Fulltext/Composite indeksler düşünülmelidir).
- Log ve geçmiş tablolarında tarih bazlı sıralamaları hızlandırmak için `created_at` ve `session_date` indeksleri.

## 5. Cache Analizi
- Laravel'in standart route ve config cache yapıları mevcut olsa da, domain bazlı cache kullanımı zayıftır.
- Sistem genelinde sık kullanılan dropdown listeleri (`branches`, `classrooms`, `roles`) ve Dashboard istatistikleri (aktif öğrenci sayısı, son 30 günlük ciro vb.) cache'lenmemektedir. Her sayfa yenilemesinde aynı `count()` veya `all()` veritabanına yük bindirmektedir.

## 6. Blade Performansı
- Blade dosyalarında (örn. `attendances.index` ve `roles.edit`) View üzerinde filtreleme ve sayma (örn. `$role->permissions->filter(...)->count()`) yapılmaktadır. Bu işlemler collection üzerinde yapıldığı için küçük verilerde sorun yaratmasa da, veri büyüdüğünde render süresini uzatacaktır.
- Sık kullanılan menü ve sidebar bileşenleri için View Composer yapısı eksiktir.

## 7. Queue Analizi
- **Mail İşlemleri:** `NotificationChannelService::send()` içinde `Mail::raw()` fonksiyonu senkron çalışmaktadır. Toplu bildirim veya SMS/Mail gönderimlerinde sayfa yanıt süresi (response time) kullanıcının beklemesine neden olacaktır. `ShouldQueue` kullanılmalıdır.
- **Export/PDF İşlemleri:** `ReportController` ve `ExportReport` action'ında dışa aktarım süreçleri senkron çalıştırılmaktadır. Yüksek verili Excel veya PDF çıktılarında sistem timeout'a (504 Gateway Timeout) düşebilir. Background Job olarak ayarlanmalıdır.

## 8. Memory Analizi
- `Student::all()`, `User::all()` gibi çağrılar pagination veya chunking yapılmadığı için Memory Limit Exhausted hatalarına açık bir yapıdadır.
- İşlem yapılacak büyük veri yığınları için `get()` yerine `cursor()` veya `chunk()` kullanımı zorunlu hale getirilmelidir.
