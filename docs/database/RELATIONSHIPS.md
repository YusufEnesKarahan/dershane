# Relationships & Database Constraints (İlişkiler ve Kısıtlar)

Bu doküman, sistemdeki varlıklar (entities) arasındaki ilişkileri ve veritabanı seviyesindeki kısıtları (constraints) tanımlar.

## 1. Bire Bir (One-to-One) İlişkiler

### User <---> Student
- **Açıklama:** Her öğrenci sisteme giriş yapabilmek için bir kullanıcıya (user) sahip olmalıdır.
- **İlişki Tipi:** `hasOne` / `belongsTo`
- **Kısıtlar:** `student.user_id` alanı benzersizdir (unique). `ON DELETE CASCADE` uygulanır; kullanıcı silindiğinde öğrencilik profili de silinir.

### User <---> Teacher
- **Açıklama:** Her öğretmen sisteme giriş yapabilmek için bir kullanıcıya (user) sahip olmalıdır.
- **İlişki Tipi:** `hasOne` / `belongsTo`
- **Kısıtlar:** `teacher.user_id` alanı benzersizdir (unique). `ON DELETE CASCADE` uygulanır.

---

## 2. Bire Çok (One-to-Many) İlişkiler

### Branch <---> Student / Teacher / Classroom / Course / Registration
- **Açıklama:** Şubeler sistemdeki verilerin yalıtımı (isolation) için en önemli kırılımdır. Bir öğrenci, öğretmen, sınıf, kurs veya kayıt tek bir şubeye bağlıdır.
- **İlişki Tipi:** `hasMany` / `belongsTo`
- **Kısıtlar:** `ON DELETE RESTRICT` uygulanır. İçerisinde aktif kayıtlar veya sınıflar bulunan bir şube veritabanı seviyesinde silinemez.

### Classroom <---> LessonSchedule
- **Açıklama:** Bir derslikte birden fazla ders planlaması yapılabilir.
- **İlişki Tipi:** `hasMany` / `belongsTo`
- **Kısıtlar:** `ON DELETE CASCADE` uygulanır. Derslik silindiğinde o dersliğe ait ders saat planlamaları da silinir.

### Course <---> LessonSchedule / Registration
- **Açıklama:** Bir kurs (örn: TYT Sayısal) birden çok ders planlamasına ve öğrenci kaydına sahiptir.
- **İlişki Tipi:** `hasMany` / `belongsTo`
- **Kısıtlar:** `ON DELETE RESTRICT` uygulanır.

### LessonSchedule <---> Attendance
- **Açıklama:** Bir ders saatine ait yoklama kayıtları.
- **İlişki Tipi:** `hasMany` / `belongsTo`
- **Kısıtlar:** `ON DELETE CASCADE` uygulanır. Ders planı silindiğinde yoklamaları da silinir.

### Homework <---> HomeworkSubmission
- **Açıklama:** Bir ödeve birden fazla öğrenci teslimi (submission) yapılabilir.
- **İlişki Tipi:** `hasMany` / `belongsTo`
- **Kısıtlar:** `ON DELETE CASCADE` uygulanır.

---

## 3. Çoktan Çoğa (Many-to-Many) İlişkiler

### User <======> Role
- **Pivot Tablo:** `user_has_roles` (`user_id`, `role_id`)
- **Kısıtlar:** `ON DELETE CASCADE` her iki yabancı anahtar için de geçerlidir.

### Role <======> Permission
- **Pivot Tablo:** `role_has_permissions` (`role_id`, `permission_id`)
- **Kısıtlar:** `ON DELETE CASCADE` her iki yabancı anahtar için de geçerlidir.

### Student <======> Parent
- **Açıklama:** Bir velinin birden fazla öğrencisi olabilir veya bir öğrencinin birden fazla velisi tanımlanabilir (anne ve baba).
- **Pivot Tablo:** `student_parent` (`student_id`, `parent_id`, `relationship_type` [mother, father, guardian])
- **Kısıtlar:** `ON DELETE CASCADE` her iki yabancı anahtar için de geçerlidir.

### Course <======> Lesson
- **Açıklama:** Bir kurs paketinin içerisinde birden fazla ders yer alabilir (örn: TYT paketinde Matematik, Türkçe, Fizik vardır). Aynı ders birden fazla kursta yer alabilir.
- **Pivot Tablo:** `course_lesson` (`course_id`, `lesson_id`)
- **Kısıtlar:** `ON DELETE CASCADE` her iki yabancı anahtar için de geçerlidir.

---

## 4. Polimorfik (Polymorphic) İlişkiler

### Media (Görsel ve Dosyalar)
- **Açıklama:** Slider, Blog, Öğrenci belgeleri gibi birçok model görsel veya dosyalara ihtiyaç duyar.
- **İlişki Tipi:** Morph Many (`mediable_id`, `mediable_type`)
- **Kısıtlar:** Veritabanı bütünlüğü için ilişkili kayıtlar silindiğinde morph kayıtları da temizlenmelidir (kod veya veritabanı tetikleyicisi seviyesinde).
