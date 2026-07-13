# Site Map (Site Haritası)

Bu doküman, Kurumsal Web Sitesi ve Yönetim Paneline ait tüm sayfaların hiyerarşik listesini içerir.

## 1. Kurumsal Web Sitesi Site Haritası (Version 1)
Ziyaretçilere açık olan dış arayüz sayfaları:

```text
├── Ana Sayfa (/)
├── Kurumsal (/kurumsal)
│   ├── Hakkımızda (/kurumsal/hakkimizda)
│   ├── Başarılarımız (/kurumsal/basarilarimiz)
│   └── SSS (/kurumsal/sss)
├── Programlar / Kurslar (/kurslar)
│   └── Kurs Detay (/kurslar/{course-slug})
├── Branşlar (/branslar)
├── Öğretmenler (/ogretmenler)
├── Galeri (/galeri)
├── İletişim (/iletisim)
├── Haberler / Blog (/blog)
│   └── Blog Detay (/blog/{blog-slug})
├── Etkinlikler (/etkinlikler)
├── Duyurular (/duyurular)
├── Online Ön Kayıt (/on-kayit)
├── Yasal Sayfalar
│   ├── KVKK Bilgilendirme (/yasal/kvkk)
│   ├── Çerez Politikası (/yasal/cerez-politikasi)
│   └── Gizlilik Politikası (/yasal/gizlilik-politikasi)
├── Hata Sayfaları
│   ├── 404 Bulunamadı
│   └── 500 Sunucu Hatası
```

---

## 2. Yönetim Paneli Site Haritası (Version 2 & 3)
Giriş yapmış yetkili kullanıcıların (Admin, Editör) erişebileceği yönetim sayfaları:

```text
├── Dashboard (/admin/dashboard)
├── Ön Kayıt Talepleri (/admin/leads)
│   ├── Liste
│   └── Ön Kayıt Detay/Düzenleme
├── İçerik Yönetimi (CMS)
│   ├── Sayfalar (/admin/pages)
│   │   ├── Liste / Düzenleme
│   │   └── Sayfa Ekle
│   ├── Slider Yönetimi (/admin/sliders)
│   ├── Blog Yönetimi (/admin/blogs)
│   │   ├── Yazı Listesi
│   │   └── Yeni Yazı Ekle
│   ├── Duyurular (/admin/announcements)
│   ├── Etkinlikler (/admin/events)
│   └── Galeri (/admin/gallery)
├── İletişim Mesajları (/admin/messages)
├── Eğitim Yönetimi (Version 2 & 3)
│   ├── Öğrenci Yönetimi (/admin/students)
│   │   ├── Öğrenci Listesi / Kartı
│   │   ├── Yeni Öğrenci Ekle
│   │   └── Profil Düzenle
│   ├── Veli Yönetimi (/admin/parents)
│   ├── Öğretmen Yönetimi (/admin/teachers)
│   ├── Sınıf/Derslik Yönetimi (/admin/classrooms)
│   ├── Kurs/Program Yönetimi (/admin/courses)
│   ├── Yoklama Sistemi (Version 3 ERP Only) (/admin/attendance)
│   ├── Ders Programı Takvimi (Version 3 ERP Only) (/admin/schedules)
│   └── Ödev Takip Modülü (Version 3 ERP Only) (/admin/homeworks)
├── Dosya & Medya Yönetimi
│   ├── Belgeler (/admin/documents)
│   └── Medya Kütüphanesi (/admin/media)
├── Sistem Yönetimi
│   ├── Kullanıcılar (/admin/users)
│   ├── Rol & Yetkiler (/admin/roles)
│   ├── Şubeler (/admin/branches)
│   ├── Menü Yönetimi (/admin/menus)
│   ├── Tema & Renk Ayarları (/admin/theme)
│   └── Genel Ayarlar (/admin/settings)
```
