# Page Blueprint (Sayfa Taslakları Hiyerarşisi)

Bu doküman, sitedeki sayfaların tasarım iskeletlerini, grid yerleşimlerini ve kapsayıcı (wrapper) yapısını tanımlar.

## 1. Web Sitesi Sayfa Şablonu (Frontend Layout Blueprint)
Dış dünyaya açık kurumsal sayfalarda uygulanacak dikey hiyerarşi:

```text
+------------------------------------------------------------------+
| NAVBAR: Sticky, h-16, max-w-7xl, logo (left), nav links (center), |
|         CTA Button (right).                                      |
+------------------------------------------------------------------+
| HERO / SECTION HEADER (İç sayfalarda): py-12, bg-neutral-900     |
|   Breadcrumbs (xs text, text-neutral-300)                        |
|   H1 Page Title (font-display, font-bold, text-white)            |
+------------------------------------------------------------------+
| MAIN CONTAINER: py-12, max-w-7xl, mx-auto, px-4                  |
|                                                                  |
|   [Grid 1 Sütun (Detay sayfaları) veya Grid 3 Sütun (Listeler)]  |
|                                                                  |
+------------------------------------------------------------------+
| FOOTER: py-12, bg-white, border-t, border-neutral-100            |
+------------------------------------------------------------------+
```

---

## 2. Yönetim Paneli Sayfa Şablonu (Admin Layout Blueprint)
İç panellerde kullanılan iki sütunlu (Sidebar + Content) yerleşim:

```text
+------------------------------------------------------------------+
| SIDEBAR (w-64, bg-slate-900)   | HEADER (h-16, bg-white, border) |
|                                |   Breadcrumbs / Page Title      |
| Logo & Brand                   |   User Menu / Profile (Right)   |
| ----------------------------   +---------------------------------+
| Navigation Items               | MAIN CONTENT AREA:              |
| - Dashboard                    |   py-6, px-6, bg-neutral-50     |
| - Students                     |                                 |
| - Teachers                     |   [Filtre Paneli (Card)]        |
| - Settings                     |                                 |
|                                |   [Veri Tablosu (Card / Table)] |
|                                |                                 |
|                                |   [Sayfalama (Pagination)]      |
+--------------------------------+---------------------------------+
```

---

## 3. İçerik Grid Yapısı Kuralları
- **3 Sütunlu Grid (Blog listeleri, Kurs listeleri vb.):**
  `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6` (Mobilde tek sütun, notebookta iki, masaüstünde 3 sütun olur).
- **Yönetici Detay/Profil Ekranları:**
  `grid grid-cols-1 lg:grid-cols-3 gap-8` (Sol alanda 2 sütun genişliğinde ana bilgiler, sağ alanda 1 sütun genişliğinde işlem/statü paneli).
