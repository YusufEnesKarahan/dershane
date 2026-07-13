# User Flow (Kullanıcı Akışları)

Bu doküman, sistemdeki farklı kullanıcı rollerine ait temel işlem adımlarını ve akış şemalarını detaylandırır.

## 1. Ziyaretçi Kullanıcı Akışları (V1 - Web Sitesi)

### Rota: Ön Kayıt Başvurusu
```text
[Ziyaretçi] 
    │
    ▼
[Ana Sayfa] ──► (Kurs İnceleme) ──► [Kurs Detay Sayfası]
    │                                       │
    ├───────────────────────────────────────┘
    ▼
[Ön Kayıt Sayfası] 
    │
    ▼
[Formu Doldur] (Ad, Telefon, Kurs Tercihi, Şube Seçimi)
    │
    ▼
[Validasyon / Kontrol] ──(Hatalı)──► [Hata Mesajlarını Göster]
    │
    │ (Başarılı)
    ▼
[Veritabanına Kaydet (leads)] ──► [Başarılı Ön Kayıt Sayfası (Teşekkürler)]
```

---

## 2. Admin Kullanıcı Akışları (V2 & V3 - Yönetim & ERP)

### Rota: Aday Ön Kayıt Yönetimi
```text
[Yönetici] ──► [Giriş Sayfası] ──► [Dashboard]
                                        │
                                        ▼
                                 [Aday Talepleri] (Liste)
                                        │
                                        ├─► [Sil] ──► [Onay] ──► [Listeye Dön]
                                        │
                                        └─► [Detay İncele]
                                                │
                                                ▼
                                         [Durum Güncelle] (Bekliyor/Arandı/Kayıt Oldu)
```

---

## 3. Version 2 Modülleri Kullanıcı Akışları (CRUD)

Aşağıdaki tablolar, yönetim panelindeki temel veri yönetim akışlarını adım adım gösterir.

### A. Öğrenci Yönetimi
1. **Listeleme:** `admin/students` -> Öğrenci listesi, şube filtresi, aktiflik durumu.
2. **Detay:** `admin/students/{id}` -> Öğrenci kimlik bilgileri, veli bilgisi, kayıtlı olduğu kurslar.
3. **Oluşturma:** `admin/students/create` -> Kişisel bilgiler formu, veli eşleme açılır listesi, kurs kaydı seçimi.
4. **Düzenleme:** `admin/students/{id}/edit` -> Bilgi güncelleme formu.
5. **Silme:** `admin/students/{id}` -> Soft Delete tetikleme, durumunu pasife çekme.

### B. Öğretmen Yönetimi
1. **Listeleme:** `admin/teachers` -> Kurumdaki öğretmenler listesi.
2. **Detay:** `admin/teachers/{id}` -> Öğretmenin girdiği sınıflar, branşları, iletişim bilgileri.
3. **Oluşturma:** `admin/teachers/create` -> Öğretmen kayıt formu (Branş, TC, e-posta, şube).
4. **Düzenleme:** `admin/teachers/{id}/edit` -> Bilgi güncelleme.
5. **Silme:** `admin/teachers/{id}` -> Soft Delete.

### C. Sınıf & Derslik Yönetimi
1. **Listeleme:** `admin/classrooms` -> Dersliklerin listesi ve kapasiteleri.
2. **Detay:** Yok. Sınıflar liste üzerinden yönetilir.
3. **Oluşturma:** `admin/classrooms/create` -> Derslik adı (örn: A-102), şube ve kapasite seçimi.
4. **Düzenleme:** `admin/classrooms/{id}/edit` -> Kapasite veya isim güncelleme.
5. **Silme:** `admin/classrooms/{id}` -> Silme işlemi (sınıfta aktif ders programı varsa engellenir).

### D. Kurs & Program Yönetimi
1. **Listeleme:** `admin/courses` -> Aktif eğitim paketleri listesi.
2. **Detay:** `admin/courses/{id}` -> Kurs içeriğindeki dersler, bu kursa kayıtlı öğrenciler listesi.
3. **Oluşturma:** `admin/courses/create` -> Kurs adı, şube seçimi, ders eşlemeleri.
4. **Düzenleme:** `admin/courses/{id}/edit` -> Ders ekleme/çıkarma, isim güncelleme.
5. **Silme:** `admin/courses/{id}` -> Soft Delete.

### E. Ders Programı (V3)
1. **Listeleme/Takvim:** `admin/schedules` -> Haftalık takvim görünümü.
2. **Oluşturma/Eşleme:** Gün, saat, ders, öğretmen ve derslik seçilerek takvime eklenir. Conflict (çakışma) kontrolü yapılır.

### F. Yoklama (V3)
1. **Listeleme:** Tarih ve ders programı seçilerek ilgili sınıfın öğrenci listesi dökülür.
2. **Oluşturma/Kayıt:** Öğrencilerin yanına "Katıldı/Katılmadı/İzinli" işaretlenerek tek seferde kaydedilir.

### G. Ödev Modülü (V3)
1. **Oluşturma:** Öğretmen tarafından sınıf seçilir, ödev başlığı, konusu, teslim tarihi ve varsa döküman eklenir.
2. **Değerlendirme:** Öğrenci teslimleri listelenir, öğretmen puan ve yorum ekler.

### H. Belge & Doküman Yönetimi (V2 & V3)
1. **Oluşturma:** PDF, Word dosyası polimorfik olarak ilgili öğrenci, öğretmen veya kurs kaydına yüklenir.
