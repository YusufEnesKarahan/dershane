# Wireframes (Tel Kafes Tasarımları)

Bu doküman, sistemdeki temel sayfaların yerleşim planlarını ASCII formatında gösterir.

## 1. Ana Sayfa (Home Page)
```text
+-----------------------------------------------------------------------+
|  [Logo]   Programlar   Branşlar   Öğretmenler     [Ön Kayıt Ol (CTA)]  |  <-- Navbar
+-----------------------------------------------------------------------+
|                                                                       |
|               GELECEĞİN EĞİTİM PLATFORMU (H1)                         |
|    Modern sınıflar ve birebir rehberlik ile başarıya ulaşın.          |  <-- Hero Section
|                                                                       |
|             [Programları İncele]   [Ön Kayıt Yap]                     |
|                                                                       |
+-----------------------------------------------------------------------+
| Neden Biz? (3 Sütunlu Özellikler)                                     |
| +-------------------+  +-------------------+  +-------------------+   |
| | [İkon]            |  | [İkon]            |  | [İkon]            |   |
| | Birebir Rehberlik |  | Uzman Kadro       |  | Butik Sınıflar    |   |
| +-------------------+  +-------------------+  +-------------------+   |
+-----------------------------------------------------------------------+
| Popüler Programlarımız                                                |
| +-------------------+  +-------------------+  +-------------------+   |
| | [Görsel]          |  | [Görsel]          |  | [Görsel]          |   |
| | TYT-AYT Hazırlık  |  | LGS Hazırlık      |  | İngilizce Kursu   |   |
| | [İncele]          |  | [İncele]          |  | [İncele]          |   |
| +-------------------+  +-------------------+  +-------------------+   |
+-----------------------------------------------------------------------+
| © 2026 Eğitim Kurumu SaaS.  KVKK - Gizlilik - Çerez                   |  <-- Footer
+-----------------------------------------------------------------------+
```

---

## 2. Kurs Detay Sayfası (Course Details)
```text
+-----------------------------------------------------------------------+
|  [Logo]   Programlar   Branşlar   Öğretmenler     [Ön Kayıt Ol (CTA)]  |
+-----------------------------------------------------------------------+
|  Home > Kurslar > TYT Hazırlık Kursu                                  |  <-- Breadcrumbs
+-----------------------------------------------------------------------+
|  TYT HAZIRLIK KURSU (H1)                                              |
|  Üniversite sınavlarına hazırlıkta en doğru adres.                    |
+-----------------------------------------------------------------------+
|  +-------------------------------------+   +-----------------------+  |
|  | [Görsel / Video Tanıtım]            |   | Ön Kayıt Başvurusu    |  |
|  |                                     |   |                       |  |
|  | Kurs İçeriği:                       |   | Ad Soyad: [        ]  |  |
|  | - Matematik (Haftalık 6 Saat)       |   | Telefon:  [        ]  |  |
|  | - Türkçe (Haftalık 4 Saat)          |   |                       |  |
|  | - Fizik/Kimya (Haftalık 4 Saat)     |   | [KAYDI TAMAMLA (CTA)] |  |  <-- Sticky Box
|  +-------------------------------------+   +-----------------------+  |
+-----------------------------------------------------------------------+
```

---

## 3. Online Ön Kayıt Sayfası (Pre-Registration)
```text
+-----------------------------------------------------------------------+
|  [Logo]   Programlar   Branşlar   Öğretmenler     [Ön Kayıt Ol (CTA)]  |
+-----------------------------------------------------------------------+
|                                                                       |
|                      ONLINE ÖN KAYIT FORMU (H1)                        |
|                                                                       |
|               +---------------------------------------+               |
|               | Adı Soyadı:   [                     ] |               |
|               | Telefon:      [                     ] |               |
|               | E-posta:      [                     ] |               |
|               | Tercih Kurs:  [ Seçiniz           \/] |               |
|               | Tercih Şube:  [ Seçiniz           \/] |               |
|               |                                       |               |
|               | [ ] KVKK Metnini onaylıyorum.         |               |
|               |                                       |               |
|               |          [BAŞVURUYU GÖNDER (CTA)]     |               |
|               +---------------------------------------+               |
|                                                                       |
+-----------------------------------------------------------------------+
```

---

## 4. Admin Dashboard Sayfası
```text
+-----------------------------------------------------------------------+
| [Dershane Admin] | Şube: [Kadıköy \/]                   Kullanıcı [v] |
+-----------------------------------------------------------------------+
| (Sidebar)  |  Ana Sayfa / Dashboard                                    |  <-- Breadcrumbs
|            | +------------------------------------------------------+ |
| Dashboard  | | İstatistik Özetleri                                  | |
|            | | Toplam Öğrenci: 152   Aday Kayıtları: 12 (Yeni)      | |
| Öğrenciler | +------------------------------------------------------+ |
| Öğretmenler| | Son Ön Kayıt Başvuruları                             | |
| Sınıflar   | | 1. Ahmet Yılmaz - TYT Kursu (Arandı)                 | |
| Kurslar    | | 2. Elif Kaya   - LGS Kursu (Bekliyor)                | |
| İçerik(CMS)| +------------------------------------------------------+ |
| Ayarlar    | | Hızlı İşlemler                                       | |
|            | | [Yeni Öğrenci Ekle]    [Yoklama Al (V3)]             | |
+------------+----------------------------------------------------------+
```
