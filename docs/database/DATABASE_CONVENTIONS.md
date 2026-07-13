# Database Conventions (Veritabanı Standartları)

Bu doküman, veritabanı şeması tasarlanırken uyulması gereken veri tipleri, isimlendirme kuralları ve indeksleme standartlarını tanımlar.

## 1. Anahtarlar (Keys)

### Primary Key
- Her tabloda bir birincil anahtar (Primary Key) bulunacaktır.
- Birincil anahtar tipi olarak **Auto-Increment Big Integer** (Laravel default `id()`) kullanılacaktır.
- Alan ismi her zaman `id` olacaktır.

### Foreign Key
- Yabancı anahtarlar (Foreign Keys) bağlanacakları tablonun tekil adı ve `_id` eki birleştirilerek yazılacaktır.
  - *Örnek:* `user_id`, `branch_id`, `course_id`.
- Veritabanı seviyesinde her zaman foreign key kısıtı (constraints) eklenecektir.
- Yabancı anahtar tipi birincil anahtarla uyumlu olarak **Unsigned Big Integer** olacaktır.

---

## 2. Zaman Damgaları ve Silme (Timestamps & Soft Deletes)

### Standart Zaman Damgaları
- Her tabloda varsayılan olarak `created_at` ve `updated_at` zaman damgaları yer alacaktır.
- Tarih formatları UTC zaman diliminde saklanacaktır.

### Yumuşak Silme (Soft Deletes)
- Silindiğinde veri kaybı yaşanmaması gereken tüm ana tablolarda (Student, Teacher, Parent, Course vb.) Soft Delete kullanılacaktır.
- Silinme zaman damgası ismi `deleted_at` (nullable datetime) olacaktır.

---

## 3. Denetim Alanları (Audit Fields / Created By)
Kritik tablolarda işlemi yapan kullanıcıyı takip etmek amacıyla aşağıdaki alanlar eklenecektir (nullable):
- `created_by` (İşlemi gerçekleştiren `users.id` değeri)
- `updated_by` (Güncellemeyi yapan `users.id` değeri)
- `deleted_by` (Kaydı silen `users.id` değeri)

---

## 4. Veri Tipleri Standardı

### Boolean
- Mantıksal durumlar için **Boolean** (`tinyint(1)`) tipi kullanılacaktır.
- Alan adları soru eki veya durum belirteci gibi isimlendirilmelidir.
  - *Örnek:* `is_active`, `is_approved`, `has_media`.

### Decimal & Sayısal
- Para veya hassas finansal veri içermeyen ama ondalıklı olması gereken diğer tüm sayısal alanlar (örn: not ortalamaları, puanlar) için **Decimal** veya **Float** tercih edilebilir.
- Sınav puanları vb. `decimal(5,2)` formatında saklanacaktır.

### Enum
- Sınırlı durumlar için veritabanı seviyesinde `enum` yerine, kod seviyesinde sarmalanmış **PHP Enums** ve veritabanında **String** (`varchar(50)`) tipi kullanılacaktır. Bu, SQLite ve MySQL arasında tam uyumluluk sağlar ve şema migrasyonu yapmadan yeni durumlar eklenmesine izin verir.

### Text
- Kısa metinler için `varchar(255)` (`string()`), uzun metinler veya açıklamalar için `text` (`text()`) kullanılacaktır.

---

## 5. İndeksleme ve Benzersizlik (Indexes & Uniques)
- **Yabancı Anahtarlar:** Tüm foreign key kolonlarında otomatik olarak indeks oluşturulmalıdır (Laravel `foreignId()->index()` veya `foreign()`).
- **Sorgulanan Alanlar:** `where` koşullarında sıkça kullanılacak kolonlar (örn: `is_active`, `status`, `registered_at`) indekslenecektir.
- **Benzersiz Kolonlar:** E-posta, TC Kimlik No, telefon numarası gibi benzersiz olması gereken alanlara veritabanı seviyesinde `unique` kısıtı eklenecektir.
