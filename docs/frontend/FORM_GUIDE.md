# Form Strategy & Guide (Form Stratejisi ve Rehber)

Bu doküman, sistem genelinde kullanılan formların (İletişim, Ön Kayıt, Yorum vb.) girdi alanlarını, doğrulama (validation) kurallarını ve hata/başarı durumlarını tanımlar.

## 1. Online Ön Kayıt Başvuru Formu (`Pre-Registration`)
Aday öğrencilerin veya velilerin web sitesi üzerinden başvuru yapması için kullanılan formdur.

- **student_name:** string (Öğrenci Adı Soyadı). Zorunlu. Maksimum 100 karakter.
- **phone:** string (Veli Telefonu). Zorunlu. Türkiye telefon formatında (`regex:/^[0-9]{10}$/`).
- **email:** string (E-posta). İsteğe bağlı. Geçerli email formatında (`email`).
- **course_id:** unsigned integer (Talep Edilen Kurs). Zorunlu. `courses` tablosunda var olmalıdır (`exists`).
- **branch_id:** unsigned integer (Tercih Edilen Şube). Zorunlu. `branches` tablosunda var olmalıdır (`exists`).
- **notes:** string (Ek Notlar/Mesaj). İsteğe bağlı. Maksimum 500 karakter.
- **kvkk_consent:** boolean (KVKK Onayı). Zorunlu. Sadece `true` kabul edilir (`accepted`).

---

## 2. İletişim Formu (`Contact Form`)
Ziyaretçilerin genel mesajlar gönderebileceği iletişim formu.

- **name:** string (Adı Soyadı). Zorunlu. En az 3, en çok 100 karakter.
- **phone:** string (Telefon). İsteğe bağlı. Türkiye telefon formatında.
- **email:** string (E-posta). Zorunlu. Geçerli email formatı.
- **subject:** string (Konu). Zorunlu. Maksimum 150 karakter.
- **message:** string (Mesaj). Zorunlu. En az 10, en çok 1000 karakter.

---

## 3. Blog Yorum Formu (`Blog Comment Form`)
Blog yazılarına yorum yapabilmek için kullanılan form (V1).

- **name:** string (Yorum Yapan Adı). Zorunlu. Maksimum 50 karakter.
- **email:** string (E-posta). Zorunlu.
- **comment:** string (Yorum Metni). Zorunlu. Maksimum 300 karakter.

---

## 4. Yönetim Paneli Arama ve Filtreleme Formları
Yönetim listelerinde veri süzmek için kullanılan form yapıları.

- **Search (Arama Girişi):** `q` (Query string). İsteğe bağlı. Maksimum 50 karakter. Özel karakterlerden temizlenir.
- **Filters (Filtreler):**
  - `branch_id`: Şubeye göre süzme.
  - `status`: Aktif/Pasif durumuna göre süzme.
  - `date_range`: Tarih aralığı (Başlangıç ve Bitiş tarihleri).

---

## 5. Doğrulama (Validation) Davranış Standartları
- **İstemci Tarafı (Client-Side):** HTML5 validasyonları (`required`, `type="email"`, `maxlength`) tarayıcı seviyesinde ilk kontrolü yapar.
- **Sunucu Tarafı (Server-Side):** Laravel Request Validation kuralları çalışır. Hata durumunda sayfa yenilenmeden hata durumları ilgili `<x-input>` bileşenine enjekte edilerek gösterilir.
- **Hata Mesajları:** Her zaman Türkçe ve anlaşılır olmalıdır (örn: *Telefon alanı geçerli bir telefon numarası olmalıdır.*).
