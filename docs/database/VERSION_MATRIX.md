# Version Matrix (Sürüm Paket Matrisi)

Bu tablo, sistemdeki varlıkların (entities) hangi ticari paket sürümlerinde (Version 1, Version 2, Version 3) aktif olarak kullanılacağını gösterir.

| Entity | V1: Kurumsal Web Sitesi | V2: Kurumsal + Yönetim | V3: Tam Yönetim (ERP) | Açıklama |
| :--- | :---: | :---: | :---: | :--- |
| **Branch** | ✔ | ✔ | ✔ | Tüm sürümlerde şube yapısı desteklenir. |
| **Page** | ✔ | ✔ | ✔ | Kurumsal sayfalar (Hakkımızda vb.) |
| **Slider** | ✔ | ✔ | ✔ | Web sitesi görselleri |
| **Blog & Category** | ✔ | ✔ | ✔ | Haberler ve duyuru yazıları |
| **Gallery** | ✔ | ✔ | ✔ | Kurum albümleri |
| **Event** | ✔ | ✔ | ✔ | Web takvimi ve etkinlikler |
| **Announcement** | ✔ | ✔ | ✔ | Genel web duyuruları |
| **ContactMessage**| ✔ | ✔ | ✔ | İletişim formu mesajları |
| **Lead** | ✔ | ✔ | ✔ | Aday öğrenci ön kayıt başvuruları |
| **User** | ✖ | ✔ | ✔ | Yönetim girişli kullanıcılar |
| **Role & Permission**| ✖ | ✔ | ✔ | Yönetici ve personel yetki yapısı |
| **Student** | ✖ | ✔ | ✔ | Kurumun kayıtlı öğrencileri |
| **Parent** | ✖ | ✔ | ✔ | Veli bilgileri ve profilleri |
| **Teacher** | ✖ | ✔ | ✔ | Öğretmen profilleri |
| **Classroom** | ✖ | ✔ | ✔ | Sınıflar ve derslikler |
| **Course** | ✖ | ✔ | ✔ | Kurs ve eğitim programları paketleri |
| **Lesson** | ✖ | ✔ | ✔ | Ders tanımlamaları (Matematik, Türkçe vb.) |
| **Registration** | ✖ | ✔ | ✔ | Kurum içi resmi öğrenci kayıtları |
| **LessonSchedule**| ✖ | ✖ | ✔ | Haftalık ders saat planlama takvimi |
| **Attendance** | ✖ | ✖ | ✔ | Günlük ders yoklamaları |
| **Homework** | ✖ | ✖ | ✔ | Ödev verme ve takip ekranları |
| **HomeworkSubmission**| ✖ | ✖ | ✔ | Ödev teslim etme ve notlandırma |
| **Setting** | ✔ | ✔ | ✔ | Temel sistem ayarları |
| **ActivityLog** | ✖ | ✔ | ✔ | Denetim ve audit log kayıtları |
| **Notification** | ✖ | ✔ | ✔ | Sistem içi bildirimler |
| **Media & Document**| ✔ | ✔ | ✔ | Polimorfik dosya yüklemeleri |

## Notlar
- **V1 (Kurumsal Web Sitesi):** Öğrenci, öğretmen veya ders planı gibi kurumsal arka-ofis yönetim varlıklarını kapsamaz. Yalnızca tanıtım, iletişim, blog ve potansiyel müşteri (`Lead`) kayıtlarını içerir.
- **V2 (Kurumsal + Yönetim):** Temel olarak öğrenci, veli, öğretmen, derslik ve kurs paketlerinin yönetimine izin verir. Ancak ders programı planlama (`LessonSchedule`) veya yoklama (`Attendance`) alma yeteneklerini içermez.
- **V3 (Tam Yönetim - ERP):** Yoklama, haftalık ders planlama takvimleri, ödevler ve teslim değerlendirmeleri gibi tüm ERP işlevlerini aktif hale getirir.
