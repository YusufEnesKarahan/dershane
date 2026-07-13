# Naming Conventions (İsimlendirme Standartları)

Bu doküman, projede oluşturulan tüm kod ve veritabanı yapılarının isimlendirme kurallarını ve standartlarını tanımlar.

## 1. Veritabanı ve Şema Yapıları

- **Tables (Tablolar):** Çoğul (plural), snake_case ve küçük harflerle isimlendirilir.
  - *Örnek:* `students`, `teachers`, `lesson_schedules`, `contact_messages`.
- **Pivot Tables (İlişki Tabloları):** İlişkili iki tablonun tekil isimleri alfabetik sırayla birleştirilerek snake_case olarak yazılır.
  - *Örnek:* `course_lesson`, `student_parent`.
- **Columns (Sütunlar):** snake_case ve küçük harflerle yazılır.
  - *Örnek:* `first_name`, `email_verified_at`.

---

## 2. PHP Kod Yapıları (Sınıflar & Sorumluluklar)

- **Models (Modeller):** Tekil (singular), PascalCase olarak yazılır.
  - *Örnek:* `Student`, `Teacher`, `LessonSchedule`.
- **Controllers (Kontrolcüler):** PascalCase olarak yazılır ve sonuna `Controller` takısı eklenir. Tek sorumluluklu controller'lar için `__invoke` metodu tercih edilebilir.
  - *Örnek:* `StudentController`, `RegisterStudentController`.
- **Services (Servisler):** PascalCase ve sonuna `Service` takısı eklenir.
  - *Örnek:* `FeatureFlagService`, `ThemeService`.
- **Repositories (Depolar):** PascalCase ve sonuna `Repository` takısı eklenir. Arayüz kontratları `app/Core/Contracts` altında sonuna `Interface` veya `Contract` alarak tanımlanır.
  - *Örnek:* `StudentRepositoryInterface` (arayüz), `EloquentStudentRepository` (somut uygulama).
- **DTO (Veri Transfer Nesneleri):** PascalCase ve sonuna `DTO` takısı eklenir.
  - *Örnek:* `PaginationDTO`, `ResponseDTO`.
- **Enums (Sabitler):** PascalCase ve sonuna `Type` veya `Status` gibi anlamlı bir belirteç eklenir.
  - *Örnek:* `VersionType`, `StatusType`.
- **Actions (Eylemler):** PascalCase, fiil veya eylem belirten isimlendirme yapılır ve sonuna `Action` takısı eklenir.
  - *Örnek:* `CreateAction`, `RegisterStudentAction`, `DeleteHomeworkAction`.
- **ViewModels (Görünüm Modelleri):** PascalCase ve sonuna `ViewModel` takısı eklenir.
  - *Örnek:* `FrontendViewModel`, `AdminStudentListViewModel`.
- **Policies (Yetkilendirme):** PascalCase ve sonuna `Policy` takısı eklenir.
  - *Örnek:* `StudentPolicy`, `HomeworkPolicy`.

---

## 3. Rota ve Görünüm Yapıları (Routes & Views)

- **Route Names (Rota İsimleri):** Küçük harflerle, dot-notation (noktalı gösterim) ve kebab-case olarak yazılır.
  - *Örnek:* `admin.students.index`, `frontend.blog-posts.show`.
- **View Names (Görünüm/Şablon Dosyaları):** Küçük harflerle ve kebab-case olarak yazılır.
  - *Örnek:* `resources/views/admin/students/index.blade.php`, `resources/views/frontend/pages/about-us.blade.php`.
