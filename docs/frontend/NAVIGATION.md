# Navigation Structure (Gezinti Yapısı)

Bu doküman, site içi yönlendirme elemanlarının (Navbar, Sidebar, Footer, Breadcrumbs vb.) yerleşim kurallarını ve yapılarını tanımlar.

## 1. Üst Navigasyon (Header / Navbar)
Kurumsal web sitesinde yer alan ve tüm sayfalarda ortak olan üst bar yapısıdır.

- **Konum:** Sticky (sayfa kaydırıldığında üstte sabitlenir) ve yarı saydam (`backdrop-blur-sm`).
- **Elemanlar (Soldan Sağa):**
  - Kurumsal Logo veya Metin Başlığı (`brand_setting('name')`).
  - Birincil Linkler: Programlar, Branşlar, Öğretmenler, Duyurular, Galeri, İletişim.
  - Sağ Alan: "Giriş Yap" (Göst) & "Ön Kayıt Ol" (Özel Buton).
- **Mobil Menü:** Hamburger butonu tıklandığında dikey olarak açılan, ekranı kaplamayan AlpineJS destekli çekmece (drawer) yapısı.

---

## 2. Yönetim Paneli Navigasyonu (Sidebar)
Yönetim panelindeki sol gezinti barı.

- **Genişlik:** `w-64` (`256px`). Mobil cihazlarda gizlenir ve hamburger ile açılır.
- **Gruplama Hiyerarşisi:**
  - **Genel:** Dashboard, İletişim Mesajları, Ön Kayıt Talepleri.
  - **İçerik Yönetimi (CMS):** Sayfalar, Sliders, Haberler/Blog, Duyurular, Etkinlikler, Galeri.
  - **Eğitim Yönetimi (ERP):** Öğrenciler, Veliler, Öğretmenler, Sınıflar, Kurs Paketleri, Yoklama (V3), Ders Programı (V3), Ödev (V3).
  - **Sistem:** Kullanıcılar, Rol/Yetki, Medya, Belgeler, Ayarlar.
- **Aktif Durum:** Geçerli sayfada olan menü elemanı `active_menu()` helper'ı aracılığıyla birincil renge boyanır.

---

## 3. Alt Navigasyon (Footer)
Web sitesinin en altında yer alan ve genel harita görevi gören alan.

- **Kolon 1 (Marka):** Logo, kısa açıklama metni ve telif hakkı.
- **Kolon 2 (Kurumsal):** Hakkımızda, Başarılarımız, SSS linkleri.
- **Kolon 3 (İletişim):** Telefon, E-posta, Adres ve Harita linki.
- **Kolon 4 (Sosyal Medya):** Instagram, Facebook, Twitter ikon ve linkleri.

---

## 4. Ekmek Kırıntıları (Breadcrumbs)
Kullanıcının site hiyerarşisinde nerede olduğunu gösteren navigasyon yardımı.

- **Kullanım:** Tüm iç sayfalarda ve admin alt sayfalarında başlığın hemen üzerinde yer alır.
- **Format:** `Ana Sayfa > [Üst Kategori] > [Mevcut Sayfa]`
