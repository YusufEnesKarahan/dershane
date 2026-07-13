# Media Guide (Görsel ve Medya Kılavuzu)

Bu kılavuz, arayüz kalitesini ve sayfa hızını korumak için sisteme yüklenecek görsellerin standart boyutlarını ve sıkıştırma kurallarını tanımlar.

## 1. Görsel Boyut Standartları (Dimensions Grid)

Aşağıdaki tabloda, sistemdeki farklı alanlarda kullanılacak görsellerin en-boy oranları (aspect ratio) ve ideal çözünürlükleri yer almaktadır:

| Medya Türü | En-Boy Oranı (Aspect) | İdeal Çözünürlük | Maksimum Dosya Boyutu | Format Kuralı |
| :--- | :---: | :--- | :--- | :--- |
| **Slider (Hero)** | 16:9 / 21:9 | 1920 x 800 px | 250 KB | WebP / Progressive JPG |
| **Blog Kapak** | 16:9 | 1200 x 675 px | 150 KB | WebP |
| **Kurs Kapak** | 4:3 | 800 x 600 px | 100 KB | WebP |
| **Öğretmen Avatar**| 1:1 (Square) | 400 x 400 px | 50 KB | WebP / PNG (Transparan) |
| **Galeri Fotoğraf**| 3:2 / 16:9 | 1600 x 1000 px | 300 KB | WebP / JPG |
| **Kurumsal Logo** | Serbest | Maksimum 300x80 px | 20 KB | SVG |
| **FavIcon** | 1:1 | 32x32 / 48x48 px | 5 KB | ICO / PNG |

---

## 2. Format ve Sıkıştırma Kuralları
- **WebP Önceliği:** Tüm dinamik görseller (Blog, galeri, kurs kapakları) sunucuya yüklendiğinde otomatik olarak **WebP** formatına dönüştürülecektir. Bu, görsel kalitesini bozmadan %30 ila %50 oranında dosya boyutundan tasarruf sağlar.
- **SVG Tercihi:** Logo, ikonlar ve grafik çizimleri için her zaman çözünürlük kaybı yaşamayan **SVG** formatı tercih edilmelidir.
- **Kayıpsız Sıkıştırma:** Web sunucusuna yüklenen görseller arka planda `spatie/laravel-image-optimizer` gibi bir paket aracılığıyla optimize edilmelidir.

---

## 3. Duyarlı (Responsive) Görsel Kullanımı
- Blade şablonlarında görseller yüklenirken `loading="lazy"` niteliği eklenerek "lazy-loading" (tembel yükleme) yapılacaktır.
- Hero ve Slider görselleri sayfa açılışında LCP (Largest Contentful Paint) değerini geciktirmemesi için `loading="lazy"` yapılmayacak, aksine pre-load edilecektir.
